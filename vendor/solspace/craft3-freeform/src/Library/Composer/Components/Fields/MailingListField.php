<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Freeform\Library\Composer\Components\Fields;

use Solspace\Freeform\Library\Composer\Components\AbstractField;
use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\InputOnlyInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\MailingListInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\RememberPostedValueInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Traits\MailingListTrait;
use Solspace\Freeform\Library\Composer\Components\Fields\Traits\SingleValueTrait;

class MailingListField extends AbstractField implements NoStorageInterface, SingleValueInterface, InputOnlyInterface, MailingListInterface, RememberPostedValueInterface
{
    use SingleValueTrait;
    use MailingListTrait;

    /** @var array */
    protected $mapping;

    /** @var bool */
    protected $hidden;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType(): string
    {
        return FieldInterface::TYPE_MAILING_LIST;
    }

    /**
     * MailingList uses its HASH as the Handle
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->getHash();
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return (bool) $this->hidden;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml(): string
    {
        $attributes = $this->getCustomAttributes();
        $this->addInputAttribute('class', $attributes->getClass());
        $isSelected = (bool) $this->getValue();

        if ($this->isHidden()) {
            return '<input '
                . $this->getInputAttributesString()
                . $this->getAttributeString('name', $this->getHash())
                . $this->getAttributeString('type', 'hidden')
                . $this->getAttributeString('id', $this->getIdAttribute())
                . $this->getAttributeString('value', 1, false)
                . $this->getRequiredAttribute()
                . $attributes->getInputAttributesAsString()
                . '/>';
        }

        return '<input '
            . $this->getInputAttributesString()
            . $this->getAttributeString('name', $this->getHash())
            . $this->getAttributeString('type', 'checkbox')
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('value', 1, false)
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . ($isSelected ? 'checked ' : '')
            . '/>';
    }

    /**
     * Output something before an input HTML is output
     *
     * @return string
     */
    protected function onBeforeInputHtml(): string
    {
        if ($this->isHidden()) {
            return '';
        }

        $attributes = $this->getCustomAttributes();
        $this->addLabelAttribute('class', $attributes->getLabelClass());

        return '<label'
            . $this->getLabelAttributesString()
            . '>';
    }

    /**
     * Output something after an input HTML is output
     *
     * @return string
     */
    protected function onAfterInputHtml(): string
    {
        if ($this->isHidden()) {
            return '';
        }

        $output = $this->getLabel();
        $output .= '</label>';

        return $output;
    }
}
