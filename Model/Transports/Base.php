<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Transports;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime;
use Shockwavemk\Mail\Base\Model\Config;
use Shockwavemk\Mail\Base\Model\Mail;

/**
 * Class Base
 * @package Shockwavemk\Mail\Base\Model\Transports
 */
class Base implements \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
{
    /**
     * @var MessageInterface
     */
    protected $_message;

    /**
     * @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    protected $_transport;

    /**
     * Selects transport class name from config and creates a new transport object with given message
     *
     * @param Config $config
     * @param MessageInterface $message
     * @param ObjectManagerInterface $manager
     * @throws MailException
     */
    public function __construct(
        Config $config,
        MessageInterface $message,
        ObjectManagerInterface $manager
    )
    {
        try {
            $this->_message = $message;
            $transportClassName = $config->getTransportClassName();
            $this->_transport = $manager->create(
                $transportClassName,
                ['message' => $message]
            );

        } catch (\Exception $e) {
            throw new MailException(
                new Phrase($e->getMessage()),
                $e);
        }
    }

    /**
     * Send a mail using this transport
     *
     * @return Base
     * @throws MailException
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
            throw new MailException(
                new Phrase($e->getMessage()),
                $e
            );
        }

        return $this;
    }

    /**
     * Get the mail object of this transport
     *
     * @return Mail
     */
    public function getMail()
    {
        return $this->_transport->getMail();
    }

    /**
     * Associate a mail object with this transport
     * Mail object is used to store transport information and sent message
     *
     * @param Mail $mail
     * @return TransportInterface
     */
    public function setMail($mail)
    {
        $this->_transport->setMail($mail);
        return $this;
    }

    /**
     * Get message associated with this transport
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail\MessageInterface
     */
    public function getMessage()
    {
        return $this->_transport->getMessage();
    }
}
