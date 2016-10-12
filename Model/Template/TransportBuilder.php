<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Shockwavemk\Mail\Base\Model\Template;

use Magento\Framework\App\AreaList;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

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
     * @var AreaList
     */
    private $areaList;

    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        AreaList $areaList)
    {
        parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory);
        $this->areaList = $areaList;
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
     * Prepare message
     *
     * @return $this
     * @throws \Zend_Mail_Exception
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();

        $types = [
            TemplateTypesInterface::TYPE_TEXT => MessageInterface::TYPE_TEXT,
            TemplateTypesInterface::TYPE_HTML => MessageInterface::TYPE_HTML,
        ];

        // Bugfix for not translated cron scheduled mails
        $areaObject = $this->areaList->getArea($template->getDesignConfig()->getArea());
        $areaObject->load(\Magento\Framework\App\Area::PART_TRANSLATE);

        $body = $template->processTemplate();
        $this->message->setMessageType($types[$template->getType()])
            ->setBody($body)
            ->setSubject($template->getSubject());

        return $this;
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function createTransport()
    {
        // Verify to get a clean mail instance
        $this->setMail(null);

        // Transfer TransportData to Mail
        $this->updateMailWithTransportData();

        /** @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface $mailTransport */
        $mailTransport = $this->mailTransportFactory
            ->create(
                ['message' => clone $this->message]
            );
        
        $mailTransport->setMail($this->getMail());

        // Cleanup of mail and message model used in this transport
        $this->reset();
        
        return $mailTransport;
    }

    /**
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function updateMailWithTransportData()
    {
        $this->getMail()
            ->setSubject($this->message->getSubject())
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
     * Get mail
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getMail()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($this->_mail)) {
            $this->_mail = $this->objectManager
                ->create('Shockwavemk\Mail\Base\Model\Mail');

            /** @var \Shockwavemk\Mail\Base\Model\Mail\AttachmentCollection $attachmentCollection */
            $attachmentCollection = $this->objectManager
                ->get('Shockwavemk\Mail\Base\Model\Mail\AttachmentCollection');

            foreach ($attachmentCollection as $attachment) {
                $this->_mail->addAttachment($attachment);
            }
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
     * @return int|null
     */
    public function getCustomerId()
    {
        if (empty($this->_customerId)) {

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

            if (!empty($this->templateVars['rma'])) {
                /** @var \Magento\Rma\Model\Rma $rma */
                $rma = $this->templateVars['rma'];
                return !empty($rma->getCustomerId()) ? $rma->getCustomerId() : $rma->getOrder()->getCustomerId();
            }
        }

        return $this->_customerId;
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        if (empty($this->_storeId)) {

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
        if (empty($this->_languageCode)) {

            /** @var \Magento\Framework\Locale\Resolver $resolver */
            $resolver = $this->objectManager->get('Magento\Framework\Locale\Resolver');
            $resolver->emulate($this->getStoreId());

            return $resolver->getLocale();
        }

        return $this->_languageCode;
    }

    /**
     * @return mixed
     */
    public function getSenderMail()
    {
        return $this->_sender;
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
     * Set mail from address
     *
     * @param string|array $from
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     */
    public function setFrom($from)
    {
	$result = $this->_senderResolver->resolve($from, $this->getStoreId());
        $this->setSenderMail($result);
        $this->message->setFrom($result['email'], $result['name']);
        
        return $this;
    }

    /**
     * @param mixed $sender
     */
    public function setSenderMail($sender)
    {
        $this->_sender = $sender;
    }

    /**
     * @return array
     */
    public function getTemplateOptions()
    {
        return $this->templateOptions;
    }
}
