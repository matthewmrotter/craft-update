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

namespace Solspace\Freeform\Models;

use craft\base\Model;

class IntegrationsQueueModel extends Model
{
    /** @var int */
    public $id;

    /** @var int */
    public $submissionId;

    /** @var string */
    public $fieldHash;

    /** @var string */
    public $integrationType;

    /** @var string */
    public $status;

    /** @var string */
    public $fieldValuesJson;

}
