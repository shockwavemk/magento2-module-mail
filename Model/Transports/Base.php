<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Transports;

/**
 * Class Base
 * @package Shockwavemk\Mail\Base\Model\Transports
 */
class Base implements \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;

    /**
     * @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    protected $_transport;

    /**
     * @var \Shockwavemk\Mail\Base\Model\Storeages\StoreageInterface
     */
    protected $_storeage;

    /**
     * @param \Shockwavemk\Mail\Base\Model\Config $config
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Magento\Framework\ObjectManagerInterface $manager
     * @throws \Magento\Framework\Exception\MailException
     */
    public function __construct(
        \Shockwavemk\Mail\Base\Model\Config $config,
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\ObjectManagerInterface $manager,
        \Magento\Framework\Stdlib\DateTime $dateTime
    )
    {
        try {
            $this->_message = $message;
            $this->_dateTime = $dateTime;

            $transportClassName = $config->getTransportClassName();
            $this->_transport = $manager->get($transportClassName);

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase($e->getMessage()),
                $e);
        }
    }

    /**
     * Send a mail using this transport
     *
     * @return \Shockwavemk\Mail\Base\Model\Transports\Base
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
            // First: Send message with given transport
            $this->_transport->sendMessage();

            // Second: Create a mail instance to store
            $mail = $this->getMail();
            $mail->updateWithTransport($this->_transport);
            $mail->save();

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase($e->getMessage()),
                $e);
        }

        return $this;
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function getMail()
    {
        return $this->_transport->getMail();
    }

    /**
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return TransportInterface
     */
    public function setMail($mail)
    {
        $this->_transport->setMail($mail);
        return $this;
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail\MessageInterface
     */
    public function getMessage()
    {
        return $this->_transport->getMessage();
    }
}
