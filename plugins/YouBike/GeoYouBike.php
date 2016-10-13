<?php
namespace plugins\YouBike;

use \PDO;

class GeoYouBike
{
	private $pdo;
	public function __construct() {
		$host = getenv('DB_HOST');
		$dbname = getenv('DB_DATABASE');
		$username = getenv('DB_USERNAME');
		$password = getenv('DB_PASSWORD');
		$dsn = sprintf("pgsql:host=%s;dbname=%s",$host,$dbname);
		$this->pdo = new PDO($dsn,$username,$password);
	}

	public function get_lnglat($lng , $lat, $meter = 1000) {
		$query_string = sprintf("SELECT * FROM geo_youbike WHERE st_dwithin (st_transform(ST_GeomFromText('POINT(%s %s)',4326),900913),st_transform(latlng,900913),%d)",$lng,$lat,$meter);
		$result = $this->pdo->query($query_string);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$str = "";
		$data = array();
		while($row = $result->fetch()) {
			if ($row['act']) {
				$str .= sprintf("[%s %s] %s 有 %d 輛可租借，%d 個空位。\n",$row['sarea'],$row['ar'],$row['sna'],$row['sbi'],$row['bemp']);
			} else {
				$str .= sprintf("[%s %s] %s 暫停使用。\n",$row['sarea'],$row['ar'],$row['sna']);
			}
			$data[$row['sno']] = $row;
		}

		return $str;
	}	

}
