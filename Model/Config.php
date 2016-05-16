<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Mail config
 */
class Config
{
    const XML_PATH_TYPE = 'system/smtp/type';
    const XML_PATH_TRANSPORT_TYPES = 'transport/types';

    const XML_PATH_STOREAGE = 'system/smtp/storeage';
    const XML_PATH_STOREAGE_TYPES = 'transport/storeages';
    const XML_PATH_SPOOLER_FOLDER_PATH = 'system/smtp/host_spool_folder_path';
    const XML_PATH_SPOOLER_RETRY_LIMIT = 'system/smtp/host_spool_folder_retry_limit';
    const XML_PATH_MAIL_TEST_MODE = 'system/smtp/test_mode';
    const XML_PATH_MAIL_TRACKING_ENABLED = 'system/smtp/tracking_enabled';
    const XML_PATH_MAIL_TRACKING_CLICKS_ENABLED = 'system/smtp/tracking_clicks_enabled';
    const XML_PATH_MAIL_TRACKING_OPENS_ENABLED = 'system/smtp/tracking_opens_enabled';

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    // Transport

    public function getTransportType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TYPE);
    }

    public function getTransportTypes()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TRANSPORT_TYPES);
    }

    public function getTransportClassName()
    {
        $className = null;

        $type = $this->getTransportType();
        $types = $this->getTransportTypes();

        if(!empty($types[$type]))
        {
            $typeConfig = $types[$type];

            $className = $typeConfig['class'];
        }

        return $className;
    }

    // Storeage

    public function getHostSpoolerFolderPath()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SPOOLER_FOLDER_PATH);
    }
    
    public function getHostRetryLimit()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SPOOLER_RETRY_LIMIT);
    }
    
    public function getStoreageType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STOREAGE);
    }

    public function getStoreageTypes()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STOREAGE_TYPES);
    }

    public function getStoreageClassName()
    {
        $className = null;

        $type = $this->getStoreageType();
        $types = $this->getStoreageTypes();

        if(!empty($types[$type]))
        {
            $typeConfig = $types[$type];

            $className = $typeConfig['class'];
        }

        return $className;
    }

    public function getTestMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TEST_MODE);
    }

    public function getTrackingEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TRACKING_ENABLED);
    }

    public function getTrackingClicksEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TRACKING_CLICKS_ENABLED);
    }

    public function getTrackingOpensEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TRACKING_OPENS_ENABLED);
    }
}
