<?php
/**
 * Freeform for Craft
 *
 * @package   Solspace:Freeform
 * @author    Solspace, Inc.
 * @copyright Copyright (c) 2008-2016, Solspace, Inc.
 * @link      https://solspace.com/craft/freeform
 * @license   https://solspace.com/software/license-agreement
 */

namespace Solspace\Freeform\Library\Payments;

interface PaymentHandlerInterface
{
    /**
     * Returns payment for submission, only first payment is returned for subscriptions
     *
     * @param integer $submissionId
     *
     * @return PaymentInterface|null
     */
    public function getBySubmissionId(int $submissionId);

    /**
     * Finds a payment with a matching resource id for specific integration
     *
     * @param string $resourceId
     * @param integer $integrationId
     *
     * @return PaymentInterface|null
     */
    public function getByResourceId(string $resourceId, int $integrationId);

    /**
     * Saves payment
     *
     * @param PaymentInterface $payment
     *
     * @return bool
     */
    public function save(PaymentInterface $payment): bool;
}
