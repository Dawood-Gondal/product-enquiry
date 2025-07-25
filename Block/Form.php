<?php

declare(strict_types=1);

/**
 * @category    BugsBunny Enterprise
 * @package     BugsBunny_OrderComment
 * @copyright   Copyright (c) 2023 BugsBunny Enterprise
 * @author      dawoodgondaldev@gmail.com
 */
namespace BugsBunny\LookingFor\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Form
 *
 * This block class is responsible for rendering and managing the "Looking For" form
 * on the frontend. It provides the form action URL used when the form is submitted.
 */
class Form extends Template
{
    /**
     * Constructor
     *
     * @param Context $context The template context object.
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Get the action URL for the form submission.
     *
     * @return string The URL where the form data will be posted.
     */
    public function getFormAction(): string
    {
        return $this->getUrl('lookingfor/index/index');
    }
}
