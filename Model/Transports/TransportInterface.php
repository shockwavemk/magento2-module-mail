<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Transports;
use stdClass;

/**
 * Mail Transport interface
 *
 * @api
 */
interface TransportInterface extends \Magento\Framework\Mail\TransportInterface
{
    /**
     * Send a mail using this transport
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage();


    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail\MessageInterface
     */
    public function getMessage();

    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function getMail();

    /**
     * @param $value
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setMail($value);
}