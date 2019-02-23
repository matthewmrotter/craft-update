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

namespace Solspace\Freeform\Records;


use craft\db\ActiveRecord;

/**
 * Class IntegrationsQueueRecord
 *
 * @property int       $id
 * @property int       $submissionId
 * @property string    $fieldHash
 * @property string    $integrationType
 * @property string    $status
 * @property string    $fieldValuesJson
 */
class IntegrationsQueueRecord extends ActiveRecord
{
    const TABLE = '{{%freeform_integrations_queue}}';

    const STATUS_ENQUEUED = 'enqueued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    const STATUS_HALTED = 'halted';

    const INTEGRATION_TYPE_MAILING_LIST = 'mailing_list';
    const INTEGRATION_TYPE_CRM          = 'crm';
    const INTEGRATION_TYPE_NOTIFICATION = 'notification';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return self::TABLE;
    }
}
