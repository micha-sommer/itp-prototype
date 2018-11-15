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
use App\Entity\Official;
use App\Entity\Registration;
use App\Entity\Transport;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class OnFlushService
{

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $oldErrorReporting = error_reporting(0);

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new \DateTime());
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
                default:
                    $changeSet->setName('unknown');
                    break;
            }
            $changeSet->setNameId($entity->getId());
            $changeSet->setChangeSet(\json_encode($uow->getEntityChangeSet($entity)));
            $em->persist($changeSet);
            $md = $em->getClassMetadata(\get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new \DateTime());
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
                default:
                    $changeSet->setName('unknown');
                    break;
            }
            $changeSet->setNameId($entity->getId());
            $json = \json_encode($entity);
            //$club = '"club":"'.$entity->getRegistration()->getClub().'",';
            //$json = substr_replace($json, $club, 1, 0);
            $changeSet->setChangeSet($json);
            $em->persist($changeSet);
            $md = $em->getClassMetadata(\get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);
        }

        foreach ($uow->getScheduledCollectionDeletions() as $col) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new \DateTime());
            $changeSet->setType('DROP');
            $changeSet->setName('unknown');
            $changeSet->setChangeSet(\json_encode($uow->getEntityChangeSet($col)));
            $em->persist($changeSet);
            $md = $em->getClassMetadata(\get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);

        }

        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new \DateTime());
            $changeSet->setType('UPDATE');
            $changeSet->setName('unknown');
            $changeSet->setChangeSet(\json_encode($uow->getEntityChangeSet($col)));
            $em->persist($changeSet);
            $md = $em->getClassMetadata(\get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);
        }
        error_reporting($oldErrorReporting);
    }
}