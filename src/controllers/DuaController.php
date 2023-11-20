<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\controllers;

use Craft;
use craft\web\Controller;
use orbitalflight\deleteassets\Plugin;

class DuaController extends Controller {

    /**
     * actionDelete
     * Will request the current volume to be scanned and for unused assets to be deleted
     *
     * @return void
     */
    public function actionDelete() {
        $this->requireCpRequest();
        $this->requirePostRequest();

        $volumeId = Craft::$app->getRequest()->getRequiredBodyParam('volume-id');

        $this->requirePermission('dua-' . $volumeId);

        $deleteRevisions = (bool) Craft::$app->getRequest()->getBodyParam('delete-revisions');
        $deleteAssets = Plugin::getInstance()->services->scan($volumeId, true, $deleteRevisions);

        if ($deleteAssets === 0) {
            $this->setFailFlash(Craft::t('delete-assets', "No assets were deleted..."));
        } else if (!$deleteAssets) {
            return $this->setFailFlash(Craft::t('delete-assets', "An error occurred!"));
        } else if ($deleteAssets === 1) {
            return $this->setSuccessFlash(Craft::t('delete-assets', "Succesfully deleted 1 asset."));
        } else {
            return $this->setSuccessFlash(Craft::t('delete-assets', "Succesfully deleted {0} asset(s).", [$deleteAssets]));
        }
    }
    
    /**
     * actionScan
     * Will request the current volume to be scanned
     *
     * @return void
     */
    public function actionScan() {
        $this->requireCpRequest();
        $this->requirePostRequest();

        $volumeId = Craft::$app->getRequest()->getRequiredBodyParam('volume-id');
        
        if (Plugin::getInstance()->services->scan($volumeId)) {
            $this->setSuccessFlash(Craft::t('delete-assets', "Scan completed!"));
            return $this->renderTemplate('delete-assets', ['scannedVolumeId' => $volumeId]);
        } else {
            return $this->setFailFlash(Craft::t('delete-assets', "An error occurred with the scan."));
        }
    }

    /**
     * actionScanEverything
     * Will request for a scan of each volumes
     *
     * @return void
     */
    public function actionScanAll() {
        $this->requireCpRequest();
        $this->requirePostRequest();
    
        Plugin::getInstance()->services->scanAll();
        return $this->setSuccessFlash(Craft::t('delete-assets', "Scan completed!"));
    }
}