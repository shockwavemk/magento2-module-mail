<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Controller\Index;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Shockwavemk\Mail\Base\Model\Template\TransportBuilder;

use Mailgun\Mailgun;

use Shockwavedesign\Mail\Mailgun\Model\Config as ScopeConfig;
use Shockwavemk\Mail\Base\Model\Simulation\Config;

class Index extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    /**
     * Core store config
     *
     * @var Config
     */
    protected $config;

    /**
     * Core store config
     *
     * @var Config
     */
    protected $scopeConfig;

    /** @var \Magento\Customer\Helper\View $customerViewHelper */
    protected $customerViewHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    protected $customer;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Customer $customer
     * @param ScopeConfig $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $config,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Customer $customer,
        ScopeConfig $scopeConfig
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * TODO
     *
     * @return \Magento\Framework\View\Result\PageFactory
     * @throws \Magento\Framework\Exception\MailException
     */
    public function execute()
    {
        $documentBinary = 'test';

        // Demo Attachment setup

        /** @var \Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface $attachment1 */
        $attachment1 = $this->_objectManager->create('Shockwavemk\Mail\Base\Model\Mail\Attachment');

        $attachment1
            ->setBinary($documentBinary)
            ->setHash('testhash')
            ->setMimeType('application/pdf')
            ->setSize(7000)
        ;


        /** @var \Shockwavemk\Mail\Base\Model\Mail\Attachment $attachment2 */
        $attachment2 = $this->_objectManager->create('Shockwavemk\Mail\Base\Model\Mail\Attachment');

        $attachment2
            ->setBinary($documentBinary)
            ->setHash('testhash2')
            ->setMimeType('application/pdf')
            ->setSize(602)
        ;

        // Customer setup


        $this->customer->setWebsiteId($this->storeManager->getWebsite()->getId());
        $this->customer->loadByEmail('shockwavemk@googlemail.com');

        $backUrl = 'http://www.shockwave-design.de';

        $store = $this->storeManager->getStore();
        $storeId = $store->getId();

        $template = Customer::XML_PATH_REGISTER_EMAIL_TEMPLATE;
        $templateParams = ['customer' => $this->customer, 'back_url' => $backUrl, 'store' => $store];

        ;

        $sender = Customer::XML_PATH_REGISTER_EMAIL_IDENTITY;


        $templateId = $this->config->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);

        /** @var \Shockwavemk\Mail\Base\Model\Mail $mail */
        $mail = $this->_objectManager->get('Shockwavemk\Mail\Base\Model\Mail');

        $mail
            ->setTrackingEnabled(true)
            ->setTrackingClicksEnabled(true)
            ->setTrackingOpensEnabled(true)
            ->setRecipientVariables(
                '{"bob@example.com": {"first":"Bobby", "id":1},
                "alice@example.com": {"first":"Bear", "id": 2}}')
            ->setTags(
                array(
                    'Magento', 'Test', 'test2'
                )
            )
            ->setDeliveryTime('2016-05-01 10:00:00')
            ->setTestMode(true)
        ;
        
        $mail->addAttachment($attachment1);



        /** @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface $transport  */
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($this->config->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId))
            ->addTo($this->customer->getEmail(), $this->customer->getName())
            ->getTransport();

        $transport->sendMessage();

        /** @var  $mail */
        $mail = $transport->getMail();

        echo "test";
    }
}
























