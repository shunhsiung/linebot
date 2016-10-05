<?php
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

date_default_timezone_set('Asia/Taipei');

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

$capsule = new Capsule;
