<?php

require_once 'config.php';
require_once 'Medoo.php';
require_once 'model/Base.class.php';

$db = new medoo([
    // required
    'database_type' => DB_TYPE,
    'database_name' => DB_DBNAME,
    'server' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PWD,
    'charset' => DB_CHARSET,

    // optional
    'port' => 3306,
    // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
    'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

function __autoload($class)   
{   
    require_once('model/'.$class.'.class.php');   
}

function characet($data)
{
  if(!empty($data))
  {
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8')
    {
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}
