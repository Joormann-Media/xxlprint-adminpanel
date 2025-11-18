<?php

namespace App\EventListener;

use App\Entity\Vehicle;
use App\Entity\MileageLog;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

#[AsEntityListener(event: 'postPersist', entity: Vehicle::class)]
class VehicleMileageLogListener
{
    public function postPersist(Vehicle $vehicle, LifecycleEventArgs $args): void
    {
        $em = $args->getObjectManager();

        $mileageLog = new MileageLog();
        $mileageLog->setVehicle($vehicle);
        $mileageLog->setDriver($vehicle->getDriver());
        $mileageLog->setDate(new \DateTime());
        $mileageLog->setStartMile(0);
        $mileageLog->setEndMile(0);
        $mileageLog->setPurpose('Fahrzeug-Inbetriebnahme');
        $mileageLog->setCreatedAt(new \DateTime());
        $mileageLog->setUpdatedAt(new \DateTime());

        $em->persist($mileageLog);
        $em->flush();
    }
}
