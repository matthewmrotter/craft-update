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
 * Class IntegrationRecord
 *
 * @property int       $id
 * @property string    $name
 * @property string    $handle
 * @property string    $type
 * @property string    $class
 * @property string    $accessToken
 * @property string    $settings
 * @property bool      $forceUpdate
 * @property \DateTime $lastUpdate
 */
class IntegrationRecord extends ActiveRecord
{
    const TABLE = '{{%freeform_integrations}}';

    const TYPE_MAILING_LIST    = 'mailing_list';
    const TYPE_CRM             = 'crm';
    const TYPE_PAYMENT_GATEWAY = 'payment_gateway';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return self::TABLE;
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['handle'], 'unique'],
            [['name', 'handle'], 'required'],
        ];
    }
}
