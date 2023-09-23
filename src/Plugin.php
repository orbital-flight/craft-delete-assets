<?php
/**
 * @copyright Copyright (c) Orbital Flight
 */

namespace orbitalflight\deleteassets;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
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
    public string $schemaVersion = '1.0.0';
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
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });

        // Register services
        $this->setComponents([
            'services' => DuaService::class,
            'helpers' => HelpersService::class,
        ]);

        // Register variable
        Event::on(CraftVariable::class, 
        CraftVariable::EVENT_INIT, 
        function(Event $event){
            $variable = $event->sender;
            $variable->set('deleteAssets', DuaVariable::class);
        });
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
