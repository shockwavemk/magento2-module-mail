<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Config\Source;

use Shockwavemk\Mail\Base\Model\Config;

class Type implements \Magento\Framework\Option\ArrayInterface
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
        if(empty($this->config->getTransportTypes()))
        {
            return [
                ['label' => __('Disabled'), 'value' => 'disabled']
            ];
        }

        $selection = array();
        foreach ($this->config->getTransportTypes() as $transportType)
        {
            $selection[] = ['label' => __($transportType['label']), 'value' => $transportType['value']];
        }

        return $selection;
    }
}
