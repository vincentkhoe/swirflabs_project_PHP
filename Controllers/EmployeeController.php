<?php
namespace Controllers;

use Models\Employee;
use Models\ErrorResponse;
use Models\SuccessResponse;

class EmployeeController
{
  private $employeeRepo;

  public function __construct($employeeRepo)
  {
    $this->employeeRepo = $employeeRepo;
  }

  public function create()
  {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
      http_response_code(400);
      echo json_encode(new ErrorResponse("Invalid request body"));
      return;
    }

    $emp = new Employee();

    if (empty(trim($input['name'] ?? ''))) {
      http_response_code(400);
      echo json_encode(new ErrorResponse("Name is required"));
      return;
    }

    if (empty(trim($input['identificationNumber'] ?? ''))) {
      http_response_code(400);
      echo json_encode(new ErrorResponse("Identification number is required"));
      return;
    }

    if (empty(trim($input['occupation'] ?? ''))) {
      http_response_code(400);
      echo json_encode(new ErrorResponse("Occupation is required"));
      return;
    }

    if (empty(trim($input['dateOfBirth'] ?? ''))) {
      http_response_code(400);
      echo json_encode(new ErrorResponse("Date of birth is required"));
      return;
    }

    $emp->setName(trim($input['name']));
    $emp->setIdentificationNumber(trim($input['identificationNumber']));
    $emp->setOccupation(trim($input['occupation']));
    $emp->setDateOfBirth(trim($input['dateOfBirth']));
    $emp->setAddress($input['address'] ?? '');
    $emp->setPlaceOfBirth($input['placeOfBirth'] ?? '');

    $uniqueKey = str_replace(' ', '', trim($input['name'])) . '_' . str_replace(' ', '', trim($input['identificationNumber']));
    $emp->setUniqueKey($uniqueKey);

    try {
      $age = Employee::calculateAge($emp->getDateOfBirth());
      $emp->setAge($age);
    } catch (\Exception $e) {
      http_response_code(400);
      echo json_encode(new ErrorResponse("Invalid date of birth format"));
      return;
    }

    try {
      $this->employeeRepo->createEmployee($emp);
      http_response_code(201);
      echo json_encode(new SuccessResponse("Employee created successfully", $emp->toArray()));
    } catch (\Exception $e) {
          if ($e->getMessage() === "DUPLICATE_EMPLOYEE") {
          http_response_code(409);
          echo json_encode(new ErrorResponse("An employee with this name and identification number already exists"));
          } else {
              http_response_code(500);
              echo json_encode(new ErrorResponse("Failed to create employee"));
          }
      }
  }

  public function getAll()
  {
      header('Content-Type: application/json');

      try {
          $employee = $this->employeeRepo->getAllEmployee();
          echo json_encode($employee);
      } catch (\Exception $e) {
          http_response_code(500);
          echo json_encode(new ErrorResponse("Failed to retrieve employees"));
    }
  }


  public function getByUniqueKey($params)
  {
    header("Content-Type: application/json");

    $name = str_replace(' ', '', trim($params['name'] ?? ''));
    $identificationNumber = str_replace(' ', '', trim($params['in'] ?? ''));
    $uniqueKey = $name . '_' . $identificationNumber;

    try {
      $employee = $this->employeeRepo->getByUniqueKey($uniqueKey);

      if (!$employee) {
        http_response_code(404);
        echo json_encode(new ErrorResponse("Employee name and identification number not found"));
        return;
      }

      echo json_encode($employee);
    } catch (\Exception $e) {
      http_response_code(404);
      echo json_encode(new ErrorResponse("Invalid employee name or identification number"));
    }
  }

  public function deleteByUniqueKey($params)
  {
    header('Content-Type: application/json');

    $name = str_replace(' ', '', trim($params['name'] ?? ''));
    $identificationNumber = str_replace(' ', '', trim($params['in'] ?? ''));
    $uniqueKey = $name . '_' . $identificationNumber;

    try {
      $this->$employeeRepo->deleteEmployee($uniqueKey);
      echo json_encode(new SuccessResponse("Employee deleted successfully"));
    } catch (\Exception $e) {
      if (strpos($e->getMessage(), 'not found') !== false) {
        http_response_code(404);
        echo json_encode(new ErrorResponse("Invalid employee name or identification number"));
      } else {
        http_response_code(500);
        echo json_encode(new ErrorResponse("Failed to delete employee"));
      }
    }
  }
}