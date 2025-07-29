<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_OrderComment
 * @copyright   Copyright (c) 2025 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

declare(strict_types=1);

namespace M2Commerce\ProductEnquiry\Controller\Index;

use Exception;
use M2Commerce\ProductEnquiry\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;


class Index extends Action
{
    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Escaper
     */
    protected $_escaper;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var
     */
    private $lookingforConfig;
    /**
     * @var JsonFactory
     */
    private $_jsonFactory;

    /**
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     * @param JsonFactory $jsonFactory
     * @param Data $helper
     * @param ManagerInterface $messageManager
     */
    public function __construct(Context $context, TransportBuilder $transportBuilder, StateInterface $inlineTranslation, ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, Escaper $escaper, JsonFactory $jsonFactory, Data $helper, ManagerInterface $messageManager)
    {
        parent::__construct($context);
        $this->_jsonFactory = $jsonFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_helper = $helper;
        $this->messageManager = $messageManager;

    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Redirect|ResultInterface
     */
    public function execute()
    {
        $refererUrl = $this->_redirect->getRefererUrl();

        $comment = $this->getRequest()->getParam('comment');

        if (!$comment) {
            $this->messageManager->addErrorMessage(__('We were unable to submit your request.'));
        } else {
            $this->_sendEmail($comment);
            $this->messageManager->addSuccessMessage(__('Thank you for submitting your request: "%1"', $comment));
        }

        return $this->resultRedirectFactory->create()->setUrl($refererUrl);
    }

    /**
     * @param $comment
     * @return bool
     */
    public function _sendEmail($comment)
    {
        $result = true;

        try {
            $recipientEmail = $this->_helper->getConfigData('productenquiry/email/recipient_email');
            if (!$recipientEmail) {
                throw new Exception("Recipient email is not configured.");
            }
            $senderIdentity = $this->_helper->getConfigData('productenquiry/email/sender_email_identity');
            $sender = ['name' => $this->scopeConfig->getValue("trans_email/ident_{$senderIdentity}/name"), 'email' => $this->scopeConfig->getValue("trans_email/ident_{$senderIdentity}/email")];
            $templateId = $this->_helper->getConfigData('productenquiry/email/email_template');
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID,])->setTemplateVars(['comment' => $comment])->setFromByScope($sender)->addTo($recipientEmail)->getTransport();
            $transport->sendMessage();

        } catch (Exception $e) {
            $result = false;
            // Optionally log: error_log($e->getMessage());
        }

        return $result;
    }
}
