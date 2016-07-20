<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Storages;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;
use Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

class Base implements StorageInterface
{
    /**
     * @var \Shockwavemk\Mail\Base\Model\Storages\StorageInterface
     */
    protected $_storage;

    /**
     * @param \Shockwavemk\Mail\Base\Model\Config $config
     * @param \Magento\Framework\ObjectManagerInterface $manager
     * @throws MailException
     */
    public function __construct(
        \Shockwavemk\Mail\Base\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $manager
    )
    {
        try {

            $storeageClassName = $config->getStoreageClassName();
            $this->_storage = $manager->get($storeageClassName);

        } catch(\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * Send a mail using this transport
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return  $id
     * @throws MailException
     *
     */
    public function saveMessage($mail)
    {
        try {

            return $this->_storage->saveMessage($mail);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * Send a mail using this transport
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail\Message
     * @throws MailException
     */
    public function loadMessage($mail)
    {
        try {

            return $this->_storage->loadMessage($mail);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @param AttachmentInterface $attachment
     * @return string $id
     * @throws MailException
     */
    public function saveAttachment($attachment)
    {
        try {

            return $this->_storage->saveAttachment($attachment);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface[]
     * @throws MailException
     */
    public function getAttachments($mail)
    {
        try {

            return $this->_storage->getAttachments($mail);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return $this
     * @throws MailException
     */
    public function saveAttachments($mail)
    {
        try {
            return $this->_storage->saveAttachments($mail);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return $id
     * @throws MailException
     */
    public function saveMail($mail)
    {
        try {

            return $this->_storage->saveMail($mail);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail
     * @throws MailException
     */
    public function loadMail($id)
    {
        try {

            return $this->_storage->loadMail($id);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @param string $path
     * @return AttachmentInterface
     * @throws MailException
     */
    public function loadAttachment($mail, $path)
    {
        try {

            return $this->_storage->loadAttachment($mail, $path);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }

    /**
     * TODO
     *
     * @return string
     * @throws MailException
     */
    public function getMailFolderPathById($mailId)
    {
        try {

            return $this->_storage->getMailFolderPathById($mailId);

        } catch (\Exception $e) {

            throw new MailException(
                new Phrase($e->getMessage()),
                $e)
            ;

        }
    }
}
