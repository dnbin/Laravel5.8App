<?php


namespace App\Services\RapidApiBooking;

use Throwable;

class ServerException extends \Exception {

    public function __construct( $message = "", $code = 0, Throwable $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }
}
