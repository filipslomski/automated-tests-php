<?php

namespace myTests\Elements;

/**
 * Class Dropdown
 * i.e. Reports
 */
class Dropdown extends BaseElement
{
    protected $dropdownOption;

    /**
     * Dropdown constructor.
     * @param \WebDriverBy $locator
     */
    public function __construct($locator)
    {
        $this->selector = $locator->getValue();
        $this->dropdownOption = new Element(\WebDriverBy::xpath($this->selector . "/option[contains(@label, '{}')]"));
        parent::__construct($locator);
    }

    public function setOption($option)
    {
        $this->click();
        if ($this->dropdownOption->setLocatorParameters($option)->isElementVisible()) {
            $this->dropdownOption->click();
        }
    }
}
