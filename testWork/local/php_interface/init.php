<?php
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/handlers.php')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/handlers.php');
}
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/events.php')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/events.php');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/functions.php')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/functions.php');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/agents.php')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/agents.php');
}