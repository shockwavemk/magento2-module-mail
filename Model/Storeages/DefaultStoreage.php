<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Storeages;

use DirectoryIterator;
use FilesystemIterator;
use IteratorIterator;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

class DefaultStoreage implements StoreageInterface
{
    const MESSAGE_FILE_NAME = 'message.json';
    const MESSAGE_HTML_FILE_NAME = 'message.html';
    const MAIL_FILE_NAME = 'mail.json';
    const ATTACHMENT_PATH = 'attachments';
    
    /** @var StoreageInterface */
    protected $_storage;

    /** @var \Shockwavemk\Mail\Base\Model\Config $_config */
    protected $_config;

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_objectManager;

    /**
     * DebugStoreage constructor.
     *
     * @param \Shockwavemk\Mail\Base\Model\Config $config
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Shockwavemk\Mail\Base\Model\Config $config,
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\ObjectManagerInterface $objectManager
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
     * @throws \Exception
     */
    public function saveMessage($mail)
    {
        // convert message to html
        $messageHtml = quoted_printable_decode($mail->getMessage()->getBodyHtml(true));

        $this->prepareFolderPath(
            $this->getMailLocalFilePath($mail,'')
        );

        // try to store message to filesystem
        $this->storeFile(
            $messageHtml,
            $this->getMailLocalFilePath($mail, DIRECTORY_SEPARATOR . self::MESSAGE_HTML_FILE_NAME)
        );

        // convert message to json
        $messageJson = json_encode($mail->getMessage());

        // try to store message to filesystem
        $this->storeFile(
            $messageJson,
            $this->getMailLocalFilePath($mail, DIRECTORY_SEPARATOR . self::MESSAGE_FILE_NAME)
        );

        return $this;
    }

    protected function prepareFolderPath($folderPath)
    {
        // create a folder for message if needed
        if (!is_dir($folderPath)) {
            $this->createFolder($folderPath);
        }
    }

    /**
     * TODO
     *
     * @param $folderPath
     * @return mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function createFolder($folderPath)
    {
        if(!@mkdir($folderPath,0777,true) && !is_dir($folderPath)) 
        { 
            throw new FileSystemException(
                new Phrase('Folder can not be created, but does not exist.')
            ); 
        }        
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return string
     */
    public function getMailFolderPathById($mailId)
    {
        // store message in temporary file system spooler
        $hostTempFolderPath = $this->_config->getHostSpoolerFolderPath();

        return $hostTempFolderPath . DIRECTORY_SEPARATOR . $mailId;
    }

    /**
     * TODO
     *
     * @param $data
     * @param $filePath
     * @return bool
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

        throw new \Exception('Unable to create a file for enqueuing Message');
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @param $path
     * @return string
     */
    protected function getMailLocalFilePath($mail, $path)
    {
        $hostSpoolerFolderPath = $this->_config->getHostSpoolerFolderPath();

        $folderPath = $hostSpoolerFolderPath . DIRECTORY_SEPARATOR . $mail->getId();

        return $folderPath . $path;
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
        $localFilePath = $this->getMailLocalFilePath($mail, DIRECTORY_SEPARATOR . self::MESSAGE_FILE_NAME);

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
            return null; // TODO
        }

        return null;
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @return bool
     * @throws \Exception
     */
    public function saveMail($mail)
    {
        // first save file to spool path
        // to avoid exceptions on external storeage provider connection

        // convert message to json
        $mailJson = json_encode($mail);

        $this->prepareFolderPath(
            $this->getMailFolderPathById($mail->getId())
        );

        // try to store message to filesystem
        return $this->storeFile(
            $mailJson,
            $this->getMailLocalFilePath($mail, DIRECTORY_SEPARATOR . self::MAIL_FILE_NAME)
        );
    }

    /**
     * TODO
     *
     * @param int $id
     * @return \Shockwavemk\Mail\Base\Model\Mail
     * @throws \Exception
     */
    public function loadMail($id)
    {
        /** @var \Shockwavemk\Mail\Base\Model\Mail $mail */
        $mail = $this->_objectManager->create('Shockwavemk\Mail\Base\Model\Mail');
        
        $mail->setId($id);
        
        $localFilePath = $this->getMailLocalFilePath($mail, DIRECTORY_SEPARATOR . self::MAIL_FILE_NAME);

        $mailJson = $this->restoreFile($localFilePath);

        if (empty($mailJson)) {
            $mailJson = '{}';
        }

        $mailData = json_decode($mailJson);

        if (!empty($mailData)) {
            $a = 1;
        }

        return $mail;
    }

    /**
     * Load binary data from storeage provider
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     * @param string $path
     * @return string
     */
    public function loadAttachment($mail, $path)
    {
        $attachmentFolder = DIRECTORY_SEPARATOR . self::ATTACHMENT_PATH;

        $localFilePath = $this->getMailLocalFilePath($mail, $attachmentFolder . $path);

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
        // get combined files list: remote and local

        $folderFileList = $this->getMailLocalFolderFileList($mail);

        $attachments = [];

        if(empty($mail->getId())) {
            return $attachments;
        }

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
    private function getMailLocalFolderFileList($mail)
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
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Exception
     */
    public function saveAttachment($attachment)
    {
        $binaryData = $attachment->getBinary();
        $mail = $attachment->getMail();

        $folderPath = $this->getMailFolderPathById($mail->getId()) .
            DIRECTORY_SEPARATOR .
            self::ATTACHMENT_PATH;

        // create a folder for message if needed
        $this->prepareFolderPath($folderPath);

        // try to store message to filesystem
        $filePath = $this->storeFile(
            $binaryData,
            $this->getMailLocalFilePath(
                $mail,
                DIRECTORY_SEPARATOR .
                self::ATTACHMENT_PATH .
                DIRECTORY_SEPARATOR .
                basename($attachment->getFilePath())
            )
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
     * @return \Shockwavemk\Mail\Base\Model\Storeages\DefaultStoreage
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
