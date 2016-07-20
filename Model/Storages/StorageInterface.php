<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Storages;
use Shockwavemk\Mail\Base\Model\Mail;
use Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface;

/**
 * Mail Storage interface
 *
 * @api
 */
interface StorageInterface
{
    /**
     * TODO
     *
     * @param Mail $mail
     *
     * @return $id
     * @throws \Magento\Framework\Exception\MailException
     */
    public function saveMessage($mail);

    /**
     * TODO
     *
     * @param Mail $mail
     * @return \Magento\Framework\Mail\MessageInterface
     */
    public function loadMessage($mail);

    /**
     * TODO
     *
     * @param Mail $mail
     * @return  $id
     */
    public function saveMail($mail);

    /**
     * TODO
     *
     * @param int $mailId
     *
     * @return Mail
     */
    public function loadMail($mailId);

    /**
     * TODO
     *
     * @param Mail $mail
     *
     * @return AttachmentInterface[]
     */
    public function getAttachments($mail);

    /**
     * TODO
     *
     * @param Mail $mail
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
     * @param Mail $mail
     * @return $this
     */
    public function saveAttachments($mail);
}
