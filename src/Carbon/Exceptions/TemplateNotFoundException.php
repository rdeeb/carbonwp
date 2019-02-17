<?php
/**
 * Exception for a template file not found
 *
 * @package CarbonWP
 * @author Ramy Deeb <me@ramydeeb.com>
 */
namespace Carbon\Exceptions;

use Throwable;

class TemplateNotFoundException extends \Exception{
    public function __construct( $templates, $code = 0, Throwable $previous = null ) {
        $filename = is_array( $templates ) ? implode( ', ', $templates ) : $templates;
        $message = "The template(s) ${filename} were not found. Please verify your path and try again.";
        parent::__construct( $message, $code, $previous );
    }
}
