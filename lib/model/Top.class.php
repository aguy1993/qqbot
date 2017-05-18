<?php

class Top extends Base
{
    private $title;
    private $val;
    private $res;
    private $field;
    private $limit = 10;

    function handle()
    {
        $arr = explode(" ",$this->msg);
        if(count($arr) == 1) {
            $arr[1] = 'money';
        }

        if($arr[1] == 'money' || $arr[1] == '金币'){
            $this->res = $this->db->select('user','*',[
                "ORDER" => "money DESC,id ASC",
                "LIMIT" => $this->limit
            ]);
            $this->title = 'MONEY';
            $this->val = '金币';
            $this->field = 'money';
        }elseif($arr[1] == 'ce' || $arr[1] == '战斗力'){
            $this->res = $this->db->select('user','*',[
                "ORDER" => "combat_effectiveness DESC,id ASC",
                "LIMIT" => $this->limit
            ]);
            $this->title  = 'CE';
            $this->val = '战斗力';
            $this->field = 'combat_effectiveness';
        }elseif($arr[1] == 'level' || $arr[1] == '等级'){
            $this->res = $this->db->select('user','*',[
                "ORDER" => "level DESC,id ASC",
                "LIMIT" => $this->limit
            ]);
            $this->title  = 'LEVEL';
            $this->val = '等级';
            $this->field = 'level';
        }elseif($arr[1] == 'sign' || $arr[1] == '签到'){
            $this->res = $this->db->select('user','*',[
                "ORDER" => "count_sign_day DESC,count_all_day DESC",
                "LIMIT" => $this->limit
            ]);
            $this->title  = 'SIGN';
            $this->val = '连续签到';
            $this->field = 'count_sign_day';
        }else{
            $this->respose_msg .= ('未找到参数：'.$arr[1]);
            $this->to_json();
        }
        $this->respose_msg .=  "\n-----------------TOP-".$this->title ."-------------\n";
        $i = 1;
        //print_r($res);
        foreach ($this->res as $key) {
            $this->respose_msg .=  $i.". ";
            $this->respose_msg .=  ($key['name'])?$key['name']:'未设置昵称';
            $this->respose_msg .=  " - ".$this->val." ".$key[$this->field]."\n";
            $i++;
        }
        $this->respose_msg .=  ("--------------------END--------------------");
        $this->to_json();
    }
}