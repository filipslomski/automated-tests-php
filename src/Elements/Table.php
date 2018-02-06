<?php

namespace myTests\Elements;

class Table extends BaseElement
{
    /**
     * Table constructor.
     * @param \WebDriverBy $locator
     */
    public function __construct($locator)
    {
        $this->selector = $locator->getValue();
        parent::__construct($locator);
    }

    public function isRowWithTextPresent(array $text) : bool
    {
        $rowWithText = new Element(\WebDriverBy::xpath($this->getSelectorWithText($text)));
        return $rowWithText->isElementPresent();
    }

    public function selectRowWithText(array $text)
    {
        $rowWithText = new Element(\WebDriverBy::xpath($this->getSelectorWithText($text)));
        $rowWithText->click();
    }

    public function performActionOnRowWithText(string $action, array $text)
    {
    }

    private function getSelectorWithText(array $texts) : string
    {
        $textSelector = "//tr";
        foreach ($texts as $text) {
            $textSelector .= "[.//*[contains(text(),'" . $text . "')]]";
        }
        return $this->selector . $textSelector;
    }
}
