<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\records;

use craft\db\ActiveRecord;

class DuaRecord extends ActiveRecord {
    public static function tableName(): string {
        return "{{%delete-unused-assets}}";
    }

    // == Rules --------------------------------
    public function rules(): array {
        return [
            [['volumeId', 'totalAssets', 'usedAssets', 'revisionAssets', 'unusedAssets', 'usedAssetsSize', 'revisionAssetsSize', 'unusedAssetsSize' ], 'safe'],
        ];
    }
}