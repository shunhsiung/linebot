<?php
namespace plugins\YouBike;

class YouBike
{
    private $taipei_url = "http://data.taipei/youbike";
    private $new_taipei_url = "http://data.ntpc.gov.tw/od/data/api/54DDDC93-589C-4858-9C95-18B2046CC1FC?%24format=json";

/*
    json 格式內容
    sno:站點代號
    sna:場站名稱(中文)
    tot:場站總停車格
    sbi:可借車位數
    sarea:場站區域(中文)
    mday:資料更新時間
    lat:緯度
    lng:經度
    ar:地址(中文)
    sareaen:場站區域(英文)
    snaen:場站名稱(英文)
    aren:地址(英文)
    bemp:可還空位數
    act:場站是否暫停營運
*/
    public function get_taipei_data () 
    {
        $handle = fopen($this->taipei_url,"rb");
        $content = stream_get_contents($handle);
        fclose($handle);

        $content = gzdecode($content);
        return json_decode($content,true);
    }

    public function get_new_taipei_data()
    {
        $handle = fopen($this->new_taipei_url,"rb");
        $content = stream_get_contents($handle);
        fclose($handle);

        return json_decode($content,true);
    }

    public function save_fixed_data ( $json )
    {
        // 第一次執行寫入
    }

    public function save_change_data ( $json )
    {
        // 每五分鐘更新變動資料 
    }
}
