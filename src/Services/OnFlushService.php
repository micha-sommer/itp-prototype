<?php

/**
 * Created by PhpStorm.
 * User: mremm
 * Date: 12.10.2018
 * Time: 21:01
 */

namespace App\Services;


use App\Entity\ChangeSet;
use App\Entity\Contestant;
use App\Entity\Invoice;
use App\Entity\InvoicePosition;
use App\Entity\Official;
use App\Entity\Registration;
use App\Entity\Transport;
use DateTime;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\ORMException;
use function get_class;
use function json_encode;

class OnFlushService
{

    /**
     * @param OnFlushEventArgs $eventArgs
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $oldErrorReporting = error_reporting(0);

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            switch (true) {
                case $entity instanceof Official:
                case $entity instanceof Contestant:
                    $entity->setTimestamp(new DateTime());
                    break;
                default:
                    break;
            }
            $md = $em->getClassMetadata(get_class($entity));
            $uow->recomputeSingleEntityChangeSet($md, $entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new DateTime());
            $changeSet->setType('UPDATE');

            switch (true) {
                case $entity instanceof Official:
                    $changeSet->setName('official');
                    break;
                case $entity instanceof Contestant:
                    $changeSet->setName('contestant');
                    break;
                case $entity instanceof Transport:
                    $changeSet->setName('transport');
                    break;
                case $entity instanceof Registration:
                    $changeSet->setName('registration');
                    break;
                case $entity instanceof Invoice:
                    $changeSet->setName('invoice');
                    break;
                case $entity instanceof InvoicePosition:
                    $changeSet->setName('invoicePosition');
                    break;
                default:
                    $changeSet->setName('unknown');
                    break;
            }
            $changeSet->setNameId($entity->getId());
            $changeSet->setChangeSet(json_encode($uow->getEntityChangeSet($entity)));
            $em->persist($changeSet);
            $md = $em->getClassMetadata(get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new DateTime());
            $changeSet->setType('DROP');
            switch (true) {
                case $entity instanceof Official:
                    $changeSet->setName('official');
                    break;
                case $entity instanceof Contestant:
                    $changeSet->setName('contestant');
                    break;
                case $entity instanceof Transport:
                    $changeSet->setName('transport');
                    break;
                case $entity instanceof Registration:
                    $changeSet->setName('registration');
                    break;
                case $entity instanceof Invoice:
                    $changeSet->setName('invoice');
                    break;
                case $entity instanceof InvoicePosition:
                    $changeSet->setName('invoicePosition');
                    break;
                default:
                    $changeSet->setName('unknown');
                    break;
            }
            $changeSet->setNameId($entity->getId());
            $json = json_encode($entity);
            //$club = '"club":"'.$entity->getRegistration()->getClub().'",';
            //$json = substr_replace($json, $club, 1, 0);
            $changeSet->setChangeSet($json);
            $em->persist($changeSet);
            $md = $em->getClassMetadata(get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);
        }

        error_reporting($oldErrorReporting);
    }
}