<?php

/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Asset;
use craft\helpers\ElementHelper;
use orbitalflight\deleteassets\Plugin;
use orbitalflight\deleteassets\models\DuaModel;
use orbitalflight\deleteassets\records\DuaRecord;
use yii\web\NotFoundHttpException;

class DuaService extends Component {

    /**
     * _checkVolume
     * Throws an error if the volume does not exist
     *
     * @param  mixed $volumeId
     * @return void
     */
    private function _checkVolume($volumeId) {
        if (!in_array($volumeId, Craft::$app->getVolumes()->allVolumeIds)) {
            throw new NotFoundHttpException(Craft::t('delete-assets', "Volume not found"));
        }
    }

    /**
     * getBadgeCount
     * Return the number of assets that could be deleted for the cp badge count.
     * Also includes the number of draft/revision assets (if enabled in settings)
     *
     * @return int
     */
    public function getBadgeCount(): int {
        $i = 0;
        
        $i += $this->getUnusedAssets();

        // Also add the number of draft/revision assets (if enabled in settings)
        if (Plugin::getInstance()->getSettings()->badgeIncludeDrafts) {
            $i += $this->getRevisionAssets();
        }

        return $i;
    }

    /**
     * getDuaModel
     * Requests the results of a previous scan for a given volume and fills data in a new model
     *
     * @param  mixed $id
     * @return DuaModel
     */
    public function getDuaModel($id): ?DuaModel {

        // Check for a previous scan of the volume
        $scanData = DuaRecord::findOne(['volumeId' => $id]);

        if (!$scanData) {
            return null;
        } else {
            // Set up a new model filled with the scan data
            $duaModel = new DuaModel();
            $scanAttributes = $scanData->getAttributes();
            $duaModel->setAttributes($scanAttributes);

            // Add formatted sizes
            $duaModel->usedAssetsSize = Plugin::getInstance()->helpers->prettySize($scanData->usedAssetsSize);
            $duaModel->revisionAssetsSize = Plugin::getInstance()->helpers->prettySize($scanData->revisionAssetsSize);
            $duaModel->unusedAssetsSize = Plugin::getInstance()->helpers->prettySize($scanData->unusedAssetsSize);

            // Add ratio calculations
            $deletableAssets = $scanData->revisionAssets + $scanData->unusedAssets;
            $duaModel->deletableAssetsRatio = Plugin::getInstance()->helpers->getRatio($deletableAssets, $scanData->totalAssets);
            $duaModel->revisionAssetsRatio = Plugin::getInstance()->helpers->getRatio($scanData->revisionAssets, $deletableAssets);
            $duaModel->unusedAssetsRatio = Plugin::getInstance()->helpers->getRatio($scanData->unusedAssets, $deletableAssets);

            // Add scan age
            $duaModel->age = Plugin::getInstance()->helpers->getAge($scanData->dateUpdated);

            $duaModel->validate();

            if ($duaModel->getErrors()) {
                return null;
            }

            return $duaModel;
        }

        return null;
    }

    /**
     * getRevisionAssets
     * Returns the total number of revision assets.
     *
     * @return int
     */
    public function getRevisionAssets(): int {
        $i = 0;

        $allRecords = DuaRecord::find()->all();
        foreach ($allRecords as $record) {
            $i += $record->revisionAssets;
        }

        return $i;
    }
    
    /**
     * getPermissionVolumes
     * Returns a permissions scheme filled with the system volumes
     *
     * @return array
     */
    public function getPermissionVolumes(): array {
        $permissions = [];

        // Get all volumes
        $volumes = Craft::$app->getVolumes()->getAllVolumes();

        // Assign permissions for each volume
        foreach ($volumes as $volume) {
            $permissions['dua-' . $volume->id] = ['label' => $volume->name];
        }

        return $permissions;
    }

    /**
     * getUnusedAssets
     * Returns the total number of unused assets.
     *
     * @return int
     */
    public function getUnusedAssets(): int {
        $i = 0;

        $allRecords = DuaRecord::find()->all();
        foreach ($allRecords as $record) {
            $i += $record->unusedAssets;
        }

        return $i;
    }

    /**
     * getUsedAssets
     * Returns the total number of used assets.
     *
     * @return int
     */
    public function getUsedAssets(): int {
        $i = 0;

        $allRecords = DuaRecord::find()->all();
        foreach ($allRecords as $record) {
            $i += $record->usedAssets;
        }

        return $i;
    }
        
    /**
     * markVolumeAsOutdated
     *
     * @param  mixed $volumeId
     * @return void
     */
    public function markVolumeAsOutdated(int $volumeId): void {
        $record = DuaRecord::find()->where(['volumeID' => $volumeId])->one();

        if (!$record) {
            // No record = not scanned yet, we can do it now (if autoscan is enabled)
            if (Plugin::getInstance()->getSettings()->autoscan) {
                $this->scan($volumeId, false, false);
            }
            return;
        } else {
            $record->outdated = true;
            $record->save();
        }
    }

