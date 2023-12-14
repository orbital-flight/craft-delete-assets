<?php

/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\migrations;

use Craft;
use craft\db\Migration;
use orbitalflight\deleteassets\records\DuaRecord;

/**
 * m231214_104429_bigint_update migration.
 */
class m231214_104430_bigint_update extends Migration {
    /**
     * @inheritdoc
     */
    public function safeUp(): bool {
        $table = DuaRecord::tableName();

        $this->alterColumn($table, 'usedAssetsSize', $this->bigInteger()->notNull());
        $this->alterColumn($table, 'revisionAssetsSize', $this->bigInteger()->notNull());
        $this->alterColumn($table, 'unusedAssetsSize', $this->bigInteger()->notNull());

        Craft::$app->db->schema->refresh();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool {
        echo "m231214_104429_bigint_update cannot be reverted.\n";
        return false;
    }
}
