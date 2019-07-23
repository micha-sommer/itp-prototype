<?php
/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 14.06.2018
 * Time: 14:01
 */

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;

class InvoicePositionsList
{
    protected $list;

    public function __construct()
    {
        $this->list = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getList(): ArrayCollection
    {
        return $this->list;
    }

    /**
     * @param ArrayCollection $list
     */
    public function setList(ArrayCollection $list): void
    {
        $this->list = $list;
    }

    public function addInvoicePosition(InvoicePosition $invoicePosition): void
    {
        $this->list->add($invoicePosition);
    }

    public function removeInvoicePosition(InvoicePosition $invoicePosition): void
    {
        $this->list->removeElement($invoicePosition);
    }

}