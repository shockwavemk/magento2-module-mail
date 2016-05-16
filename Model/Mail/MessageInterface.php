<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Mail;

/**
 * Mail Storeage interface
 *
 * @api
 */
interface MessageInterface extends \Magento\Framework\Mail\MessageInterface
{
    public function getRecipients();

    public function getFrom();

    public function getReplyTo();

    public function getReturnPath();

    public function getSubject();

    public function getDate();

    public function getMessageId();

    public function getHeaders();
}
