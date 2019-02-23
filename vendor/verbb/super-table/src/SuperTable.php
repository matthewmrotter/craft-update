<?php
namespace verbb\supertable;

use verbb\supertable\base\PluginTrait;
use verbb\supertable\elements\SuperTableBlockElement;
use verbb\supertable\fields\SuperTableField;
use verbb\supertable\models\SuperTableBlockTypeModel;
use verbb\supertable\variables\SuperTableVariable;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Elements;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;

use NerdsAndCompany\Schematic\Schematic;
use NerdsAndCompany\Schematic\Events\ConverterEvent;

use barrelstrength\sproutbase\app\import\services\Importers;

use yii\base\Event;

class SuperTable extends Plugin
{
    // Public Properties
    // =========================================================================

    public $schemaVersion = '2.0.4';
    public $hasCpSettings = true;

    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerVariables();
        $this->_registerFieldTypes();
        $this->_registerElementTypes();
        $this->_registerIntegrations();
    }

    // Private Methods
    // =========================================================================


    private function _registerVariables()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('superTable', SuperTableVariable::class);
        });
    }

    private function _registerFieldTypes()
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = SuperTableField::class;
        });
    }

    private function _registerElementTypes()
    {
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = SuperTableBlockElement::class;
        });
    }

    private function _registerIntegrations()
    {
        // Support for Schematic - https://github.com/nerds-and-company/schematic
        if (class_exists(Schematic::class)) {
            Event::on(Schematic::class, Schematic::EVENT_RESOLVE_CONVERTER, function (ConverterEvent $event) {
                if ($event->modelClass == SuperTableField::class) {
                    $event->converterClass = 'verbb\supertable\integrations\schematic\converters\fields\SuperTableSchematic';
                }

                if ($event->modelClass == SuperTableBlockTypeModel::class) {
                    $event->converterClass = 'verbb\supertable\integrations\schematic\converters\models\SuperTableBlockTypeSchematic';
                }
            });
        }

        // Support for Sprout Import - https://github.com/barrelstrength/craft-sprout-import
        if (class_exists(Importers::class)) {
            Event::on(Importers::class, Importers::EVENT_REGISTER_IMPORTER_TYPES, function (RegisterComponentTypesEvent $event) {
                $event->types[] = 'verbb\supertable\integrations\sproutimport\importers\fields\SuperTableImporter';
            });
        }
    }

}
