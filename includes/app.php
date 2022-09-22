<?php


require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeload();
require 'funciones.php';
require 'database.php';


// Conectarnos a la base de datos
use Dotenv\Dotenv;
use Model\ActiveRecord;
ActiveRecord::setDB($db);