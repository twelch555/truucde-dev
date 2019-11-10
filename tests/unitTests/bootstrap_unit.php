<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Truucde
 */

/**
 * Autoload the composer items
 */
require_once 'vendor/autoload.php';

/**
 * Bootstrap Method of WP Mock
 */
WP_Mock::bootstrap();

/**
 * Plugin files under test.
 */
require_once 'truucde.php';
