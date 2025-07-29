<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_OrderComment
 * @copyright   Copyright (c) 2025 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

declare(strict_types=1);

namespace M2Commerce\ProductEnquiry\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Form extends Template
{
    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('productenquiry/index/index');
    }
}
