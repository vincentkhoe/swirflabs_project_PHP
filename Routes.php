<?php
require_once __DIR__ . '/vendor/autoload.php';

use Config\Database;
use Repository\EmployeeRepository;
use Controllers\EmployeeController;

$db = Database::getInstance()->getConnection();

$employeeRepo = new EmployeeRepository($db);
$employeeController = new EmployeeController($employeeRepo);

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$request = strtok($request, "?");

if (strpos($request, '/api') === 0) {
    $apiRequest = substr($request, 4);

    if (preg_match('#^/employee$#', $apiRequest) && $method === 'POST') {
      $employeeController->create();
    } elseif (preg_match('#^/employee$#', $apiRequest) && $method === 'GET') {
      $employeeController->getAll();
    } elseif (preg_match('#^/employee/([^/]+)/([^/]+)$#', $apiRequest, $matches) && $method === 'GET') {
      $employeeController->getByUniqueKey(['name' => $matches[1], 'in' => $matches[2]]);
    } elseif (preg_match('#^/employee/([^/]+)/([^/]+)', $apiRequest, $matches) && $method === 'DELETE') {
      $employeeController->deleteByUniqueKey(['name' => $matches[1], 'in' => $matches[2]]);
    } else {
      http_response_code(404);
      header('Content-Type: application/json');
      echo json_encode(['error' => 'API endpoint not found']);
    }
    exit;
}

$request = str_replace(['..', './'], '', $request);

if ($request === '' || $request === '/') {
  $indexPath = __DIR__ . '/static/index.html';
  if (file_exists($indexPath)) {
    header('Content-Type: text/html');
    readfile($indexPath);
    exit;
  }
  http_response_code(404);
  echo "index.html not found";
  exit;
}

$filePath = __DIR__ . '/static' . $request;

if (file_exists($filePath) && !is_dir($filePath)) {
  $ext = pathinfo($filePath, PATHINFO_EXTENSION);
  $mimeTypes = [
      'css' => 'text/css',
      'js' => 'application/javascript',
      'html' => 'text/html',
  ];

  $contentType = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : mime_content_type($filePath);
  header("Content-Type: $contentType");
  readfile($filePath);
  exit;
}

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found']);
