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
    /**
     *
     */
    const XML_PATH_TYPE = 'system/smtp/type';
    /**
     *
     */
    const XML_PATH_TRANSPORT_TYPES = 'transport/types';

    /**
     *
     */
    const XML_PATH_STORAGE = 'system/smtp/storage';
    /**
     *
     */
    const XML_PATH_STORAGE_TYPES = 'transport/storages';
    /**
     *
     */
    const XML_PATH_SPOOLER_FOLDER_PATH = 'system/smtp/host_spool_folder_path';
    /**
     *
     */
    const XML_PATH_SPOOLER_RETRY_LIMIT = 'system/smtp/host_spool_folder_retry_limit';
    /**
     *
     */
    const XML_PATH_MAIL_TEST_MODE = 'system/smtp/test_mode';
    /**
     *
     */
    const XML_PATH_MAIL_TRACKING_ENABLED = 'system/smtp/tracking_enabled';
    /**
     *
     */
    const XML_PATH_MAIL_TRACKING_CLICKS_ENABLED = 'system/smtp/tracking_clicks_enabled';
    /**
     *
     */
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
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    // Transport

    /**
     * @return mixed
     */
    public function getTransportType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TYPE);
    }

    /**
     * @return mixed
     */
    public function getTransportTypes()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TRANSPORT_TYPES);
    }

    /**
     * @return null
     */
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

    // Storage

    /**
     * This path is used to store (temporary) files on local server
     * e.g. <magento root>/pub/media/emails
     *
     * @return mixed
     */
    public function getHostSpoolerFolderPath()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SPOOLER_FOLDER_PATH);
    }

    /**
     * A configurable limit how often a write access is tried on local file system on write
     *
     * @return mixed
     */
    public function getHostRetryLimit()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SPOOLER_RETRY_LIMIT);
    }

    /**
     * Returns string class name of storage class
     *
     * @return string
     */
    public function getStorageType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STORAGE);
    }

    /**
     * @return string[]
     */
    public function getStorageTypes()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STORAGE_TYPES);
    }

    /**
     * @return null
     */
    public function getStorageClassName()
    {
        $type = $this->getStorageType();
        $types = $this->getStorageTypes();

        if(empty($typeConfig = $types[$type]))
        {
            return null;
        }
        
        return $typeConfig['class'];
    }

    /**
     * If test mode enabled, emails are built and stored, not sent
     * 
     * @return bool
     */
    public function getTestMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TEST_MODE);
    }

    /**
     * Depending on mail transport vendor, all links in outgoing messages are replaced by tracking links
     * 
     * @return bool
     */
    public function getTrackingEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TRACKING_ENABLED);
    }

    /**
     * @return bool
     */
    public function getTrackingClicksEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TRACKING_CLICKS_ENABLED);
    }

    /**
     * @return bool
     */
    public function getTrackingOpensEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAIL_TRACKING_OPENS_ENABLED);
    }
}
