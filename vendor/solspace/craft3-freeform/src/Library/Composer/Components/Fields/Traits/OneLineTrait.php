<?php

namespace Solspace\Freeform\Library\Composer\Components\Fields\Traits;

trait OneLineTrait
{
    /** @var bool */
    protected $oneLine;

    /**
     * @return bool
     */
    public function isOneLine(): bool
    {
        return (bool) $this->oneLine;
    }
}
