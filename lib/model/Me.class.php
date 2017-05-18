<?php

class Me extends Base
{
    private $illegal_name = ['horo','小可','管理员','admin','江泽民','习近平','无名氏','random'];

    function handle()
    {
        $arr = explode(" ",$this->msg);
        if(count($arr) == 1 ) {
            $res = $this->db->select('user','*',[
                "qq" => $this->qq
            ]);
            if(!$res){
                $this->exists_user();
                $this->handle();
            }
            $res = $res[0];
            
            $this->respose_msg .=  "\n-----------------PROFILE-----------------\n";
            $this->respose_msg .=  "昵称：";
            $this->respose_msg .=  ($res['name'])?$res['name']:"未设置昵称";
            $this->respose_msg .=  "\nLevel：".$res['level']."(EXP ".$res['exp']." )\n";
            $this->respose_msg .=  "战斗力：".$res['combat_effectiveness']."\n";
            $this->respose_msg .=  "幸运值：".$res['lucky']."\n";
            $this->respose_msg .=  "金币：".$res['money']."\n";
            $this->respose_msg .=  "连续签到：".$res['count_sign_day']."天(可补签".$res['re_sign_day']."天) 总签到：".$res['count_all_day']."天\n";
            $this->respose_msg .=  "--------------------END--------------------";
            $this->to_json();
        }
        if($arr[1] == 'name' && count($arr) == 3){
            $res = $this->db->select('user','*',[
                "qq" => $this->qq
            ]);
            if(!$res){
                add_user($this->db,$this->qq);
                me($this->db,$this->qq,$this->msg);
            }
            if(strlen($arr[2]) > 16 || strlen($arr[2]) < 2){
                $this->respose_msg .= '名字长度只能2-16个字符哦~';
                $this->to_json();
            }
            if(in_array($arr[2], $this->illegal_name)){
                $this->respose_msg .= ('和谐词汇~	已禁止');
                $this->to_json();
            }
            $if_has = $this->db->has('user',[
                "name" => $arr[2]
            ]);
            if($if_has){
                $this->respose_msg .= '名称已存在~';
                $this->to_json();
            }
            $res = $this->db->update('user',[
                "name" => $arr[2]
            ],[
                "qq" => $this->qq
            ]);
            if($res){
                $this->respose_msg .= ("已将名称设置为：".$arr[2]);
                $this->to_json();
            }else{
                $this->respose_msg .= ("遇到异常错误~");
                $this->to_json();
            }
        }
        $this->respose_msg .= '参数错误';
        $this->to_json();
    }
}