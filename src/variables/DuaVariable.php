<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\variables;

use orbitalflight\deleteassets\models\DuaModel;
use orbitalflight\deleteassets\Plugin;

class DuaVariable {
    public function autoscan(int $id) {
        return Plugin::getInstance()->services->scan($id, false, false);
    }

    public function getVolume(int $id): ?DuaModel {
        return Plugin::getInstance()->services->getDuaModel($id);
    }
}