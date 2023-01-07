<?php
/**
 * Shim HTTP request.
 */

// Connection details
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REMOTE_PORT'] = '65535';
$_SERVER['SERVER_SOFTWARE'] = 'Apache';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME'] = '0.0.0.0';
$_SERVER['SERVER_PORT'] = '80';

// Request details
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'example.com';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
$_SERVER['HTTP_ACCEPT'] = '*/*';
