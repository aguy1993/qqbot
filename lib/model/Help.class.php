<?php

class Help extends Base
{
    function handle()
    {
        $arr = explode(" ",$this->msg);
        if(count($arr) == 1 ) {
            $this->respose_msg .= '输出/help 指令名，查看帮助信息';
        }else{
            $res = $this->db->select('muti-command','*',[
                "name" => $arr[1]
            ]);
            if(!$res) {
                exit('未找到指令: '.$arr[1].',请输入/菜单，查看所有可用指令');
            }else{
                $res = $this->db->select('command','*',[
                    "AND" => [
                        'id' => $res[0]['fid'],
                        'need_admin' => 0
                    ]
                ]);
                if(!$res){
                    $this->respose_msg .= '未开放查看改指令的权限';
                    $this->to_json();
                }
                $names = $this->db->select('muti-command','name',[
                    "fid" => $res[0]['id']
                ]);
                $name_str = '';
                foreach ($names as $key) {
                    $name_str .= $key.',';
                }
                $this->respose_msg .= "\n-----------------HELP-----------------\n";
                $this->respose_msg .= "指令名：".$res[0]['name']."(".$res[0]['ch_name'].")\n";
                $this->respose_msg .= "说明：\n";
                $this->respose_msg .= $res[0]['help_info']."\n";
                $this->respose_msg .= "用法：\n";
                $this->respose_msg .= $res[0]['use_method']."\n";
                $this->respose_msg .= "近义词：\n";
                $this->respose_msg .= rtrim($name_str,',')."\n";
                $this->respose_msg .= "-------------------END------------------";
            }
        }
        $this->to_json();
    }
}