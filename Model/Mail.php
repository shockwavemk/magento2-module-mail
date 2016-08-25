<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model;

use JsonSerializable;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface;
use Shockwavemk\Mail\Base\Model\ResourceModel\Mail as ResourceMail;
use Shockwavemk\Mail\Base\Model\ResourceModel\Mail\Collection;
use stdClass;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

/**
 * Mail model
 *
 * @method \Shockwavemk\Mail\Base\Model\ResourceModel\Mail _getResource()
 * @method \Shockwavemk\Mail\Base\Model\ResourceModel\Mail getResource()
 *
 * @method stdClass getResult() - will not be saved
 * @method stdClass getAdditionalInlines() - will not be saved
 * @method stdClass getAdditionalMessages() - will not be saved
 *
 * @method int getId()
 * @method string getSubject()
 * @method int getCustomerId()
 * @method string getTemplateIdentifier()
 * @method string getTemplateModel()
 * @method int getStoreId()
 * @method bool getSent()
 * @method string getLanguageCode()
 * @method array getRecipientVariables()
 * @method string getTransportId()
 * @method string getParentId()
 * @method string getCc()
 * @method string getBcc()
 * @method string getMultipartAttachment()
 * @method string getMultipartInline()
 * @method string getCampaign()
 * @method bool getDkimEnabled()
 * @method bool getRequireTlsEnabled()
 * @method bool getSkipVerificationEnabled()
 * @method string[] getCustomHeaders()
 * @method string[] getCustomVariables()
 *
 *
 * @method \Shockwavemk\Mail\Base\Model\Mail setSubject(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setCustomerId(int $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTemplateIdentifier(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTemplateModel(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setStoreId(int $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setSent(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setSentAt(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setLanguageCode(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setRecipientVariables(array $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setMessage(\Shockwavemk\Mail\Base\Model\Mail\MessageInterface $message)
 * @method \Shockwavemk\Mail\Base\Model\Mail setAttachments(AttachmentInterface[] $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setAdditionalMessages(array $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setAdditionalInlines(array $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setDeliveryTime(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTestMode(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTrackingEnabled(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTrackingClicksEnabled(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTrackingOpensEnabled(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setTransportId(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setParentId(int $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setCc(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setBcc(string $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setMultipartAttachment($value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setMultipartInline($value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setCampaign($value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setDkimEnabled($value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setRequireTlsEnabled(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setSkipVerificationEnabled(bool $value)
 * @method \Shockwavemk\Mail\Base\Model\Mail setCustomHeaders(array $values)
 * @method \Shockwavemk\Mail\Base\Model\Mail setCustomVariables(array $values)
 *
 * @method \Shockwavemk\Mail\Base\Model\Mail setResult(stdClass $value) - will not be saved
 *
 * @property TimezoneInterface _timeZone
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

/** @noinspection ClassOverridesFieldOfSuperClassInspection */
class Mail extends \Magento\Framework\Model\AbstractModel implements JsonSerializable
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'mail';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mail';

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var \Shockwavemk\Mail\Base\Model\Storages\Base */
    protected $_storage;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $_date;

    /** @var \Magento\Framework\Math\Random */
    protected $_mathRandom;

    /** @var \Magento\Framework\Stdlib\DateTime */
    protected $_dateTime;

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manager;

    /** @var Config */
    protected $_config;

    /** @var TimezoneInterface */
    protected $_timeZone;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceMail $resource
     * @param Collection $resourceCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param TimezoneInterface $timezone
     * @param \Magento\Framework\ObjectManagerInterface $manager
     * @param Config $config
     * @param Storages\Base $storage
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    /** @noinspection MoreThanThreeArgumentsInspection
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceMail $resource
     * @param Collection $resourceCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param TimezoneInterface $timezone
     * @param \Magento\Framework\ObjectManagerInterface $manager
     * @param Config $config
     * @param Storages\Base $storage
     * @param array $data
     */
    public function __construct(
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceMail $resource,
        Collection $resourceCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        TimezoneInterface $timezone,
        \Magento\Framework\ObjectManagerInterface $manager,
        \Shockwavemk\Mail\Base\Model\Config $config,
        \Shockwavemk\Mail\Base\Model\Storages\Base $storage,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        $this->_date = $date;
        $this->_mathRandom = $mathRandom;
        $this->_dateTime = $dateTime;
        $this->_manager = $manager;
        $this->_config = $config;
        $this->_storage = $storage;
        $this->_timeZone = $timezone;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return Storages\Base
     */
    public function getstorage()
    {
        return $this->_storage;
    }

    /**
     * Every mailing is associated to a 'real' message
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail\Message|null
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getMessage()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        if (!empty(parent::getMessage())) {
            /** @noinspection PhpUndefinedMethodInspection */
            return parent::getMessage();
        }

        return $this->_storage->loadMessage(
            $this
        );
    }

    /**
     * @param \Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface $attachment
     * @throws \Magento\Framework\Exception\MailException
     */
    public function addAttachment($attachment)
    {
        /** @var \Shockwavemk\Mail\Base\Model\Mail\AttachmentInterface[] $currentAttachments */
        $attachments = $this->getAttachments();
        $attachment->setMail($this);

        $attachments[$attachment->getFileName()] = $attachment;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->setAttachments($attachments);
    }

    /**
     * A mail can have zero to n associated attachments.
     * An Attachment is a meta description to get information and access to binary data
     *
     * @return \Shockwavemk\Mail\Base\Model\Mail\Attachment[] Attachments
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getAttachments()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        if (!empty(parent::getAttachments())) {
            /** @noinspection PhpUndefinedMethodInspection */
            return parent::getAttachments();
        }

        $attachments = $this->_storage->getAttachments($this);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->setAttachments($attachments);

        return $attachments;
    }

    /**
     * TODO
     *
     * @return string[]
     */
    public function getRecipients()
    {
        $value = $this->getData('recipients');
        return json_decode($value, true);
    }

    /**
     * @param array $value
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function setSenderMail(array $value)
    {
        $value = json_encode($value);
        $this->setData('sender_mail', $value);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSenderMail()
    {
        $value = $this->getData('sender_mail');
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($decoded = json_decode($value, true))) {
            return $decoded;
        }
        return '';
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setVars(array $value)
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($value)) {
            $value = $this->convertMagentoModelsToPointer($value);
        }

        $value = json_encode($value);
        $this->setData('vars', $value);
        return $this;
    }

    /**
     * @param $mailValues
     * @return array
     */
    protected function convertMagentoModelsToPointer($mailValues)
    {
        $newMailValues = [];

        foreach ($mailValues as $key => $value) {
            if (is_subclass_of($value, 'Magento\Framework\Model\AbstractModel')) {
                /** @var \Magento\Framework\Model\AbstractModel $value */
                $value = array(
                    'entity_id' => $value->getId(),
                    'class' => get_class($value)
                );
            }
            /** @noinspection IsEmptyFunctionUsageInspection */
            elseif(method_exists($value, 'getData') && !empty($value->getData())) {
                $valueData = $value->getData();

                /** @noinspection IsEmptyFunctionUsageInspection */
                if(method_exists($value, 'getId') && !empty($value->getId())) {
                    $valueData['entity_id'] = $value->getId();
                }

                $valueData['class'] = get_class($value);
                $value = $valueData;
            }

            $newMailValues[$key] = $value;
        }

        return $newMailValues;
    }

    /**
     * @return array|mixed
     */
    public function getVars()
    {
        $value = $this->getData('vars');

        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($value)) {
            $value = json_decode($value, true);
            $value = $this->convertPointerToMagentoModels($value);
        }

        return $value;
    }

    /**
     * Recover magento models from stored class/ids tuple
     *
     * @param $mailValues
     * @return \Magento\Framework\Model\AbstractModel[]|array
     */
    protected function convertPointerToMagentoModels($mailValues)
    {
        $newMailValues = [];

        foreach ($mailValues as $key => $value) {
            if (is_array($value) && !empty($value['class']) && !empty($value['entity_id'])) {
                /** @var \Magento\Framework\Model\AbstractModel $value */
                $newValue = $this->_manager->get($value['class']);
                $newValue->load($value['entity_id']);
                $newMailValues[$key] = $newValue;
            }
            /** @var \Magento\Rma\Model\ResourceModel\Item\Collection $newValue */
            elseif(is_array($value) && !empty($value['class'])) {
                $newValue = $this->_manager->get($value['class']);
                unset($value['class']);
                if(!empty($value)) {
                    foreach ($value as $item) {
                        /** @var \Magento\Rma\Model\Item $entity */
                        $entity = $newValue->getNewEmptyItem();
                        $entity->addData($item);
                        $newValue->addItem($entity);
                    }
                }                
            }
            else {
                $newMailValues[$key] = $value;
            }
        }

        return $newMailValues;
    }

    /**
     * Options are stored as json string in database
     *
     * @param array $value
     * @return $this
     */
    public function setOptions(array $value)
    {
        $value = json_encode($value);
        $this->setData('options', $value);
        return $this;
    }

    /**
     * Options are stored as json string in database
     *
     * @return mixed
     */
    public function getOptions()
    {
        $value = $this->getData('options');
        return json_decode($value, true);
    }

    /**
     * Set tags used for mail
     * Tags are stored as json string in database
     *
     * @param $value
     * @return $this
     */
    public function setTags($value)
    {
        $value = json_encode($value);
        $this->setData('tags', $value);
        return $this;
    }

    /**
     * Get tags used for mail
     * Tags are stored as json string in database
     *
     * @return mixed
     */
    public function getTags()
    {
        $value = $this->getData('tags');
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($decoded = json_decode($value, true))) {
            return $decoded;
        }
        return [];
    }

    /**
     * Update mail data with information created while transport init
     *
     * @param \Shockwavemk\Mail\Base\Model\Transports\TransportInterface $transport
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function updateWithTransport($transport)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this
            ->setSubject($transport->getMessage()->getSubject())
            ->setMessage($transport->getMessage())
            ->setRecipients($transport->getMessage()->getRecipients())
            ->setId(null);

        return $this;
    }

    /**
     * Set recipients of mail
     *
     * @param string[] $value
     * @return \Shockwavemk\Mail\Base\Model\Mail
     */
    public function setRecipients(array $value)
    {
        $value = json_encode($value);
        $this->setData('recipients', $value);
        return $this;
    }

    /**
     * Save mail to db, message and attachments to storage
     *
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Exception
     */
    public function save()
    {
        parent::save();

        $this->_storage->saveMessage($this);

        $this->_storage->saveAttachments($this);

        $this->_storage->saveMail($this);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * @throws \Magento\Framework\Exception\MailException
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data = $this->getData();
        $attachmentsSerialized = [];

        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($this->getAttachments())) {
            foreach ($this->getAttachments() as $attachment) {
                $attachmentsSerialized[] = $attachment->jsonSerialize();
            }
        }

        $data['attachments'] = $attachmentsSerialized;

        return $data;
    }

    /**
     * @return string
     */
    public function getDeliveryTime()
    {
        $deliveryTime = $this->getData('delivery_time');
        $dateTime = $this->_timeZone->date($deliveryTime);

        return $dateTime->format('D, d M Y H:i:s O');
    }

    /**
     * @return string
     */
    public function getSentAt()
    {
        $sentAt = $this->getData('sent_at');
        $dateTime = $this->_timeZone->date($sentAt);

        return $dateTime->format('D, d M Y H:i:s O');
    }

    /**
     * @return string
     */
    public function getTestMode()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($value = $this->getData('test_mode'))) {
            $value = $this->_config->getTestMode();
            $this->setData('test_mode', $value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getTrackingEnabled()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($value = $this->getData('tracking_enabled'))) {
            $value = $this->_config->getTrackingEnabled();
            $this->setData('tracking_enabled', $value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getTrackingClicksEnabled()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($value = $this->getData('tracking_clicks_enabled'))) {
            $value = $this->_config->getTrackingClicksEnabled();
            $this->setData('tracking_clicks_enabled', $value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getTrackingOpensEnabled()
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($value = $this->getData('tracking_opens_enabled'))) {
            $value = $this->_config->getTrackingOpensEnabled();
            $this->setData('tracking_opens_enabled', $value);
        }

        return $value;
    }
}
