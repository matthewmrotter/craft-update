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
use Solspace\Freeform\Library\Exceptions\Integrations\IntegrationException;
use Solspace\Freeform\Library\Integrations\MailingLists\MailingListOAuthConnector;
use Solspace\Freeform\Models\IntegrationModel;
use Solspace\Freeform\Records\IntegrationRecord;
use Solspace\Freeform\Resources\Bundles\IntegrationsBundle;
use Solspace\Freeform\Resources\Bundles\MailingListsBundle;
use yii\web\Response;

class MailingListsController extends BaseController
{
    /**
     * Make sure this controller requires a logged in member
     */
    public function init()
    {
        if (!\Craft::$app->request->getIsConsoleRequest()) {
            $this->requireLogin();
        }
    }

    /**
     * Presents a list of all mailing list integrations
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $mailingListIntegrations = $this->getMailingListsService()->getAllIntegrations();

        \Craft::$app->view->registerAssetBundle(MailingListsBundle::class);

        return $this->renderTemplate(
            'freeform/settings/_mailing_lists',
            [
                'integrations' => $mailingListIntegrations,
                'providers'    => $this->getMailingListsService()->getAllMailingListServiceProviders(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function actionCreate(): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $model = IntegrationModel::create(IntegrationRecord::TYPE_MAILING_LIST);

        return $this->renderEditForm($model, 'Create new mailing list');
    }

    /**
     * @param int|null              $id
     * @param IntegrationModel|null $model
     *
     * @return Response
     * @throws \HttpException
     */
    public function actionEdit(int $id = null, IntegrationModel $model = null): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        if (null === $model) {
            $model = $this->getMailingListsService()->getIntegrationById($id);
        }

        if (!$model) {
            throw new \HttpException(
                404,
                Freeform::t('Mailing List integration with ID {id} not found', ['id' => $id])
            );
        }

