<?php

class Ban extends Base
{
    function handle()
    {
        if (count($this->argument_arr) == 3 && $this->argument_arr[0] == 'ban') {
            //处理ban
            $qq = $this->exit_user($this->argument_arr[1]);
            if(!qq) {
                $this->respose_msg .= '未找到'.$this->argument_arr[1];
                $this->to_json();
            }
            $this->ban_user($qq,$this->argument_arr[2]);
        }elseif (count($this->argument_arr) == 2 && $this->argument_arr[0] == 'deban'){
            //处理deban
            $qq = $this->exit_user($this->argument_arr[1]);
            if(!qq) {
                $this->respose_msg .= '未找到'.$this->argument_arr[1];
                $this->to_json();
            }
            $this->deban_user($qq);
        }else {
            $this->respose_msg .= '使用姿势不对哦~';
        }
        $this->to_json();
    }

    function ban_user($qq,$date) {
        $res = $this->db->update('user',[
            "ban_date" => date('Y-m-d H:i:s',strtotime($date))
        ],[
            "qq" => $qq
        ]);
        $res = $this->db->select('user','bandate',[
            "qq" => $qq
        ])[0];
        $this->respose_msg .= '管理员将[CQ:at,qq='.$qq.']关进了小黑屋';
    }

    function deban_user($qq) {
        $res = $this->db->update('user',[
            "ban_date" => null
        ],[
            "qq" => $qq
        ]);
        $this->respose_msg .= '管理员从小黑屋中释放了[CQ:at,qq='.$qq.']';
        return $res;
    }
}