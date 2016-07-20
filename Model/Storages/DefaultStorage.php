<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Storages;

use DirectoryIterator;
use FilesystemIterator;
use IteratorIterator;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Shockwavemk\Mail\Base\Model\Config;
use SplFileObject;

class DefaultStorage implements StorageInterface
{
    const MESSAGE_FILE_NAME = 'message.json';
    const MESSAGE_HTML_FILE_NAME = 'message.html';
    const MAIL_FILE_NAME = 'mail.json';
    const ATTACHMENT_PATH = 'attachments';
    
    /** @var StorageInterface */
    protected $_storage;

    /** @var Config $_config */
    protected $_config;

    /** @var ObjectManagerInterface */
    protected $_objectManager;

    /**
     * DebugStorage constructor.
     *
     * @param Config $config
     * @param MessageInterface $message
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $config,
        MessageInterface $message,
        ObjectManagerInterface $objectManager
    )
    {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
    }

    /**
     * Save file to spool path
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function saveMessage($mail)
    {
        // convert message to html
        $messageHtml = quoted_printable_decode(
            $mail->getMessage()->getBodyHtml(true)
        );

        $this->createFolder(
            $this->getMailFolderPathById($mail->getId())
        );

        // try to store message to filesystem
        $this->storeFile(
            $messageHtml,
            $this->getMailFolderPathById($mail->getId()) .
                DIRECTORY_SEPARATOR . self::MESSAGE_HTML_FILE_NAME
        );

        // convert message to json
        $messageJson = json_encode($mail->getMessage());

        // try to store message to filesystem
        $this->storeFile(
            $messageJson,
            $this->getMailFolderPathById($mail->getId()) .
                DIRECTORY_SEPARATOR . self::MESSAGE_FILE_NAME
        );

        return $this;
    }

    /**
     * Create a folder path, if folder does not exist
     *
     * @param $folderPath
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function createFolder($folderPath)
    {
        if(!@mkdir($folderPath,0777,true) && !is_dir($folderPath))
        {
            throw new FileSystemException(
                new Phrase('Folder can not be created, but does not exist.')
            );
        }     
        
        return $this;
    }

    /**
     * Returns a string concat of magento config value for email spool path and mail id
     * e.g. pub/media/emails/<mailId>
     *
     * @param $mailId
     * @return string e.g. pub/media/emails/<mailId>
     */
    public function getMailFolderPathById($mailId)
    {
        // store message in temporary file system spooler
        $hostTempFolderPath = $this->_config->getHostSpoolerFolderPath();

        return $hostTempFolderPath . DIRECTORY_SEPARATOR . $mailId;
    }

    /**
     * Stores string data at a given path
     *
     * @param $data
     * @param $filePath
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    protected function storeFile($data, $filePath)
    {
        // create a folder for message if needed
        if (!is_dir(dirname($filePath))) {
            $this->createFolder(dirname($filePath));
        }

        /** @noinspection LoopWhichDoesNotLoopInspection */
        for ($i = 0; $i < $this->_config->getHostRetryLimit(); $i++) {
            /* We try an exclusive creation of the file.
             * This is an atomic operation, it avoid locking mechanism
             */
            $fp = @fopen($filePath, 'x');

            if($fp !== false) {
                if (false === fwrite($fp, $data)) {
                    return false;
                }

                fclose($fp);

                return $filePath;
            }
        }

