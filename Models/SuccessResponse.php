<?php
namespace Models;

class SuccessResponse
{
    public $message;
    public $data;

    public function __construct($message, $data = null)
    {
        $this->message = $message;
        $this->data = $data;
    }
}