<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Shockwavemk\Mail\Model\Transports;

use Shockwavemk\Mail\Base\Model\Transports\Base;

class DebugTransport extends Base implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        // TODO
    }
}
