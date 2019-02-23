<?php

namespace Solspace\Freeform\Events\Forms;

use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\Freeform\Library\Logging\FreeformLogger;
use yii\base\Event;

class PageJumpEvent extends Event
{
    /** @var Form */
    public $form;

    /** @var int */
    private $jumpToIndex;

    /**
     * PageJumpEvent constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        parent::__construct([]);
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return int|null
     */
    public function getJumpToIndex()
    {
        return $this->jumpToIndex;
    }

    /**
     * @param mixed $jumpToIndex
     */
    public function setJumpToIndex(int $jumpToIndex = null)
    {
        $totalPages = \count($this->getForm()->getLayout()->getPages());

        if ($jumpToIndex < $totalPages) {
            $this->jumpToIndex = $jumpToIndex;
        } else {
            Freeform::getInstance()->logger
                ->getLogger(FreeformLogger::CONDITIONAL_RULE)
                ->error(
                    Freeform::t(
                        'Form "{form}" did not correctly jump to page index "{pageIndex}"',
                        [
                            'form'      => $this->getForm()->getName(),
                            'pageIndex' => $this->jumpToIndex,
                        ]
                    ),
                    ['form' => $this->getForm()]
                );
        }
    }
}
