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

namespace Solspace\Freeform\Controllers;

use craft\helpers\UrlHelper;
use Solspace\Commons\Helpers\PermissionHelper;
use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\Exceptions\FreeformException;
use Solspace\Freeform\Models\StatusModel;
use Solspace\Freeform\Records\StatusRecord;
use Solspace\Freeform\Resources\Bundles\StatisticsBundle;
use yii\web\HttpException;
use yii\web\Response;

class StatusesController extends BaseController
{
    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $statuses = $this->getStatusesService()->getAllStatuses();
        $this->view->registerAssetBundle(StatisticsBundle::class);

        return $this->renderTemplate('freeform/statuses', ['statuses' => $statuses]);
    }

    /**
     * @return Response
     */
    public function actionCreate(): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_FIELDS_MANAGE);

        $model = StatusModel::create();

        return $this->renderEditForm($model, 'Create new status');
    }

    /**
     * @param int $id
     *
     * @return Response
     * @throws \HttpException
     */
    public function actionEdit(int $id): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $model = $this->getStatusesService()->getStatusById($id);

        if (!$model) {
            throw new \HttpException(404, Freeform::t('"Status with ID {id} not found', ['id' => $id]));
        }

        return $this->renderEditForm($model, $model->name);
    }

    /**
     * @return Response
     */
    public function actionSave()
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $post = \Craft::$app->request->post();

        $statusId = $post['statusId'] ?? null;
        $status   = $this->getNewOrExistingStatus($statusId);

        $status->setAttributes($post);

        if ($this->getStatusesService()->save($status)) {
            // Return JSON response if the request is an AJAX request
            if (\Craft::$app->request->isAjax) {
                return $this->asJson(['success' => true]);
            }

            \Craft::$app->session->setNotice(Freeform::t('Status saved'));
            \Craft::$app->session->setFlash(Freeform::t('Status saved'));

            return $this->redirectToPostedUrl($status);
        }

        // Return JSON response if the request is an AJAX request
        if (\Craft::$app->request->isAjax) {
            return $this->asJson(['success' => false]);
        }

        \Craft::$app->session->setError(Freeform::t('Status not saved'));

        // Send the event back to the template
        \Craft::$app->urlManager->setRouteParams(['status' => $status, 'errors' => $status->getErrors()]);
    }

    /**
     * @return Response
     * @throws HttpException
     */
    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        if (!\Craft::$app->request->isAjax) {
            throw new HttpException(404, 'Page does not exist');
        }

        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $idList = json_decode(\Craft::$app->request->post('ids', '[]'));

        try {
            $order = 1;
            foreach ($idList as $id) {
                \Craft::$app
                    ->db
                    ->createCommand()
                    ->update(
                        StatusRecord::TABLE,
                        ['sortOrder' => $order++],
                        'id = :id',
                        ['id' => $id]
                    )
                    ->execute();
            }

            return $this->asJson(['success' => true]);
        } catch (\Exception $e) {
            return $this->asErrorJson($e->getMessage());
        }
    }

    /**
     * Deletes a field
     */
    public function actionDelete()
    {
        $this->requirePostRequest();
        if (!\Craft::$app->request->isAjax) {
            throw new HttpException(404, 'Page does not exist');
        }

        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $statusId = \Craft::$app->request->post('id');

        $this->getStatusesService()->deleteById($statusId);

        return $this->asJson(['success' => true]);
    }

    /**
     * @param StatusModel $model
     * @param string      $title
     *
     * @return Response
     */
    private function renderEditForm(StatusModel $model, string $title): Response
    {
        $variables = [
            'status'             => $model,
            'title'              => $title,
            'continueEditingUrl' => 'freeform/settings/statuses/{id}',
        ];

        return $this->renderTemplate('freeform/statuses/edit', $variables);
    }

    /**
     * @param int $statusId
     *
     * @return StatusModel
     * @throws \Exception
     */
    private function getNewOrExistingStatus($statusId): StatusModel
    {
        $statusService = $this->getStatusesService();

        if ($statusId) {
            $status = $statusService->getStatusById($statusId);

            if (!$status) {
                throw new FreeformException(
                    Freeform::t('Status with ID {id} not found', ['id' => $statusId])
                );
            }
        } else {
            $status            = StatusModel::create();
            $status->sortOrder = $statusService->getNextSortOrder();
        }

        return $status;
    }
}
