<?php //  src/Entity/Flight.php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name : 'flights')]
class Flight implements EntityInterface {

  public CONST CREATE_GROUP = 'create';
  public CONST UPDATE_GROUP = 'update';

   #[ORM\Id]
   #[ORM\Column(type:'integer')]
   #[ORM\GeneratedValue]
   private ?int $id = null;

   #[ORM\Column(type:'string', length:15)]
   #[Assert\NotBlank(groups: [self::CREATE_GROUP])]
   private string $number;

   #[ORM\Column(type:'string', length:3)]
   #[Assert\NotBlank(groups: [self::CREATE_GROUP, self::UPDATE_GROUP])]
    private string $origin;

   #[ORM\Column(type:'string', length:3)]
   #[Assert\NotBlank(groups: [self::CREATE_GROUP, self::UPDATE_GROUP])]
   private string $destination;

   #[ORM\Column(type:'datetime_immutable', name: 'departure_time')]
   #[Assert\NotBlank(groups: [self::CREATE_GROUP, self::UPDATE_GROUP])]
   private DateTimeImmutable $departureTime;

   #[ORM\Column(type:'datetime_immutable', name: 'arrival_time')]
   #[Assert\NotBlank(groups: [self::CREATE_GROUP, self::UPDATE_GROUP])]
   private DateTimeImmutable $arrivalTime;

   public function getNumber() : string{
    return $this->number;
   }

   public function setNumber(string $number) : void {
     $this->number = $number;
   }

   public function getOrigin() : string{
    return $this->origin;
   }

   public function setOrigin(string $origin) : void {
     $this->origin = $origin;
   }

   public function getDestination() : string{
    return $this->destination;
   }

   public function setDestination(string $destination) : void {
     $this->destination = $destination;
   }

   public function getDepartureTime() : DateTimeImmutable{
    return $this->departureTime;
   }

   public function setDepartureTime(string|DateTimeImmutable $departureTime) : void {

    if(is_string($departureTime))
    {
       $departureTime = new DateTimeImmutable();
    }
     $this->departureTime = $departureTime;
   }

   public function getArrivalTime() : DateTimeImmutable{
    return $this->arrivalTime;
   }

   public function setArrivalTime(string|DateTimeImmutable $arrivalTime) : void {
     if(is_string($arrivalTime))
    {
       $arrivalTime = new DateTimeImmutable();
    }
     $this->arrivalTime = $arrivalTime;
   }

}