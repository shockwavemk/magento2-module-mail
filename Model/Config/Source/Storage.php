<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Storage implements ArrayInterface
{
    protected $config;

    public function __construct(
        \Shockwavemk\Mail\Base\Model\Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $selection = array();
        foreach ($this->config->getStorageTypes() as $storageType)
        {
            $selection[] = [
                'label' => __($storageType['label']), 
                'value' => $storageType['value']
            ];
        }

        return $selection;
    }
}
