<?php
$rootDir = dirname(dirname(dirname(__FILE__)));
require_once $rootDir . "/vendor/autoload.php";
require_once $rootDir . "/app/autoload.php";

use plugins\YouBike\GeoYouBike;


if ($_SERVER['argc'] >= 3) {
	$lng = $_SERVER['argv'][1];
	$lat = $_SERVER['argv'][2];
	$meter = isset($_SERVER['argv'][3]) ? $_SERVER['argv'][3] : 700;

	$geo = new GeoYouBike();
//$data = $geo->get_lnglat("121.367479","25.07663",700);
	$data = $geo->get_lnglat($lng,$lat,$meter);
	echo $data;
}
