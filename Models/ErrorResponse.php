<?php
namespace Models;

class ErrorResponse
{
    public $error;

    public function __construct($message)
    {
      $this->error = $message;
    }
}