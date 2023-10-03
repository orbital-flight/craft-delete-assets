<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\models;

use Craft;
use craft\base\Model;

/**
 * Delete Unused Assets settings
 */
class Settings extends Model {
    // == Properties --------------------------------
    public string $usedAssetsColor = "12e193"; // Color of the used assets on the frontend rendering
    public string $revisionAssetsColor = "ffbf00"; // Color of the used assets on the frontend rendering
    public string $unusedAssetsColor = "e12d39"; // Color of the used assets on the frontend rendering

    // == Rules --------------------------------
    public function rules(): array {
        return [
            [['usedAssetsColor', 'revisionAssetsColor', 'unusedAssetsColor'], 'required'],
            [['usedAssetsColor', 'revisionAssetsColor', 'unusedAssetsColor'], 'match', 'pattern' => '/^([0-9a-fA-F]{3}){1,2}$/', 'message' => Craft::t('delete-assets', Craft::t('delete-assets', 'Invalid color format.'))]
        ];
    }
}