        throw new FileSystemException(
            new Phrase('Unable to create a file for enqueuing Message')
        );
    }

    /**
     * Restore a message from filesystem
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return \Shockwavemk\Mail\Base\Model\Mail\Message $message
     * @throws \Exception
     * @throws \Zend_Mail_Exception
     */
    public function loadMessage($mail)
    {
        $localFilePath = $this->getMailFolderPathById($mail->getId()) .
            DIRECTORY_SEPARATOR . self::MESSAGE_FILE_NAME;

        $messageJson = $this->restoreFile($localFilePath);

        if (empty($messageJson)) {
            $messageJson = '{}';
        }

        $messageData = json_decode($messageJson);

        /** @var \Shockwavemk\Mail\Base\Model\Mail\Message $message */
        $message = $this->_objectManager->create('Shockwavemk\Mail\Base\Model\Mail\Message');

        if (!empty($messageData->type)) {
            $message->setType($messageData->type);
        }

        if (!empty($messageData->txt)) {
            $message->setBodyText($messageData->txt);
        }

        if (!empty($messageData->html)) {
            $message->setBodyHtml($messageData->html);
        }

        if (!empty($messageData->from)) {
            $message->setFrom($messageData->from);
        }

        if (!empty($messageData->subject)) {
            $message->setSubject($messageData->subject);
        }

        if (!empty($messageData->recipients)) {
            foreach ($messageData->recipients as $recipient) {
                $message->addTo($recipient);
            }
        }

        return $message;
    }

    /**
     * Load binary file data from a given file path
     *
     * @param $filePath
     * @return null|string
     */
    private function restoreFile($filePath)
    {
        try {
            for ($i = 0; $i < $this->_config->getHostRetryLimit(); ++$i) {
                /* We try an exclusive creation of the file. This is an atomic operation, it avoid locking mechanism */
                @fopen($filePath, 'x');

                if (false === $fileData = file_get_contents($filePath)) {
                    return null;
                }
                return $fileData;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Convert a mail object to json and store it at mail folder path
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function saveMail($mail)
    {
        // first save file to spool path
        // to avoid exceptions on external storage provider connection

        // convert message to json
        $mailJson = json_encode($mail);

        $this->createFolder(
            $this->getMailFolderPathById($mail->getId())
        );

        // try to store message to filesystem
        return $this->storeFile(
            $mailJson,
            $this->getMailFolderPathById($mail->getId()) .
                DIRECTORY_SEPARATOR . self::MAIL_FILE_NAME
        );
    }

    /**
     * Loads json file from storage and converts/parses it to a new mail object
     *
     * @param int $id folder name / id eg. pub/media/emails/66
     * @return \Shockwavemk\Mail\Base\Model\Mail
     * @throws \Exception
     */
    public function loadMail($id)
    {
        /** @var \Shockwavemk\Mail\Base\Model\Mail $mail */
        $mail = $this->_objectManager->create('Shockwavemk\Mail\Base\Model\Mail');
        
        $mail->setId($id);
        
        $localFilePath = $this->getMailFolderPathById(
            $mail->getId()) .
                DIRECTORY_SEPARATOR . self::MAIL_FILE_NAME;

        $mailJson = $this->restoreFile($localFilePath);

        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($mailJson)) {
            $mailJson = '{}';
        }

        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($mailData = json_decode($mailJson, true))) {
            foreach ($mailData as $key => $value) {
                $mail->setData($key, $value);
            }
        }

        return $mail;
    }

    /**
     * Load binary data from storage provider
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @param string $path
     * @return string
     */
    public function loadAttachment($mail, $path)
    {
        $attachmentFolder = DIRECTORY_SEPARATOR . self::ATTACHMENT_PATH;

        $localFilePath = $this->getMailFolderPathById($mail->getId()) .
            $attachmentFolder . $path;

        return $this->restoreFile($localFilePath);
    }

    /**
     * Returns attachments for a given mail
     *
     * \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail\Attachment[]
     */
    public function getAttachments($mail)
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if(empty($mail->getId())) {
            return [];
        }
        
        $folderFileList = $this->getMailLocalFolderFileList($mail);

        $attachments = [];
        
        foreach ($folderFileList as $filePath => $fileMetaData) {

            $filePath = $fileMetaData['path'];

            /** @var \Shockwavemk\Mail\Base\Model\Mail\Attachment $attachment */
            $attachment = $this->_objectManager
                ->create('\Shockwavemk\Mail\Base\Model\Mail\Attachment');

            $attachment->setFilePath($filePath);
            $attachment->setMail($mail);

            // transfer all meta data into attachment object
            foreach ($fileMetaData as $attributeKey => $attributeValue) {
                $attachment->setData($attributeKey, $attributeValue);
            }

            $attachments[$filePath] = $attachment;
        }

        return $attachments;
    }

    /**
     * Get file list for a given mail
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return array
     */
    protected function getMailLocalFolderFileList($mail)
    {
        $spoolFolder = $this->_config->getHostSpoolerFolderPath() .
            DIRECTORY_SEPARATOR .
            $mail->getId() .
            DIRECTORY_SEPARATOR .
            self::ATTACHMENT_PATH;

        // create a folder for attachments if needed
        if (!is_dir($spoolFolder)) {
            $this->createFolder($spoolFolder);
        }

        /** @var RecursiveIteratorIterator $objects */
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($spoolFolder),
            RecursiveIteratorIterator::LEAVES_ONLY,
            FilesystemIterator::SKIP_DOTS
        );

        /** @var array $files */
        $files = [];

        /**
         * @var string $name
         * @var SplFileObject $object
         */
        foreach ($objects as $path => $object) {
            /** @noinspection TypeUnsafeComparisonInspection */
            /** @noinspection NotOptimalIfConditionsInspection */
            if ($object->getFilename() != '.' && $object->getFilename() != '..') {
                $filePath = str_replace($spoolFolder, '', $path);
                $file = [
                    'name' => $object->getFilename(),
                    'path' => $path
                ];
                $files[$filePath] = $file;
            }
        }

        return $files;
    }

    /**
     * Save all attachments of a given mail
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Exception
     */
    public function saveAttachments($mail)
    {
        /** @var \Shockwavemk\Mail\Base\Model\Mail\Attachment[] $attachments */
        $attachments = $mail->getAttachments();

        foreach ($attachments as $attachment) {
            $this->saveAttachment($attachment);
        }

        return $this;
    }

    /**
     * Save an attachment binary to a file in host temp folder
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail\Attachment $attachment
     * @return int $id
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Exception
     */
    public function saveAttachment($attachment)
    {
        $binaryData = $attachment->getBinary();
        /** @var \Shockwavemk\Mail\Base\Model\Mail $mail */
        $mail = $attachment->getMail();

        $folderPath = $this->getMailFolderPathById($mail->getId()) .
            DIRECTORY_SEPARATOR .
            self::ATTACHMENT_PATH;

        // create a folder for message if needed
        $this->createFolder($folderPath);

        // try to store message to filesystem
        $filePath = $this->storeFile(
            $binaryData,
            $this->getMailFolderPathById($mail->getId()) .
                DIRECTORY_SEPARATOR .
                self::ATTACHMENT_PATH .
                DIRECTORY_SEPARATOR .
                basename($attachment->getFilePath())
        );
        
        $attachment->setFilePath($filePath);
    }

    /**
     * Get Folder list for host spooler folder path
     *
     * @return string[]
     */
    public function getRootLocalFolderFileList()
    {
        $spoolFolder = $this->_config->getHostSpoolerFolderPath();

        /** @var IteratorIterator $objects */
        $objects = new IteratorIterator(
            new DirectoryIterator($spoolFolder)
        );

        /** @var array $files */
        $files = [];

        /**
         * @var string $name
         * @var SplFileObject $object
         */
        foreach ($objects as $path => $object) {
            /** @noinspection TypeUnsafeComparisonInspection */
            /** @noinspection NotOptimalIfConditionsInspection */
            if ($object->getFilename() != '.' && $object->getFilename() != '..') {
                /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
                $shortFilePath = str_replace($spoolFolder, '', $object->getPathName());

                /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
                $files[] = [
                    'name' => $object->getFilename(),
                    'localPath' => $object->getPathName(),
                    'remotePath' => $shortFilePath,
                    'modified' => $object->getMTime()
                ];
            }
        }

        return $files;
    }

    /**
     * Get list of files in a local file path
     *
     * @param $localPath
     * @return string[]
     */
    public function getLocalFileListForPath($localPath)
    {
        $files = [];
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localPath),
            RecursiveIteratorIterator::LEAVES_ONLY,
            FilesystemIterator::SKIP_DOTS
        );

        /**
         * @var string $path
         * @var SplFileObject $object
         */
        foreach ($objects as $path => $object) {
            /** @noinspection TypeUnsafeComparisonInspection */
            /** @noinspection NotOptimalIfConditionsInspection */
            if ($object->getFilename() != '.' && $object->getFilename() != '..') {
                $hostTempFolderPath = $this->_config->getHostSpoolerFolderPath();

                /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
                $remoteFilePath = str_replace($hostTempFolderPath, '/', $object->getPathName());


                /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
                $file = [
                    'name' => $object->getFilename(),
                    'localPath' => $object->getPathName(),
                    'remotePath' => $remoteFilePath,
                    'modified' => $object->getMTime()
                ];

                $files[$object->getFilename()] = $file;
            }
        }

        return $files;
    }

    /**
     * Deletes all local stored files
     *
     * @param $localPath
     * @return \Shockwavemk\Mail\Base\Model\Storages\DefaultStorage
     */
    public function deleteLocalFiles($localPath)
    {
        $it = new RecursiveDirectoryIterator(
            $localPath,
            RecursiveDirectoryIterator::SKIP_DOTS
        );

        $files = new RecursiveIteratorIterator(
            $it,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($localPath);
    }
}
