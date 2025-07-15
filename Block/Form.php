<?php
namespace BugsBunny\Lookingfor\Block;
class Form extends \Magento\Framework\View\Element\Template
{
    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        parent::__construct($context);
    }

    public function getFormAction()
    {
        return $this->getUrl('lookingfor/index/index');
    }
}
