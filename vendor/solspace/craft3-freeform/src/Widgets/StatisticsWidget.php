<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Freeform\Widgets;

use craft\base\Widget;
use Solspace\Commons\Helpers\PermissionHelper;
use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\DataObjects\FreeformStatistics;
use Solspace\Freeform\Resources\Bundles\StatisticsWidgetBundle;

class StatisticsWidget extends Widget
{
    /** @var array */
    public $statusIds;

    /** @var array */
    public $formIds;

    /** @var bool */
    public $showGlobalStatistics;

    /**
     * @return bool
     */
    public static function isSelectable(): bool
    {
        // This widget is only available to users that can manage submissions
        return PermissionHelper::checkPermission(Freeform::PERMISSION_SUBMISSIONS_MANAGE);
    }

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Freeform::getInstance()->name . ' ' . Freeform::t('Statistics');
    }

    /**
     * @return string
     */
    public static function iconPath(): string
    {
        return __DIR__ . '/../icon-mask.svg';
    }

    public function init()
    {
        parent::init();

        if (!$this->statusIds) {
            $this->statusIds = '*';
        }

        if (!$this->formIds) {
            $this->formIds = '*';
        }

        if (!$this->showGlobalStatistics) {
            $this->showGlobalStatistics = false;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['statusIds', 'formIds'], 'required'],
        ];
    }

    /**
     * @return string
     */
    public function getBodyHtml(): string
    {
        $freeform           = Freeform::getInstance();
        $submissionsService = $freeform->submissions;
        $forms              = $freeform->forms->getAllForms();

        $selectedStatusIds = $this->statusIds;
        if ($selectedStatusIds === '*') {
            $selectedStatusIds = null;
        }

        $selectedFormIds = $this->formIds;
        if ($selectedFormIds === '*') {
            $selectedFormIds = null;
        }

        $formStatistics = [];
        if (null !== $selectedFormIds) {
            foreach ($forms as $form) {
                if (\in_array($form->id, $selectedFormIds, false)) {
                    $submissionCount = $submissionsService->getSubmissionCount(
                        [$form->id],
                        $selectedStatusIds
                    );

                    $formStatistics[] = [
                        'label'      => $form->name,
                        'statistics' => new FreeformStatistics($submissionCount, $form->spamBlockCount),
                    ];
                }
            }
        }

        if (empty($selectedFormIds)) {
            $submissionCount = $submissionsService->getSubmissionCount(null, $selectedStatusIds);
            $spamBlockCount  = 0;
            foreach ($forms as $form) {
                if (!$form) {
                    continue;
                }
                $spamBlockCount += $form->spamBlockCount;
            }

            $formStatistics[] = [
                'statistics' => new FreeformStatistics($submissionCount, $spamBlockCount),
            ];
        }

        $fieldCount        = null;
        $formCount         = null;
        $notificationCount = null;
        if ($this->showGlobalStatistics) {
            $fieldCount        = \count($freeform->fields->getAllFieldHandles());
            $formCount         = \count($forms);
            $notificationCount = \count($freeform->notifications->getAllNotifications());
        }

        \Craft::$app->view->registerAssetBundle(StatisticsWidgetBundle::class);

        return \Craft::$app->view->renderTemplate(
            'freeform/_widgets/statistics/body',
            [
                'formStatistics'       => $formStatistics,
                'showGlobalStatistics' => $this->showGlobalStatistics,
                'fieldCount'           => $fieldCount,
                'formCount'            => $formCount,
                'notificationCount'    => $notificationCount,
            ]
        );
    }

    /**
     * @return string
     */
    public function getSettingsHtml(): string
    {
        $freeform = Freeform::getInstance();

        $statuses   = $freeform->statuses->getAllStatuses();
        $statusList = [];
        foreach ($statuses as $status) {
            if (!$status) {
                continue;
            }
            $statusList[$status->id] = $status->name;
        }

        $forms    = $freeform->forms->getAllForms();
        $formList = [];
        foreach ($forms as $form) {
            $formList[$form->id] = $form->name;
        }

        return \Craft::$app->view->renderTemplate(
            'freeform/_widgets/statistics/settings',
            [
                'settings'   => $this->getSettings(),
                'statusList' => $statusList,
                'formList'   => $formList,
            ]
        );
    }
}
