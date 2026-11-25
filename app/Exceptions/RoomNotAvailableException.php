<?php

namespace App\Exceptions;

use Exception;

class RoomNotAvailableException extends Exception
{
    protected $message = 'Room was just booked. Choose another room.';
}
