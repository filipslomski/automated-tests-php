<?php


namespace myTests\Elements;

use DateTime;
use myTests\Context;

/**
 * Class DatePicker
 * accepts format i.e. '23 Mar 2016'
 */
class Datepicker extends BaseElement
{
    public $selector;
    public $currentMonthAndYear;
    public $next;
    public $previous;
    public $day;

    public function __construct(\WebDriverBy $locator)
    {
        $this->selector = $locator->getValue();
        $this->currentMonthAndYear = new Element(\WebDriverBy::xpath($this->selector . ""));
        $this->next = new Element(\WebDriverBy::xpath($this->selector . ""));
        $this->previous = new Element(\WebDriverBy::xpath($this->selector . ""));
        $this->day = new Element(\WebDriverBy::xpath($this->selector . ""));
        parent::__construct($locator);
    }

    public function setDate(string $date, string $expectedFormat = 'Y-m-d') : string
    {
        $dateTime = new DateTime();
        $dateTime->modify($date);
        list($day, $month, $year) = explode(' ', $dateTime->format('j n Y'));
        $this->click();
        $this->selectYear($year);
        $this->selectMonth($month);
        $this->selectDay($day);

        return $dateTime->format($expectedFormat);
    }

    private function selectYear(string $year)
    {
        while (intval($year) != $this->getYear()) {
            if (intval($year) < $this->getYear()) {
                $this->next->click();
            } else {
                $this->previous->click();
            }
        }
    }

    private function selectMonth(string $month)
    {
        while (intval($month) != date_parse($this->getMonth())['month']) {
            if (intval($month) < date_parse($this->getMonth())['month']) {
                $this->next->click();
            } else {
                $this->previous->click();
            }
        }
    }

    private function selectDay(string $day)
    {
        $this->day->setLocatorParameters($day)->click();
    }

    private function getMonth() : string
    {
        return trim(preg_replace('/[0-9]+/', '', $this->currentMonthAndYear->getText()));
    }

    private function getYear() : int
    {
        return intval(preg_replace('/\D/', '', $this->currentMonthAndYear->getText()));
    }
}
