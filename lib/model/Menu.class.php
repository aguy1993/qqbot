<?php

/**
 * Created by PhpStorm.
 * User: horo
 * Date: 2016/12/25 0025
 * Time: 12:38
 */
class Menu extends Base
{
    function handle()
    {
        $res = $this->db->select('command','*',[
            "AND" => [
                "need_admin" => 0,
                "is_hidden" => 0,
            ],
            "ORDER" => "order ASC"
        ]);
        $this->respose_msg .= "\n-----------------MENU-----------------\n";
        $i = 1;
        foreach ($res as $key) {
            $this->respose_msg .= $i.'. /'.$key['name'].'  -  '.$key['ch_name'].'  -  '.$key['help_info']."\n";
            $i++;
        }
        $this->respose_msg .= "-------------------END------------------";
        $this->to_json();
    }
}