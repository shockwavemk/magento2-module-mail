<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Mail;

use JsonSerializable;

class Message extends \Magento\Framework\Mail\Message implements JsonSerializable
{
    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array(
            'type' => $this->getType(),
            'bodyText' => $this->getBodyText(),
            'bodyHtml' => $this->getBodyHtml(),
            'recipients' => $this->getRecipients(),
            'from'    => $this->getFrom(),
            'subject' => $this->getSubject(),
            'text'    => quoted_printable_decode($this->getBodyText(true)),
            'html'    => quoted_printable_decode($this->getBodyHtml(true))
        );
    }
}
