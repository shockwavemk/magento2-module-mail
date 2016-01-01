<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Shockwavemk\Mail\Model\Transports;

use Symfony\Component\Config\Definition\Exception\Exception;

class Base implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\TransportInterface
     */
    protected $_transport;

    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Shockwavemk\Mail\Model\Config $config,
        \Magento\Framework\Mail\MessageInterface $message,
        $parameters = null)
    {
        try
        {
            $transportClassName = $config->getTransportClassName();
            $this->_transport = new $transportClassName($config, $message, $parameters);
        }
        catch(Exception $e)
        {
            throw $e; // TODO
        }
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
            $this->_transport->sendMessage();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
