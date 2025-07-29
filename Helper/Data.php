<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_OrderComment
 * @copyright   Copyright (c) 2025 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

declare(strict_types=1);

namespace M2Commerce\ProductEnquiry\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @param $path
     * @param $storeId
     * @return mixed
     */
    public function getConfigData($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
