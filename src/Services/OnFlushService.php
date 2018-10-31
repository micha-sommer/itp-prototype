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

class OnFlushService
{

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $oldErrorReporting = error_reporting(0);

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new \DateTime());
            $changeSet->setType('ADD');
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
            dump($uow->getEntityIdentifier($entity));
            $changeSet->setChangeSet(\json_encode($uow->getEntityChangeSet($entity)));
            $em->persist($changeSet);
            $md = $em->getClassMetadata(\get_class($changeSet));
            $uow->computeChangeSet($md, $changeSet);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $changeSet = new ChangeSet();
            $changeSet->setTimestamp(new \DateTime());
            $changeSet->setType('UPDATE');
            dump($uow->getEntityIdentifier($entity));
            dump($uow->getEntityChangeSet($entity));
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
            $changeSet->setChangeSet(\json_encode($uow->getOriginalEntityData($entity)));
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