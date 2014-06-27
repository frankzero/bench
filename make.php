<?php
/*
    系統 初始化
    產生1萬張卡 
    1. 寫到檔案
    2. 寫到 mysql
    3. 寫到 mongodb
*/
//phpinfo();exit;
error_reporting(E_ALL);
define('ROOTPATH','/var/www/frank/');
define('APPPATH','/var/www/frank/bench/');

define('DBHOST','localhost');
define('DBNAME','bench');
define('DBUSER','frank');
define('DBPASSWORD','10281028');

include(APPPATH.'lib.php');
$sw = new __stopwatch();

//pdo_test();
create_card(10000);

//clear();
//$d = file_get_contents(APPPATH.'card/'.rand(0,9999));$d = json_decode($d,true);show($d);

//pdo_test2();

echo $sw->stop();



