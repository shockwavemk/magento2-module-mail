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
    /** @var \Shockwavemk\Mail\Base\Model\Mail */
    protected $_mail;

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
        $mailId = $this->_request->getParam('id');
        $recalculate = $this->_request->getParam('resend_type');
        $email = $this->_request->getParam('email');

        $this->_mail = $this->_objectManager->get('Shockwavemk\Mail\Base\Model\Mail');
        $this->_mail->load($mailId);

        if(empty($mailId) || empty($this->_mail->getId())) {

            $redirectUrl = $this->_buildUrl(
                'customer/mail/edit',
                ['_secure' => true, 'id' => $mailId]
            );

            $this->messageManager->addException(new \Exception(
                __('Mail can not be loaded.')),
                __('Mail can not be loaded.')
            );

            return $this->resultRedirectFactory
                ->create()
                ->setUrl(
                    $this->_redirect->error($redirectUrl)
                );
        }

        try {

            $this->_mail->setParentId(
                $this->_mail->getId()
            );

            /** @var MessageInterface $message */
            $message = $this->_mail->getMessage();

            $this->_mail->getAttachments();

            $this->_mail->setId(null);

            if(!empty($email)) {
                $recipients = [$email];
            } else {
                $recipients = $this->_mail->getRecipients();
            }

            $transportBuilder =  $this->getTransportBuilderTemplate();

            foreach($recipients as $recipient) {
                $transportBuilder->addTo(
                    $recipient
                );
            }

            if(!empty($recalculate) && $recalculate == 'recalculate') {
                $transportBuilder->getTransport()
                    ->sendMessage();
            } else {
                $transportBuilder->getBackupTransport($message)
                    ->sendMessage();
            }

            $this->messageManager->addSuccess(__('Mail re-sent to customer.'));

            $url = $this->_buildUrl(
                'customer/mail/edit',
                ['_secure' => true,
                    'id' => $this->_mail->getId()]
            );

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));

        } catch (InputException $e) {

            $this->messageManager->addError($e->getMessage());

            foreach ($e->getErrors() as $error) {

                $this->messageManager->addError(
                    $error->getMessage() . ': ' . $error->getTraceAsString()
                );

            }

        } catch (\Exception $e) {

            $this->messageManager->addException(
                $e,
                __($e->getMessage() . ': ' . $e->getTraceAsString())
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
     * @return TransportBuilder
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function getTransportBuilderTemplate()
    {
        return $this->transportBuilder
            ->setTemplateIdentifier($this->_mail->getTemplateIdentifier())
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $this->_mail->getStoreId()])
            ->setTemplateVars($this->_mail->getVars())
            ->setFrom($this->_mail->getSenderMail());
    }
}
