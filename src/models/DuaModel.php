<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\models;

use craft\base\Model;

class DuaModel extends Model {
    // == Properties --------------------------------
    public int $volumeId; // The volume id for which those data stand
    public int $totalAssets; // Total assets 
    public int $usedAssets; // Total used assets 
    public int $revisionAssets; // Total assets used only in revisions and drafts 
    public int $unusedAssets; // Total unused assets 
    public string $usedAssetsSize; // Size of the used assets
    public string $revisionAssetsSize; // Size of the revisions and drafts assets
    public string $unusedAssetsSize; // Size of the unused assets
    public int $deletableAssetsRatio; // % of deletable assets / total assets
    public int $revisionAssetsRatio; // % of revision and draft assets / deletable assets
    public int $unusedAssetsRatio; // % of unused assets / deletable assets

    // == Methods --------------------------------
    public function __toString() {
        return (string) $this->totalAssets;
    }

    // == Rules --------------------------------
    public function rules(): array {
        return [
            [['volumeId', 'totalAssets', 'usedAssets', 'revisionAssets', 'unusedAssets', 'usedAssetsSize', 'revisionAssetsSize', 'unusedAssetsSize', 'deletableAssetsRatio', 'revisionAssetsRatio', 'unusedAssetsRatio'], 'required'],
            [['deletableAssetsRatio', 'revisionAssetsRatio', 'unusedAssetsRatio'], 'in', 'range' => range(0, 100)],
        ];
    }
}
