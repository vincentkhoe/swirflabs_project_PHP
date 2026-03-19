<?php
namespace Repository;

use PDO;
use Models\Employee;
use Models\EmployeeResponse;

class EmployeeRepository
{
    private $db;

    public function __construct($db)
    {
      $this->db = $db;
    }

    public function createEmployee(Employee $emp)
    {
      $query = "INSERT INTO employees (unique_key, name, identification_number, age, address, occupation, place_of_birth, date_of_birth)
          VALUES (:unique_key, :name, :identification_number, :age, :address, :occupation, :place_of_birth, :date_of_birth)";
      
      $stmt = $this->db->prepare($query);

      try {
        $result = $stmt->execute([
          ':unique_key' => $emp->getUniqueKey(),
          ':name' => $emp->getName(),
          ':identification_number' => $emp->getIdentificationNumber(),
          ':age' => $emp->getAge(),
          ':address' => $emp->getAddress(),
          ':occupation' => $emp->getOccupation(),
          ':place_of_birth' => $emp->getPlaceOfBirth(),
          ':date_of_birth' => $emp->getDateOfBirth()
        ]);

        if (!$result) {
            throw new \Exception("Error inserting employee");
        }

        $emp->setId($this->db->lastInsertId());
        return true;
      } catch (\PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            throw new \Exception("DUPLICATE_EMPLOYEE");
        } else {
            throw new \Exception("DB_ERROR: " . $e->getMessage());
        }
      }
    }

    public function getAllEmployee()
    {
        $query = "SELECT id, unique_key, name, identification_number, age, address, occupation, place_of_birth, date_of_birth
                  FROM employees
                  ORDER BY id DESC";
        
        $stmt = $this->db->query($query);
        $employees = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = [
                'id' => $row['id'],
                'uniqueKey' => $row['unique_key'],
                'name' => $row['name'],
                'identificationNumber' => $row['identification_number'],
                'age' => $row['age'],
                'address' => $row['address'],
                'occupation' => $row['occupation'],
                'placeOfBirth' => $row['place_of_birth'],
                'dateOfBirth' => $row['date_of_birth']
            ];
        }

        return $employees;
    }

    public function getByUniqueKey($uniqueKey)
    {
        $query = "SELECT id, unique_key, name, identification_number, age, address, occupation, place_of_birth, date_of_birth
                  FROM employees
                  WHERE unique_key = :unique_key";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':unique_key' => $uniqueKey]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
          return null;
        }

        return [
            'id' => $row['id'],
            'uniqueKey' => $row['unique_key'],
            'name' => $row['name'],
            'identificationNumber' => $row['identification_number'],
            'age' => $row['age'],
            'address' => $row['address'],
            'occupation' => $row['occupation'],
            'placeOfBirth' => $row['place_of_birth'],
            'dateOfBirth' => $row['date_of_birth']
        ];
    }

    public function deleteEmployee($uniqueKey)
    {
          $query = "DELETE FROM employees WHERE unique_key = :unique_key";

          $stmt = $this->db->prepare($query);
          $stmt->execute([':unique_key' => $uniqueKey]);

          $rowsAffected = $stmt->rowCount();

          if ($rowsAffected === 0) {
              throw new \Exception("Employee with unique key $uniqueKey not found");
          }

          return true;
    }

    public function checkDuplicate($uniqueKey)
    {
      $query = "SELECT EXISTS(SELECT 1 FROM employees WHERE unique_key = :unique_key) as exist";

      $stmt = $this->db->prepare($query);
      $stmt->execute([':unique_key' => $uniqueKey]);

      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return (bool)$result['exist'];
    }
}