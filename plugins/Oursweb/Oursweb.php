<?php
namespace plugins\Oursweb;

class Oursweb 
{
    private $key;

    public function __construct () {
        $this->key = getenv('OURSWEB_API_KEY') ?: '<your oursweb api key>';        
    }

    public function get_church_list ($lat, $lng , $isjson = false , $max = 3) {
        $api_url = sprintf("http://church.oursweb.net:51919/%s/latlng/%s,%s,%d",$this->key,$lat,$lng,$max);
        $handle = fopen($api_url,"rb"); 
        $content = stream_get_contents($handle);
        fclose($handle);
        $json = json_decode($content,true);
        if ($isjson) {
            return $json; 
        } else {
            $str = "";
            foreach ($json as $j) {
                $str .= sprintf("%s %s%s%s\n",$j['ocname'],$j['city'],$j['town'],$j['address']);
            }
            return $str;
        }
    }
}
