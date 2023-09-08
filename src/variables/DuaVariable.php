<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\variables;

use orbitalflight\deleteassets\models\DuaModel;
use orbitalflight\deleteassets\Plugin;

class DuaVariable {
    public function getVolume(int $id): ?DuaModel {
        return Plugin::getInstance()->services->getDuaModel($id);
    }
}