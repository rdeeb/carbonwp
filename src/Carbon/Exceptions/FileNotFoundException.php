<?php
/**
 * Exception for a template file not found
 *
 * @package CarbonWP
 * @author Ramy Deeb <me@ramydeeb.com>
 */
namespace Carbon\Exceptions;

use Throwable;

class FileNotFoundException extends \Exception{
    public function __construct( $filename, $code = 0, Throwable $previous = null ) {
        $message = "The file ${filename} was not found. Please verify your path and try again.";
        parent::__construct( $message, $code, $previous );
    }
}
