<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/api/log.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/luhn_algorithm_test.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/engine.php';



$some_validation= new valid();
$some_validation->bool_r();

