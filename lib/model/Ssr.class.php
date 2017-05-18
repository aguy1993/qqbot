<?php

class Ssr extends Base
{
    private $price = 50;
    public $cq_send_private_msg = [
    	"code" => "2",
    	"msg" => ""
    ];

    function handle()
    {
        $post_data = ['key'=>'4pMYIpFHjikG4mlg'];
        $result = json_decode($this->send_post('http://ssr.coolco.in/getSSR.php', $post_data));
        if(!$result || $result->status != 1){
        	$this->respose_msg .= '获取失败'.$result->msg;
        }else{
        	$this->json_arr['status'] = '3';
        	if($this->qq == '65728272'){
                $this->price = 0;
            }
            $res = $this->pay($this->price);
        	if($res == 0){
        		$this->respose_msg .= '抱歉，你付不起'.$this->price.'枚金币。';
        	}else{
        		$this->respose_msg .= 'SSR获取成功，已发送至QQ，花费'.$this->price.'枚金币，剩余'.$res."枚。\n本次重置时间：".$result->date."，每周六0点重置。\n客户端：http://t.cn/RiUmPnm";
        		$this->cq_send_private_msg['msg'] = "SSR链接为：\nssr://".$result->msg."\n复制后，请用SSR PC客户端导入，导入后可在服务器设置里查看二维码。\n本次更新时间：".$result->date;
        		array_push($this->json_arr['cq'],$this->cq_send_private_msg);
        	}
        	$this->to_json();
        }
    }
}