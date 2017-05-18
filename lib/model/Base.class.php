<?php

class Base
{
	
	public $db;
	public $json_arr;
	public $argument_arr;
	public $table;

	public $qq;
	public $msg;
	public $respose_msg;

	public $cq_card;
	public $cq_img;
	public $cq_send_group_msg;
	public $cq_send_private_msg;
	public $cq_set_title;

	public $need_validate;
	public $max_param;

	//初始化参数
	function __construct($db,$qq,$msg)
	{
		$this->db = $db;
		$this->qq = $qq;
		$this->msg = $msg;
		$this->argument_arr = explode(" ",$msg);
		$this->json_arr = [
			"status" => 1, //h不处理，0异常，1单消息，2纯q操作，3混合
			"msg" => "",//返回消息主体
			"at_qq" => $qq,//@qq
			"cq" => [
			//cq操作1
			//cq操作2
			//... cq操作...
			]
		];
		$this->handle();
	}

	//初始化用户数据到数据库
	function exists_user()
	{
		$res = $this->db->has('user',[
			"qq" => $this->qq
		]);
		if(!$res){
			$this->db->insert('user',[
				"qq" => $this->qq,
				"reg_date" =>  date('Y-m-d H:i:s')
			]);
			$this->respose_msg = "数据初始化成功!\n";
			$this->json_arr['status'] = 3;
		}
	}

	//处理类
	function handle()
	{
		return 0;
	}

	//json转换
	function to_json()
	{
	    $this->json_arr['msg'] = $this->respose_msg;
		echo json_encode($this->json_arr,JSON_UNESCAPED_UNICODE);
		exit();
	}
    //发生post请求
	function send_post($url, $post_data) {
		$postdata = http_build_query($post_data);
		$options = array(
			'http' => array(
			'method' => 'POST',
			'header' => 'Content-type:application/x-www-form-urlencoded',
			'content' => $postdata,
			'timeout' => 10 // 超时时间（单位:s）
			)
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		return $result;
	}

	//扣款
	function pay($price) {
		$god = $this->db->select('user','money',[
			"qq" => $this->qq
		])[0];
		if($god == null || $god < $price){
			return 0;
		}else{
			$res = $this->db->update('user',[
				"money[-]" => $price 
			],[
				"qq" => $this->qq
			]);
			return $god-$price;
		}
	}
	//查询用户是否存在
    function exit_user($qq_or_name) {
        $res = $this->db->select('user','qq',[
            "OR" => [
                "qq" => $qq_or_name,
                "name" => $qq_or_name
            ]
        ])[0];
        return $res;
    }
    //查询用户是否在封禁中
    function check_ban($qq_or_name) {
	    $res = $this->db->select('user','ban_date',[
            "OR" => [
                "qq" => $qq_or_name,
                "name" => $qq_or_name
            ]
        ])[0];
	    if($res && date('Y-m-d H:i:s',strtotime($res))>date('Y-m-d H:i:s')) {
	        return 1;
        }else{
	        return 0;
        }
    }
}