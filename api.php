<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
define('ROOTPATH','/var/www/vhosts/default/htdocs/');
define('APPPATH','/var/www/vhosts/default/htdocs/bench/');

define('DBHOST','localhost');
define('DBNAME','bench');
define('DBUSER','frank');
define('DBPASSWORD','10281028');
include(APPPATH.'lib.php');


//print_r($_POST);

$cmd = $_POST['cmd'];

switch($cmd){
    case 'update':
        $conn = getconn();
        $field = $_POST['field'];
        $value = $_POST['value'];
        $id = $_POST['id'];
        $conn->query("update card set $field = ? where id = ?",array($value, $id));
        break;
    
    case 'delete':
        $conn = getconn();
        $id = $_POST['id'];
        $conn->query('delete from card where id=?',array($id));
        break;
    
    case 'create':
        create_card(10000);
        break;
        
    default:
        break;
}