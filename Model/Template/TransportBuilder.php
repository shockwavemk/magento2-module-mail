<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Shockwavemk\Mail\Base\Model\Template;

/**
 * Class TransportBuilder
 * @package Shockwavemk\Mail\Base\Model\Template
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Mail Transport
     *
     * @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    protected $_transport;

    protected $_customerId;

    protected $_storeId;

    protected $_languageCode;

    /** @var \Shockwavemk\Mail\Base\Model\Mail */
    protected $_mail;

    protected $_sender;

    /**
     * @return mixed
     */
    public function getSenderMail()
    {
        return $this->_sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSenderMail($sender)
    {
        $this->_sender = $sender;
    }

    /**
     * Get mail transport
     *
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function getTransport()
    {
        $this->prepareMessage();

        return $this->createTransport();
    }

    /**
     * Get mail transport with a backup of a existing messageString
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail\MessageInterface $message
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function getBackupTransport($message)
    {
        $this->prepareMessage();

        // replace message
        $this->message = $message;

        return $this->createTransport();
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function getMail()
    {
        if(empty($this->_mail))
        {
            $this->_mail = $this->objectManager->get('Shockwavemk\Mail\Base\Model\Mail');

            $attachment = $this->objectManager->get('Shockwavemk\Mail\Base\Model\Mail\Attachment');
            
            $this->_mail->addAttachment($attachment);
        }

        return $this->_mail;
    }

    /**
     * @param $mail \Shockwavemk\Mail\Base\Model\Mail
     * @return $this \Shockwavemk\Mail\Base\Model\Template\TransportBuilder
     */
    public function setMail($mail)
    {
        $this->_mail = $mail;
        return $this;
    }

    /**
     * Set mail from address
     *
     * @param string|array $from
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     */
    public function setFrom($from)
    {
        $this->setSenderMail($this->_senderResolver->resolve($from));
        parent::setFrom($from);

        return $this;
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function updateMailWithTransportData()
    {
        $this->getMail()
            ->setTemplateIdentifier($this->templateIdentifier)
            ->setTemplateModel($this->templateModel)
            ->setVars($this->templateVars)
            ->setOptions($this->templateOptions)
            ->setCustomerId($this->getCustomerId())
            ->setStoreId($this->getStoreId())
            ->setLanguageCode($this->getLanguageCode())
            ->setSenderMail($this->getSenderMail());

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        if(empty($this->_customerId)) {

            if (!empty($this->templateVars['customer'])) {
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->templateVars['customer'];
                return $customer->getId();
            }

            if (!empty($this->templateVars['order'])) {
                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->templateVars['order'];
                return $order->getCustomerId();
            }
        }

        return $this->_customerId;
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        if(empty($this->_storeId)) {

            if (!empty($this->templateVars['store'])) {
                /** @var \Magento\Store\Model\Store $store */
                $store = $this->templateVars['store'];
                return $store->getId();
            }
        }

        return $this->_storeId;
    }

    /**
     * @return null|string
     */
    public function getLanguageCode()
    {
        if(empty($this->_languageCode)) {

            /** @var \Magento\Framework\Locale\Resolver $resolver */
            $resolver = $this->objectManager->get('Magento\Framework\Locale\Resolver');
            $resolver->emulate($this->getStoreId());

            return $resolver->getLocale();
        }

        return $this->_languageCode;
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function createTransport()
    {
        /** @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface $mailTransport */
        $mailTransport = $this->mailTransportFactory
            ->create(
                ['message' => clone $this->message]
            );

        $this->updateMailWithTransportData();

        $mailTransport->setMail($this->getMail());

        $this->reset();
        return $mailTransport;
    }
}
