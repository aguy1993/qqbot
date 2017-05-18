<?php

class Pay extends Base
{
    private $rand_user;
    private $user_money;

    function handle()
    {
        $this->get_user_money();
        $this->argument_arr[2] = abs($this->argument_arr[2]);
        $this->check_argument();
        $this->pay_random();
        $qq = $this->exit_user($this->argument_arr[1]);
        if(!$qq){
            $this->respose_msg .= '用户不存在哦。';
            $this->to_json();
        }elseif ($this->check_ban($qq)){
            $this->respose_msg .= 'PY对象被关小黑屋了，暂时无法交易。';
            $this->to_json();
        }elseif ($qq == $this->qq){
            $this->respose_msg .= '不能付款给自己哦~';
            $this->to_json();
        }
        $pay_money = ceil($this->argument_arr[2]) + $this->count_charge($this->argument_arr[2]);
        $res = $this->pay_qq($qq,ceil($this->argument_arr[2]));
        if(!$res){
            $this->respose_msg .= '发生异常，数据错误~';
            $this->to_json();
        }else{
            $this->respose_msg .= '向[CQ:at,qq='.$qq.'] 支付了'.ceil($this->argument_arr[2]). '枚金币，手续费'.$this->count_charge($this->argument_arr[2]).'枚金币~';
            $this->to_json();
        }

    }

    function check_argument()
    {
        if(count($this->argument_arr) == 1) {
            $this->respose_msg = "请指定收款人和金币数哦~";
            $this->to_json();
        }elseif (count($this->argument_arr) == 2) {
            $this->respose_msg = "请指定金币数哦~";
            $this->to_json();
        }elseif (!is_numeric($this->argument_arr[2]) || $this->argument_arr[2] <= 0){
            $this->respose_msg = "请输入正确的金币数量~";
            $this->to_json();
        }elseif ($this->user_money < ($this->argument_arr[2] + $this->count_charge($this->argument_arr[2]))){
            $this->respose_msg = "穷逼，没钱还学人转账";
            $this->to_json();
        }else{
            return 0;
        }
    }

    function pay_random(){
        if($this->argument_arr[1] == 'random') {
            $this->rand_user = $this->db->query("SELECT * FROM user WHERE qq != ".$this->qq." ORDER BY RAND() LIMIT 1")->fetchAll()[0];
            $res_a = $this->db->update('user',[
                "money[-]" => $this->argument_arr['2']
            ],[
                "qq" => $this->qq
            ]);
            $res_b = $this->db->update('user',[
                "money[+]" => $this->argument_arr['2']
            ],[
                "qq" => $this->rand_user['qq']
            ]);
            if(!$res_a || !$res_b){
                $this->respose_msg .= '发生异常，数据错误~';
                $this->to_json();
            }else{
                $this->respose_msg .= '向[CQ:at,qq='.$this->rand_user['qq'].'] 支付了'.$this->argument_arr['2']. '枚金币~';
                $this->to_json();
            }
        }
    }

    function pay_qq($qq,$money) {
        $loss = $money + $this->count_charge($money);
        $res_a = $this->db->update('user',[
            "money[-]" => $loss
        ],[
            "qq" => $this->qq
        ]);
        $res_b = $this->db->update('user',[
            "money[+]" => $money
        ],[
            "qq" => $qq
        ]);
        return $res_a+$res_b;
    }

    function get_user_money() {
        $this->user_money = $this->db->select('user','money',[
            "qq" => $this->qq
        ])[0];
    }

    //计算手续费
    function count_charge($pay_money) {
        $min = 10;
        $max = 1000;
        $commission_charge = ceil(ceil($pay_money) * 0.1);
        if($commission_charge < $min){
            $commission_charge = $min;
        }elseif($commission_charge > $max){
            $commission_charge = $max;
        }
        return $commission_charge;
    }

}