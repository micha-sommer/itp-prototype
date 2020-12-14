<?php

namespace App\Services;

use DateTime;

class Globals
{
    /**
     * The year of the tournament.
     */
    private int $year;

    /**
     * The counter for the training camp, e.g. 15th International Training Camp.
     */
    private int $itcCount;

    /**
     * The counter for the tournament, e.g. 28th International Thuringia Cup.
     */
    private int $itpCount;

    /**
     * When true displays a warning message (i.e. accomondation is limited).
     */
    private bool $alertAccomondation;

    /**
     * DateTime, when you last can enter competitors etc.
     */
    private DateTime $endDate;

    /**
     * DateTime, from when increased registration fee is charged.
     */
    private DateTime $deadline;

    /**
     * Tells whether registration is currently active or not.
     */
    private bool $isActive;

    public function __construct()
    {
        $this->setYear('2021')
            ->setItcCount('17')
            ->setItpCount('30')
            ->setAlertAccomondation(false)
            ->setEndDate(new DateTime('March 10, 2021 23:59'))
            ->setDeadline(new DateTime('March 10, 2021 23:59'))
            ->setActive(false)
        ;
    }

    /**
     * Get the value of year.
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year.
     *
     * @param mixed $year
     *
     * @return self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of itcCount.
     */
    public function getItcCount()
    {
        return $this->itcCount;
    }

    /**
     * Set the value of itcCount.
     *
     * @param mixed $itcCount
     *
     * @return self
     */
    public function setItcCount($itcCount)
    {
        $this->itcCount = $itcCount;

        return $this;
    }

    /**
     * Get the value of itpCount.
     */
    public function getItpCount()
    {
        return $this->itpCount;
    }

    /**
     * Set the value of itpCount.
     *
     * @param mixed $itpCount
     *
     * @return self
     */
    public function setItpCount($itpCount)
    {
        $this->itpCount = $itpCount;

        return $this;
    }

    /**
     * Get the value of alertAccomondation.
     */
    public function getAlertAccomondation()
    {
        return $this->alertAccomondation;
    }

    /**
     * Set the value of alertAccomondation.
     *
     * @param mixed $alertAccomondation
     *
     * @return self
     */
    public function setAlertAccomondation($alertAccomondation)
    {
        $this->alertAccomondation = $alertAccomondation;

        return $this;
    }

    /**
     * Get the value of endDate.
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate.
     *
     * @param mixed $endDate
     *
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the value of deadline.
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set the value of deadline.
     *
     * @param mixed $deadline
     *
     * @return self
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get tells whether registration is currently active or not.
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Set tells whether registration is currently active or not.
     *
     * @param mixed $isActive
     *
     * @return self
     */
    public function setActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }
}
