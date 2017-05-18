<?php
/**
* 检查类
*/
class Check
{
	
	private $db;
	private $json_arr;
	private $admin = ['65728272'];
	private $argument_arr;

	//初始化
	function __construct($db,$qq,$msg)
	{
		$this->db = $db;
		$this->argument_arr = explode(" ",$msg);
		$this->json_arr = [
			"status" => 1,
			"at_qq" =>$qq,
			"msg" => "",
		];

		/* 筛选并执行指令 */
        $this->check_qq_hb($msg,$qq);
        $this->check_ban_date($qq);
		if(count($this->argument_arr) == 1) {
		    $this->check_is_new_face($this->argument_arr[0]);
			/* 处理不带参数的指令 */
			$res = $db->select('muti-command','*',[
				"name" => $msg
			]);
			if(!$res){
//				$this->json_arr['msg'] = "未找到命令：".$this->argument_arr[0];
//				$this->to_json();
                $this->check_is_new_face($msg,1);
			}
		}else{
            $this->check_is_new_face($this->argument_arr[0]);
			/* 处理带参数的指令 */
			$res = $db->select('muti-command','*',[
				"name" => $this->argument_arr[0]
			]);
			if($res){
				$res = $db->select('command','*',[
					"id" => $res[0]['fid']
				]);
				/* 预处理参数数量问题 */
				if($res[0]['max_param'] < count($this->argument_arr)-1){
					$this->json_arr->msg = "参数数量超过限制，请检查指令，可输入'/help 指令名' 查看帮助信息:\n";
					$this->to_json();
				}
			}else{
				$this->check_is_new_face($msg,1);
			}
		}
		$this->check_admin($qq);
	}

	//json转换
	function to_json()
	{
		echo json_encode($this->json_arr,JSON_UNESCAPED_UNICODE);
		exit();
	}

	//判断是否新版表情
	function check_is_new_face($msg,$other=0)
	{
		$new_face_arr = ['我最美','小纠结','骚扰','惊喜','喷血','斜眼笑','卖萌','托腮','无奈','泪奔','doge','笑哭','舔'];

		if($other || in_array($msg, $new_face_arr) || in_array(mb_substr($msg,0,0,'utf-8'), $new_face_arr) || in_array(mb_substr($msg,0,1,'utf-8'), $new_face_arr) || in_array(mb_substr($msg,0,2,'utf-8'), $new_face_arr) || in_array(mb_substr($msg,0,3,'utf-8'), $new_face_arr))
		{
			$this->json_arr['status'] = 'h';
			$this->to_json();
		}
	}

	//返回命令对象
	function get_command_class($msg)
	{
		$res = $this->db->select('muti-command','*',[
			"name" => $msg
		]);
		$res = $this->db->select('command','*',[
			"id" => $res[0]['fid']
		]);
		return $res[0]['class'];
	}

    //检测口令红包
    function check_qq_hb($msg,$qq)
    {
        if(preg_match('/CQ:hb.*\/pay/',$msg)){
            $res = $this->db->update('user',[
                "ban_date" => date('Y-m-d H:i:s',strtotime('+12 hour'))
            ],[
                "qq" => $qq
            ]);
            $this->json_arr['msg'] = "检测到刷金币，关小黑屋10小时~";
            $this->to_json();
        }
    }

    //检查是否封禁
    function check_ban_date($qq)
    {
        $res = $this->db->select('user','ban_date',[
            "qq" => $qq
        ])[0];
        if($res && date('Y-m-d H:i:s',strtotime($res))>date('Y-m-d H:i:s')) {
            $this->json_arr['msg'] = "你被关小黑屋了，解封时间：".date('Y-m-d H:i:s',strtotime($res));
            $this->to_json();
        }elseif ($res && date('Y-m-d H:i:s',strtotime($res))<date('Y-m-d H:i:s')) {
            $res = $this->db->update('user',[
                "ban_date" => null
            ],[
                "qq" => $qq
            ]);
        }
    }

    //检查管理员权限
    function check_admin($qq)
    {
        $res = $this->db->select('muti-command','fid',[
            "name" => $this->argument_arr[0]
        ])[0];
        $res = $this->db->select('command','need_admin',[
            "id" => $res
        ])[0];
        if($res && !in_array($qq,$this->admin)) {
            $this->json_arr['msg'] = "你不是老娘的MASTER，滚";
            $this->to_json();
        }
    }
}