<?php
declare(strict_types=1);

/**
 * @category    BugsBunny Enterprise
 * @package     BugsBunny_OrderComment
 * @copyright   Copyright (c) 2023 BugsBunny Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace BugsBunny\LookingFor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * Helper class to retrieve configuration values from system config.
 */
class Data extends AbstractHelper
{
    /**
     * Data constructor.
     *
     * @param Context $context Magento helper context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Retrieve config value by path and optional store ID.
     *
     * @param string $path
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigData(string $path, ?int $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
