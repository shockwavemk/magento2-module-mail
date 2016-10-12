<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Controller\Adminhtml\Mail;

use Magento\Framework\Exception\InputException;

use Magento\Customer\Model\Customer;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface;
use Shockwavemk\Mail\Base\Model\Mail\MessageInterface;
use Shockwavemk\Mail\Base\Model\Template\TransportBuilder;

class SendPost extends \Shockwavemk\Mail\Base\Controller\Adminhtml\Mail
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @param Context|\Magento\Framework\App\Action\Context $context
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Customer $customer
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Customer $customer
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->customer = $customer;

        parent::__construct($context);
    }

    /**
     * Resend mail
     *
     * @return void|$this
     */
    public function execute()
    {
        // Get request data
        $mailId = $this->_request->getParam('id');
        $recalculate = $this->_request->getParam('resend_type');

        // Email address to re-send mail
        $email = $this->_request->getParam('email');

        /** @var \Shockwavemk\Mail\Base\Model\Mail $mail */
        $mail = $this->_objectManager->get('Shockwavemk\Mail\Base\Model\Mail');
        $mail->load($mailId);

        /** @noinspection IsEmptyFunctionUsageInspection */
        if(empty($mailId) || empty($mail->getId()) || empty($email) || empty($recalculate)) {

            $redirectUrl = $this->_buildUrl(
                'customer/mail/edit',
                ['_secure' => true, 'id' => $mailId]
            );

            $this->messageManager->addException(new \Exception(
                __('Please provide all data for resending.')),
                __('Please provide all data for resending.')
            );

            return $this->resultRedirectFactory
                ->create()
                ->setUrl(
                    $this->_redirect->error($redirectUrl)
                );
        }

        try
        {
            // Get information of parent mail

            /** @var MessageInterface $parentMessage */
            $parentMessage = $mail->getMessage();

            /** @var AttachmentInterface[] $parentAttachments */
            $parentAttachments = $mail->getAttachments();

            // Set parent id to allow parent links in frontend
            $mail->setParentId(
                $mail->getId()
            );

            // On given $email
            if(!empty($email)) {
                $recipients = [$email];
            } else {
                $recipients = $mail->getRecipients();
            }
            
            // Derive a transportBuilder from existing Mail
            $transportBuilder =  $this->deriveTransportBuilderFromExistingMail($mail);

            $this->applyRecipientsOnTransportBuilder($recipients, $transportBuilder);


            /** @noinspection IsEmptyFunctionUsageInspection */
            if(!empty($recalculate) && $recalculate === 'recalculate') {
                $transport = $transportBuilder->getTransport();
            } else {
                $transport = $transportBuilder->getBackupTransport($parentMessage);
            }

            $transport->getMail()->setAttachments($parentAttachments);

            /** @var \Shockwavemk\Mail\Base\Model\Transports\TransportInterface $transport */
            $transport->sendMessage();

            $this->messageManager->addSuccess(__('Mail re-sent to customer.'));

            $url = $this->_buildUrl(
                'customer/mail/edit',
                [
                    '_secure' => true,
                    'id' => $transport->getMail()->getId()
                ]
            );

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));

        }
        catch (InputException $e)
        {

            $this->messageManager->addError($e->getMessage());

            foreach ($e->getErrors() as $error) {

                $this->messageManager->addError(
                    $error->getMessage()
                );

            }

        }
        catch (\Exception $e)
        {

            $this->messageManager->addException(
                $e,
                __($e->getMessage())
            );

        }

        $redirectUrl = $this->_buildUrl(
            'customer/mail/send',
            ['_secure' => true, 'id' => $mailId]
        );

        return $this->resultRedirectFactory->create()->setUrl(
            $this->_redirect->error($redirectUrl)
        );
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl($route = '', $params = [])
    {
        /** @var \Magento\Framework\UrlInterface $urlBuilder */
        $urlBuilder = $this->_objectManager->create('Magento\Framework\UrlInterface');
        return $urlBuilder->getUrl($route, $params);
    }

    /**
     * TODO
     *
     * @param \Shockwavemk\Mail\Base\Model\Mail $mail
     *
     * @return TransportBuilder
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function deriveTransportBuilderFromExistingMail($mail)
    {
        return $this->transportBuilder
            ->setTemplateIdentifier($mail->getTemplateIdentifier())
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $mail->getStoreId()])
            ->setTemplateVars($mail->getVars())
            ->setFrom($mail->getSenderMail());
    }

    /**
     * @param $recipients
     * @param $transportBuilder
     */
    protected function applyRecipientsOnTransportBuilder($recipients, $transportBuilder)
    {
        // Some times magento does not save recipients as array
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                $transportBuilder->addTo(
                    $recipient
                );
            }
        } else {
            $transportBuilder->addTo(
                $recipients
            );
        }
    }
}