        return $this->renderEditForm($model, $model->name);
    }

    /**
     * @param string|null           $handle
     *
     * @return Response
     * @throws \HttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionHandleOAuthRedirect(string $handle = null): Response
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);
        $model = $this->getMailingListsService()->getIntegrationByHandle($handle);

        if (!$model) {
            throw new \HttpException(
                404,
                Freeform::t('Mailing List integration with ID {id} not found', ['id' => $id])
            );
        }

        if (\Craft::$app->request->getParam('code')) {
            $response = $this->handleAuthorization($model);

            if (null !== $response) {
                return $response;
            }
        }

        return $this->renderEditForm($model, $model->name);
    }

    /**
     * Saves an integration
     */
    public function actionSave()
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $this->requirePostRequest();

        $post = \Craft::$app->request->post();

        $handle = $post['handle'] ?? null;
        $model  = $this->getNewOrExistingMailingListIntegrationModel($handle);

        $isNewIntegration = !$model->id;

        $postedClass  = $post['class'];
        $model->class = $postedClass;

        $postedClassSettings = $post['settings'][$postedClass] ?? [];
        unset($post['settings']);

        $settingBlueprints = $this->getMailingListsService()->getMailingListSettingBlueprints($postedClass);

        foreach ($postedClassSettings as $key => $value) {
            $isValueValid = false;

            foreach ($settingBlueprints as $blueprint) {
                if ($blueprint->getHandle() === $key) {
                    $isValueValid = true;

                    break;
                }
            }

            if (!$isValueValid) {
                unset($postedClassSettings[$key]);
            }
        }

        // Adding hidden stored settings to the list
        foreach ($model->getIntegrationObject()->getSettings() as $key => $value) {
            if (!isset($postedClassSettings[$key])) {
                $postedClassSettings[$key] = $value;
            }
        }

        $post['settings'] = $postedClassSettings ?: null;

        $model->setAttributes($post);

        try {
            $model->getIntegrationObject()->onBeforeSave($model);
        } catch (\Exception $e) {
            $model->addError('integration', $e->getMessage());
        }

        if (!$model->getErrors() && $this->getMailingListsService()->save($model)) {

            // If it's a new integration - we make the user complete OAuth2 authentication
            if ($isNewIntegration) {
                $model->getIntegrationObject()->initiateAuthentication();
            }

            // Return JSON response if the request is an AJAX request
            if (\Craft::$app->request->isAjax) {
                return $this->asJson(['success' => true]);
            }

            \Craft::$app->session->setNotice(Freeform::t('Mailing List Integration saved'));
            \Craft::$app->session->setFlash('Mailing List Integration saved');

            return $this->redirectToPostedUrl($model);
        }

        // Return JSON response if the request is an AJAX request
        if (\Craft::$app->request->isAjax) {
            return $this->asJson(['success' => false]);
        }

        \Craft::$app->session->setError(Freeform::t('Mailing List Integration not saved'));

        return $this->renderEditForm($model, $model->name);
    }

    /**
     * Checks integration connection
     */
    public function actionCheckIntegrationConnection()
    {
        $id = \Craft::$app->request->post('id');

        $integration = $this->getMailingListsService()->getIntegrationObjectById($id);

        try {
            $integration->checkConnection();

            return $this->asJson(['success' => true]);
        } catch (IntegrationException $exception) {
            return $this->asJson(['success' => false, 'errors' => $exception->getMessage()]);
        }
    }

    /**
     * Checks integration connection
     *
     * @param string $handle
     *
     * @throws IntegrationException
     */
    public function actionForceAuthorization(string $handle)
    {
        $model  = $this->getMailingListsService()->getIntegrationByHandle($handle);

        if (!$model) {
            throw new IntegrationException(
                Freeform::t("Mailing list with handle '{handle}' not found", ['handle' => $handle])
            );
        }

        $integration = $model->getIntegrationObject();

        $integration->initiateAuthentication();
    }

    /**
     * Deletes a mailing integration
     */
    public function actionDelete()
    {
        $this->requirePostRequest();
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $id = \Craft::$app->request->post('id');

        $this->getMailingListsService()->delete($id);

        return $this->asJson(['success' => true]);
    }

    /**
     * @param string $handle
     *
     * @return IntegrationModel
     */
    private function getNewOrExistingMailingListIntegrationModel($handle): IntegrationModel
    {
        $mailingListIntegration = $this->getMailingListsService()->getIntegrationByHandle($handle);

        if (!$mailingListIntegration) {
            $mailingListIntegration = IntegrationModel::create(IntegrationRecord::TYPE_MAILING_LIST);
        }

        return $mailingListIntegration;
    }

    /**
     * Handle OAuth2 authorization
     *
     * @param IntegrationModel $model
     *
     * @return Response|null
     */
    private function handleAuthorization(IntegrationModel $model)
    {
        $integration = $model->getIntegrationObject();
        $code        = \Craft::$app->request->getParam('code');

        if (!$integration instanceof MailingListOAuthConnector || empty($code)) {
            return null;
        }

        $accessToken = $integration->fetchAccessToken();

        $model->accessToken = $accessToken;
        $model->settings    = $integration->getSettings();

        if ($this->getMailingListsService()->save($model)) {
            // Return JSON response if the request is an AJAX request
            \Craft::$app->session->setNotice(Freeform::t('Mailing List Integration saved'));
            \Craft::$app->session->setFlash(Freeform::t('Mailing List Integration saved'));
        } else {
            \Craft::$app->session->setError(Freeform::t('Mailing List Integration not saved'));
        }

        return $this->redirect(UrlHelper::cpUrl('freeform/settings/mailing-lists/' . $model->handle));
    }

    /**
     * @param IntegrationModel $model
     * @param string           $title
     *
     * @return Response
     */
    private function renderEditForm(IntegrationModel $model, string $title): Response
    {
        $this->view->registerAssetBundle(IntegrationsBundle::class);

        if (\Craft::$app->request->getParam('code')) {
            $response = $this->handleAuthorization($model);

            if (null !== $response) {
                return $response;
            }
        }

        $serviceProviderTypes = $this->getMailingListsService()->getAllMailingListServiceProviders();
        $settingBlueprints    = $this->getMailingListsService()->getAllMailingListSettingBlueprints();

        $variables = [
            'integration'          => $model,
            'blockTitle'           => $title,
            'serviceProviderTypes' => $serviceProviderTypes,
            'continueEditingUrl'   => 'freeform/settings/mailing-lists/{handle}',
            'settings'             => $settingBlueprints,
        ];

        return $this->renderTemplate('freeform/settings/_mailing_list_edit', $variables);
    }
}
