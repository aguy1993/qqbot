<?php
require_once 'lib/init.php';

header( 'Content-Type: application/json;charset=utf8' );
header( 'Pragma: no-cache' );
header( 'Cache-Control: no-cache, no-store, max-age=0' );
header( 'Expires: 1L' );

date_default_timezone_set('PRC');

if(!isset($_POST['key'])){
	exit('非法授权！');
}elseif(!isset($_POST['msg'])){
	exit('缺少参数msg');
}elseif(!isset($_POST['qq'])){
	exit('缺少参数msg');
}elseif($_POST['key'] != $key){
	exit('缺少参数msg');
}else{
	$msg = characet($_POST['msg']);
	$qq = $_POST['qq'];
}

$msg = trim($msg);
$msg = substr($_POST['msg'], 1);
$arr = explode(" ",$msg);

$check = new Check($db,$qq,$msg);

$command_class = $check->get_command_class($arr[0]);

$command = new $command_class($db,$qq,$msg);

/*$json_arr = [
    "status" => 1, //h不处理，0异常，1单消息，2纯q操作，3混合
    "msg" => $msg,//返回消息主体
    "at_qq" => $qq,//@qq
    "cq" => [
        //cq操作1
        //cq操作2
        //... cq操作...
    ]
];

echo json_encode($json_arr,JSON_UNESCAPED_UNICODE);
exit();*/

?>