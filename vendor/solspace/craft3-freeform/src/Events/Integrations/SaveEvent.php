<?php

namespace Solspace\Freeform\Events\Integrations;

use craft\events\CancelableEvent;
use Solspace\Freeform\Models\IntegrationModel;

class SaveEvent extends CancelableEvent
{
    /** @var IntegrationModel */
    public $model;

    /** @var bool */
    public $new;

    /**
     * @param IntegrationModel $model
     * @param bool             $new
     */
    public function __construct(IntegrationModel $model, bool $new)
    {
        $this->model = $model;
        $this->new   = $new;

        parent::__construct();
    }

    /**
     * @return IntegrationModel
     */
    public function getModel(): IntegrationModel
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->new;
    }
}