    /**
     * scan
     * Scans the selected volume, and performs the following actions:
     * Default: Gathers data and request them to be proccessed and stored in the database (will do it in any scenario)
     * Delete: Delete unused assets
     * DeleteRevisions: Delete unused assts AND assets used in revisions
     * 
     * Returns true after a successful scan, and the number of deleted assets if deletion was requested
     *  
     * @param  mixed $volumeId
     * @param  mixed $delete
     * @param  mixed $deleteRevision
     * @return bool|int
     */
    public function scan(int $volumeId, bool $delete = false, bool $deleteRevisions = false): bool|int {
        $this->_checkVolume($volumeId);

        // Get all assets from the selected volume and parse them
        $assets = Asset::find()->volumeId($volumeId)->all();

        // Variables
        $scanData = [
            'volumeId' => $volumeId,
            'totalAssets' => count($assets),
            'usedAssets' => 0,
            'revisionAssets' => 0,
            'unusedAssets' => 0,
            'usedAssetsSize' => 0,
            'revisionAssetsSize' => 0,
            'unusedAssetsSize' => 0,
        ];
        $i = 0; // Deleted assets

        // == Data processing =====
        // A) Parse the assets
        // B) If no relation is found, it's an unused asset (type 3)
        // C) If relations are found, parse them and fetch its related Element
        // D) If the related element is not a draft or a revision, the Asset is used. We can skip further verifications (type 1)
        // E) If all relations were drafts or revisions, the Asset is a "revision Asset" (type 2)
        // #) At any time, if the asset should be deleted by user request, mark it for deletion (type 4)
        // =========================

        // A) Parse the assets
        foreach ($assets as $currentAsset) {
            $relationType = 0;
            $query = new Query();
            $relations = $query->select(['sourceId'])
                ->from(Table::RELATIONS)
                ->where(['targetId' => $currentAsset->id])
                ->all();

            // B) If no relation is found, it's an unused asset (type 3)
            if (empty($relations)) {
                $relationType = $delete ? 4 : 3;
            } else {
                // C) If relations are found, parse them and fetch its related Element
                $ghostRelations = 0;
                foreach ($relations as $currentRelation) {
                    if ($relationType != 1) { // Will skip if asset is already found to be "used"
                        $relatedElement = Craft::$app->elements->getElementById($currentRelation['sourceId']);
                        if (!$relatedElement) {
                            // Relation exists but the element does not anymore, it's a ghost relation
                            $ghostRelations++;
                            Craft::info('[DUA] – Relation between Asset ' . $currentAsset->id . ' and Element ' . $currentRelation['sourceId'] . ' exists but Element is null.');
                        } elseif (!ElementHelper::isDraftOrRevision($relatedElement)) {
                            // D) If the related element is not a draft or a revision, the Asset is used. We can skip further verifications (type 1)
                            $relationType = 1;
                        }
                    }
                }

                // E) If no relation turned with a case 1 (Used)...
                if ($relationType != 1) {
                    if ($ghostRelations == count($relations)) {
                        // All relations were registered but linked to nothing, hence it's a "Unused Asset" (type 3)
                        $relationType = $delete ? 4 : 3;
                    } else {
                        // At least one remaining relation was a draft or a revision, the Asset is a "revision Asset" (type 2)
                        $relationType = ($delete && $deleteRevisions) ? 4 : 2;
                    }
                }
            }

            // Store the data
            switch ($relationType) {
                case 1:
                    $scanData['usedAssets']++;
                    $scanData['usedAssetsSize'] += $currentAsset->size;
                    break;

                case 2:
                    $scanData['revisionAssets']++;
                    $scanData['revisionAssetsSize'] += $currentAsset->size;
                    break;

                case 3:
                    $scanData['unusedAssets']++;
                    $scanData['unusedAssetsSize'] += $currentAsset->size;
                    break;

                case 4:
                    Craft::$app->elements->deleteElement($currentAsset);
                    $i++;
                    break;

                default:
                    Craft::warning("[DUA] Unknown error with asset " . $currentAsset->getId(), __METHOD__);
            }
        }

        // Re-count the number of assets in the current volume
        $scanData['totalAssets'] = Asset::find()->volumeId($volumeId)->count();

        // Save the data in the database
        // Find the former data and update it if it exists
        $scanRecord = DuaRecord::find()->where(['volumeId' => $volumeId])->one();
        
        // If it doesn't, create a new record
        if (!$scanRecord) {
            $scanRecord = new DuaRecord();
        }
        
        $scanRecord->attributes = $scanData;
        $scanRecord->outdated = false;
        $scanRecord->save();

        return $delete ? $i : true;
    }

    /**
     * scanAll
     * Scans all volumes
     *
     * @return void
     */
    public function scanAll() {
        $volumesId = Craft::$app->getVolumes()->allVolumeIds;
        foreach ($volumesId as $id) { // * Could be potentially turned into a queue in a future update
            $this->scan($id);
        }
    }
}
