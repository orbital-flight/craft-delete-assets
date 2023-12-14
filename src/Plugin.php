<?php

/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\elements\Asset;
use craft\events\ModelEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use craft\web\twig\variables\CraftVariable;
use yii\base\Event;
use orbitalflight\deleteassets\models\Settings;
use orbitalflight\deleteassets\services\DuaService;
use orbitalflight\deleteassets\services\HelpersService;
use orbitalflight\deleteassets\variables\DuaVariable;

/**
 * Delete Unused Assets plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @author Orbital Flight <flightorbital@gmail.com>
 * @copyright Orbital Flight
 * @license MIT
 */
class Plugin extends BasePlugin {
    public string $schemaVersion = '2.0.1';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;

    public static function config(): array {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
            // ...
        });

        // Register services
        $this->setComponents([
            'services' => DuaService::class,
            'helpers' => HelpersService::class,
        ]);

        // Register variable
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('deleteAssets', DuaVariable::class);
            }
        );

        // Register permiissions
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {

                $permissionVolumes = $this->services->getPermissionVolumes();

                $duaPermissions = [];

                $duaPermissions['dua-access'] = [
                    'label' => "Perform a deletion action",
                    'nested' => $permissionVolumes,
                ];

                $event->permissions[] = [
                    'heading' => "Delete Unused Assets",
                    'permissions' => $duaPermissions,
                ];
            }
        );

        // * Auto-detect outdated volumes
        // When an asset is deleted
        Event::on(
            Asset::class,
            Asset::EVENT_AFTER_DELETE,
            function (Event $event) {
                $asset = $event->sender;
                $this->services->markVolumeAsOutdated($asset->getVolumeId());
            }
        );

        // When an asset is saved
        Event::on(
            Asset::class,
            Asset::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                $asset = $event->sender;
                $this->services->markVolumeAsOutdated($asset->getVolumeId());
            }
        );
    }

    public function getCpNavItem(): ?array {
        $item = parent::getCpNavItem();
        if ($this->getSettings()->showBadge) {
            $item['badgeCount'] = $this->services->getBadgeCount();
        }

        return $item;
    }

    protected function createSettingsModel(): ?Model {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string {
        return Craft::$app->view->renderTemplate('delete-assets/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }
}
