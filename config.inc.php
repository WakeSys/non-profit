<?php

require_once __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/..');
$dotenv->load();

$mysqldb = getenv("DB_DATABASE");		// DB name 
$mysqluser = getenv("DB_USERNAME");		// Benutzername fur den MySQL-Zugang 
$mysqlpasswd = getenv("DB_PASSWORD");	// Passwort 
$mysqlhost = getenv("DB_HOST");			// Name des Rechners, auf dem MySQL laeuft 


// echo '$mysqlhost: ' . $mysqlhost . '<br>';