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
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\StaticValueInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Traits\SingleValueTrait;
use Solspace\Freeform\Library\Composer\Components\Fields\Traits\StaticValueTrait;

class CheckboxField extends AbstractField implements SingleValueInterface, InputOnlyInterface, StaticValueInterface
{
    use SingleValueTrait;
    use StaticValueTrait;

    /** @var bool */
    protected $checked;

    /** @var bool */
    protected $checkedByPost;

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_CHECKBOX;
    }

    /**
     * @return bool
     */
    public function isChecked(): bool
    {
        if (null !== $this->checkedByPost) {
            return $this->checkedByPost;
        }

        return $this->checked;
    }

    /**
     * @param bool $isChecked
     *
     * @return $this
     */
    public function setIsChecked(bool $isChecked)
    {
        $this->checked = $isChecked;

        return $this;
    }

    /**
     * @param bool $isChecked
     *
     * @return $this
     */
    public function setIsCheckedByPost(bool $isChecked)
    {
        $this->checkedByPost = $isChecked;

        return $this;
    }

    /**
     * Outputs the HTML of input
     *
     * @return string
     */
    public function getInputHtml(): string
    {
        $attributes = $this->getCustomAttributes();

        $output = '<input '
            . $this->getInputAttributesString()
            . $this->getAttributeString('name', $this->getHandle())
            . $this->getAttributeString('type', FieldInterface::TYPE_HIDDEN)
            . $this->getAttributeString('value', '')
            . $attributes->getInputAttributesAsString()
            . '/>';

        $output .= $this->getSingleInputHtml();

        return $output;
    }

    /**
     * @return string
     */
    public function getSingleInputHtml(): string
    {
        $attributes = $this->getCustomAttributes();
        $this->addInputAttribute('class', $attributes->getClass());

        return '<input '
            . $this->getInputAttributesString()
            . $this->getAttributeString('name', $this->getHandle())
            . $this->getAttributeString('type', $this->getType())
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('value', 1)
            . $this->getParameterString('checked', $this->isChecked())
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . '/>';
    }

        /**
     * @return \Twig_Markup
     */
    public function renderSingleInput(): \Twig_Markup
    {
        return $this->renderRaw($this->getSingleInputHtml());
    }

    /**
     * @param bool $optionsAsValues
     *
     * @return string
     */
    public function getValueAsString(bool $optionsAsValues = true): string
    {
        if ($optionsAsValues) {
            $value = (int) $this->getValue() === 1 ? $this->getStaticValue() : $this->getValue();

            return (string) $value;
        }

        return (string) $this->getValue();
    }

    /**
     * Output something before an input HTML is output
     *
     * @return string
     */
    protected function onBeforeInputHtml(): string
    {
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
        $output = $this->getLabel();
        $output .= '</label>';

        return $output;
    }
}
