<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Storeages;
use Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface;
use Shockwavemk\Mail\Base\Model\Mail\MessageInterface;

/**
 * Mail Storeage interface
 *
 * @api
 */
interface StoreageInterface
{
    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return $id
     * @throws \Magento\Framework\Exception\MailException
     */
    public function saveMessage($mail);

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return \Magento\Framework\Mail\MessageInterface
     */
    public function loadMessage($mail);

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return  $id
     */
    public function saveMail($mail);

    /**
     * TODO
     *
     * @param int $mailId
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function loadMail($mailId);

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return AttachmentInterface[]
     */
    public function getAttachments($mail);

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @param string $path
     *
     * @return AttachmentInterface
     */
    public function loadAttachment($mail, $path);

    /**
     * @param AttachmentInterface $attachment
     *
     * @return string $path
     */
    public function saveAttachment($attachment);

    /**
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return $this
     */
    public function saveAttachments($mail);
}
