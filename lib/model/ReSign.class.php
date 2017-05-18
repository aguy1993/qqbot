<?php

class ReSign extends Base
{
    function handle()
    {
        $res = $this->db->select('user','*',[
            "qq" => $this->qq
        ])[0];

        if($res['re_sign_day'] == 0){
            $this->respose_msg .= '你的可补签天数为0，无法补签';
            $this->to_json();
        }
        if(!isset($this->argument_arr[1])) {
            $this->respose_msg .= '你的可补签天数为'.$res['re_sign_day'].'，补签需要花费'.($res['re_sign_day']*10).'金币，确认请输入/resign confirm';
            $this->to_json();
        }
        if($this->argument_arr[1] == 'confirm') {
            $result = $this->pay($res['re_sign_day']*10);
            if(!$result) {
                $this->respose_msg .= '补签失败，你付不起这么钱';
            }else{
                $this->re_sign($res['re_sign_day']);
                $this->respose_msg .= "补签成功\n，花费".($res['re_sign_day']*10).'金币，补签后连续签到天数：'.($res['count_sign_day']+$res['re_sign_day']).'天！';
            }

            $this->to_json();
        }
    }

    function re_sign($day)
    {
        $res = $this->db->update('user',[
            "count_sign_day[+]" => $day,
            "re_sign_day" => 0
        ],[
            "qq" => $this->qq
        ]);
    }

}