<?php
/**
 * Roting middleware for the Carbon WP theme
 *
 * This file will load the correct frontend controller to display.
 *
 * @package CarbonWP
 * @author Ramy Deeb <me@ramydeeb.com>
 */
namespace Carbon\Middleware;

class Routing
{
    protected $rules;

    public function __construct( array $rules )
    {
        $this->rules = $rules;
    }

    public function match()
    {

    }
}
