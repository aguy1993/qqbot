<?php

/**
* 
*/
class Roll extends Base
{
	
	function handle()
	{
		$this->respose_msg = '掷出了'.rand(1,100).'点';
		$this->to_json();
	}
}