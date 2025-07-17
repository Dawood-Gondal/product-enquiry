<?php

declare(strict_types=1);

/**
 * @category    BugsBunny Enterprise
 * @package     BugsBunny_OrderComment
 * @copyright   Copyright (c) 2023 BugsBunny Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace BugsBunny\LookingFor\Controller\Index;

use Exception;
use Magento\Framework\App\Action\Action;
use BugsBunny\LookingFor\Model\ConfigInterface;
use BugsBunny\LookingFor\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Index
 *
 * Controller to handle "Looking For" form submissions via AJAX.
 */
class Index extends Action
{
    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $_transportBuilder;

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Escaper
     */
    protected Escaper $_escaper;

    /**
     * @var Data
     */
    protected Data $_helper;

    /**
     * @var JsonFactory
     */
    private JsonFactory $_jsonFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     * @param JsonFactory $jsonFactory
     * @param Data $helper
     */
    public function __construct(Context $context, TransportBuilder $transportBuilder, StateInterface $inlineTranslation, ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, Escaper $escaper, JsonFactory $jsonFactory, Data $helper)
    {
        parent::__construct($context);
        $this->_jsonFactory = $jsonFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_helper = $helper;
    }

    /**
     * Execute method to process the form submission and send email.
     *
     * @return Json
     */
    public function execute(): Json
    {
        $responseJson = $this->_jsonFactory->create();
        $result = ['message' => '', 'result' => ''];

        $comment = $this->getRequest()->getParam('comment');
        if (!empty($comment)) {
            $result['result'] = true;
            $sent = $this->_sendEmail($comment);
            if (!$sent) {
                $result['result'] = false;
                $result['message'] = "We were unable to submit your request.";
            } else {
                $result['message'] = "Thank you for submitting your request.";
            }
        } else {
            $result['result'] = false;
            $result['message'] = 'Input box is empty. Please, Fill in the input box';
        }

        return $responseJson->setData($result);
    }

    /**
     * Send email with the submitted comment using configured email template.
     *
     * @param string $comment
     * @return bool
     */
    private function _sendEmail(string $comment): bool
    {
        $this->inlineTranslation->suspend();
        $result = true;

        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier($this->_helper->getConfigData('lookingfor/email/email_template'))->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID])->setTemplateVars(['comment' => $comment])->setFrom($this->_helper->getConfigData('lookingfor/email/sender_email_identity'))->addTo($this->_helper->getConfigData('lookingfor/email/recipient_email'))->getTransport();

            $transport->sendMessage();
        } catch (Exception $e) {
            $result = false;
        } finally {
            $this->inlineTranslation->resume();
        }
        return $result;
    }
}
