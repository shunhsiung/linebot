<?php
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

date_default_timezone_set('Asia/Taipei');

$rootDir = dirname(dirname(__FILE__));

$dotenv = new Dotenv\Dotenv($rootDir);
$dotenv->load();

$capsule = new Capsule;

$database = [
	'driver'	=> 'pgsql',
	'host'		=> getenv('DB_HOST','localhost'),
	'database'	=> getenv('DB_DATABASE','foo'),
	'username'	=> getenv('DB_USERNAME','foo'),
	'password'	=> getenv('DB_PASSWORD',''),
	'charset'	=> 'utf8',
	'prefix' 	=> '',
];

$capsule->addConnection($database);
$capsule->setAsGlobal();
$capsule->bootEloquent();

