<?php
namespace Models;

use DateTime;

class Employee
{
  private $id;
  private $uniqueKey;
  private $name;
  private $identificationNumber;
  private $age;
  private $address;
  private $occupation;
  private $placeOfBirth;
  private $dateOfBirth;
  private $createdAt;

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }

  public function getUniqueKey() { return $this->uniqueKey; }
  public function setUniqueKey($uniqueKey) { $this->uniqueKey = $uniqueKey; }

  public function getName() { return $this->name; }
  public function setName($name) { $this->name = $name; }

  public function getIdentificationNumber() { return $this->identificationNumber; }
  public function setIdentificationNumber($identificationNumber) { $this->identificationNumber = $identificationNumber; }

  public function getAge() { return $this->age; }
  public function setAge($age) { $this->age = $age; }

  public function getAddress() { return $this->address; }
  public function setAddress($address) { $this->address = $address; }

  public function getOccupation() { return $this->occupation; }
  public function setOccupation($occupation) { $this->occupation = $occupation; }

  public function getPlaceOfBirth() { return $this->placeOfBirth; }
  public function setPlaceOfBirth($placeOfBirth) { $this->placeOfBirth = $placeOfBirth; }

  public function getDateOfBirth() { return $this->dateOfBirth; }
  public function setDateOfBirth($dateOfBirth) { $this->dateOfBirth = $dateOfBirth; }

  public function getCreatedAt() { return $this->createdAt; }
  public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'uniqueKey' => $this->uniqueKey,
      'name' => $this->name,
      'identificationNumber' => $this->identificationNumber,
      'age' => $this->age,
      'address' => $this->address,
      'occupation' => $this->occupation,
      'placeOfBirth' => $this->placeOfBirth,
      'dateOfBirth' => $this->dateOfBirth,
      'createdAt' => $this->createdAt
    ];
  }

  public static function calculateAge($dateOfBirth)
  {
    $dob = new DateTime($dateOfBirth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    return $age;
  }
}