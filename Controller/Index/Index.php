<?php
namespace BugsBunny\Lookingfor\Controller\Index;

use Magento\Framework\App\Action\Action;
use BugsBunny\Lookingfor\Model\ConfigInterface;
use BugsBunny\Lookingfor\Helper\Data;

class Index extends Action
{
    private $lookingforConfig;

    private $_jsonFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    protected $_helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->_jsonFactory = $jsonFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_helper = $helper;
    }


    public function execute()
    {
        $responseJson = $this->_jsonFactory->create();
        $result = ['message' => '', 'result' => ''];

        $comment = $this->getRequest()->getParam('comment');
        if (!empty($comment)) {
            // Retrieve your form data
            $result['result'] = true;
            $sent = $this->_sendEmail($comment);
            if (!$sent) {
                $result['result'] = false;
                $result['message'] = "We were unable to submit your request.";
            } else {
                $result['message'] = "Thank you for submitting your request.";
            }
        } else {
            $result['message'] = 'Input box is empty. Please, Fill in the input box';
            $result['result'] = false;
        }

        $responseJson->setData(
            $result
        );
        return $responseJson;
    }

    private function _sendEmail($comment){

        $this->inlineTranslation->suspend();
        $result = true;
        try {
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->_helper->getConfigData('lookingfor/email/email_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['comment' => $comment])
                ->setFrom($this->_helper->getConfigData('lookingfor/email/sender_email_identity'))
                ->addTo($this->_helper->getConfigData('lookingfor/email/recipient_email'))
                ->getTransport();
            $transport->sendMessage();

            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $result = false;
        }
        return $result;
    }
}
