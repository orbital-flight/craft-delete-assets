<?php

/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\migrations;

use Craft;
use craft\db\Migration;
use orbitalflight\deleteassets\records\DuaRecord;

/**
 * m231115_161431_dua_two migration.
 */
class m231115_161431_dua_two extends Migration {
    /**
     * @inheritdoc
     */
    public function safeUp(): bool {
        $table = DuaRecord::tableName();

        $this->addColumn($table, 'outdated', $this->boolean()->notNull()->defaultValue(false));
        Craft::$app->db->schema->refresh();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool {
        echo "m231115_161431_dua_two cannot be reverted.\n";
        return false;
    }
}
