<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\Mail;
use Zend_Mime_Part;

/**
 * Mail Storeage interface
 *
 * @api
 */
interface AttachmentInterface
{
    /**
     * @return mixed
     */
    public function getBinary();

    /**
     * @param string
     * @return $this
     */
    public function setBinary($value);

    /**
     * @return mixed
     */
    public function getMimeType();

    /**
     * @return mixed
     */
    public function getMail();

    /**
     * @param string
     * @return $this
     */
    public function setMail($value);

    /**
     * Returns all data of attachment object, except binary data
     *
     * @return array
     */
    public function jsonSerialize();

    /**
     * @return Zend_Mime_Part
     */
    public function toMimePart();

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @return string
     */
    public function getDisposition();

    /**
     * @param string $value
     * @return $this
     */
    public function setDisposition($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setEncoding($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setHash($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMimeType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setFileName($value);
    
    /**
     * @param string $value
     * @return $this
     */
    public function setFilePath($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSize($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setUrl($value);
}
