<?php
namespace Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
      $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
      $dotenv->load();

      $host = $_ENV['DB_HOST'];
      $port = $_ENV['DB_PORT'];
      $dbname = $_ENV['DB_NAME'];
      $user = $_ENV['DB_USER'];
      $pass = $_ENV['DB_PASS'];

      $maxAttempts = 5;
      $attempt = 0;

      while ($attempt < $maxAttempts) {
        try {
          $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
          $this->connection = new PDO($dsn, $user, $pass);
          $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          
          $this->connection->exec("USE `$dbname`");
          
          error_log("Database connected and Initialized Complete");
          break;
        } catch (PDOException $e) {
          $attempt++;
          error_log("Failed to connect to database (attempt:$attempt/$maxAttempts): " . $e->getMessage());

          if ($attempt >= $maxAttempts) {
            die("Could not connect to database after 5 attempts:" . $e->getMessage());
          }

          sleep(2);
        }
      }
    }

    public static function getInstance()
    {
      if (self::$instance === null) {
        self::$instance = new self();
      }
      return self::$instance;
    }

    public function getConnection()
    {
      return $this->connection;
    }
}