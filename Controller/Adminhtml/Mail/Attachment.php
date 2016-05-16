<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Controller\Adminhtml\Mail;

use Magento\Downloadable\Helper\File;
use Magento\Downloadable\Helper\Download as DownloadHelper;

class Attachment extends \Shockwavemk\Mail\Base\Controller\Adminhtml\Mail
{
    /**
     * Preview Newsletter template
     *
     * @return void|$this
     */
    public function execute()
    {
        $encodedId = $this->_request->getParam('id');

        /** @var \Shockwavemk\Mail\Base\Model\Mail $_mail */
        $mail = $this->_objectManager->get('\Shockwavemk\Mail\Base\Model\Mail');
        $mail->load(base64_decode($encodedId));

        $encodedPath = $this->_request->getParam('path');
        $localPath = base64_decode($encodedPath);

        /** @var \Shockwavemk\Mail\Base\Model\Mail\Attachment[] $attachments */
        $attachments = $mail->getAttachments();

        /** @var \Shockwavemk\Mail\Base\Model\Mail\Attachment $attachment */
        if(empty($attachment = $attachments[$localPath])) {

            $this->messageManager->addError(
                __('Sorry, there was an error getting requested content. Please contact the store owner.')
            );

            return $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }

        try
        {
            $this->_processDownload($attachment);
            exit(0);
        }
        catch (\Exception $e)
        {
            $this->messageManager->addError(
                __('Sorry, there was an error getting requested content. Please contact the store owner: ' . $e->getMessage())
            );
        }

        return $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * @param \Shockwavemk\Mail\Base\Model\Mail\Attachment $attachment
     */
    protected function _processDownload($attachment)
    {
        /** @var \Magento\Framework\App\ResponseInterface $response */
        $response = $this->getResponse();

        $response->setHttpResponseCode(
            200
        )->setHeader(
            'Pragma',
            'public',
            true
        )->setHeader(
            'Cache-Control',
            'must-revalidate, post-check=0, pre-check=0',
            true
        )->setHeader(
            'Content-type',
            $attachment->getFileType(),
            true
        );

        $this->getResponse()->setHeader('Content-Disposition', $attachment->getDisposition() . '; filename=' .  basename($attachment->getFilePath()));

        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();

        echo $attachment->getBinary();
    }
}
