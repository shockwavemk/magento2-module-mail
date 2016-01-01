<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Mail config
 */
class Config
{
    const XML_PATH_TYPE = 'system/mail/type';
    const XML_PATH_TRANSPORT_TYPES = 'transport/types';

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
        $type = $this->getTransportType();
        $className = $type; // TODO

        return $className;
    }
}
