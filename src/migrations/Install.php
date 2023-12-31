<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets\migrations;

use Craft;
use craft\db\Migration;
use orbitalflight\deleteassets\records\DuaRecord;

/**
 * Install migration.
 */
class Install extends Migration {
    /**
     * @inheritdoc
     */
    public function safeUp(): bool {
        $table = DuaRecord::tableName();

        if (!$this->db->tableExists($table)) {
            $this->createTable($table, [
                'id' => $this->primaryKey(),
                'volumeId' => $this->integer()->notNull(),
                'outdated' => $this->boolean()->notNull()->defaultValue(false),
                'locked' => $this->boolean()->notNull()->defaultValue(false),
                'totalAssets' => $this->integer()->notNull(),
                'usedAssets' => $this->integer()->notNull(),
                'usedAssetsSize' => $this->bigInteger()->notNull(),
                'revisionAssets' => $this->integer()->notNull(),
                'revisionAssetsSize' => $this->bigInteger()->notNull(),
                'unusedAssets' => $this->integer()->notNull(),
                'unusedAssetsSize' => $this->bigInteger()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);
        }

        Craft::$app->db->schema->refresh();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool {

        $this->dropTableIfExists(DuaRecord::tableName());

        return true;
    }
}
