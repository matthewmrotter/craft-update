<?php

namespace Solspace\Freeform\Events\Mailer;

use craft\events\CancelableEvent;
use craft\mail\Message;
use Solspace\Freeform\Elements\Submission;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\Freeform\Library\Mailing\NotificationInterface;

class SendEmailEvent extends CancelableEvent
{
    /** @var Message */
    public $message;

    /** @var Form */
    public $form;

    /** @var NotificationInterface */
    public $notification;

    /** @var array */
    public $fieldValues;

    /** @var Submission */
    public $submission;

    /**
     * @param Message               $message
     * @param Form                  $form
     * @param NotificationInterface $notification
     * @param array                 $fieldValues
     * @param Submission|null       $submission
     */
    public function __construct(
        Message $message,
        Form $form,
        NotificationInterface $notification,
        array $fieldValues,
        Submission $submission = null
    )
    {
        $this->message      = $message;
        $this->form         = $form;
        $this->notification = $notification;
        $this->fieldValues  = $fieldValues;
        $this->submission   = $submission;

        parent::__construct([]);
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification(): NotificationInterface
    {
        return $this->notification;
    }

    /**
     * @return array
     */
    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    /**
     * @return Submission|null
     */
    public function getSubmission()
    {
        return $this->submission;
    }
}
