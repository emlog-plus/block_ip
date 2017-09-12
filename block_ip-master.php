<?php
/*
Plugin Name: 防CC攻击
Version: 0.1
Plugin URL: https://github.com/emlog-plus/block_ip
Description: 简单有效的防止CC攻击,可以自行填写IP黑名单
Author: Flyer
Author Email: gao.eison@gmail.com
Author URL: https://crazyus.ga
*/
!defined('EMLOG_ROOT') && exit('access deined!');

function block_ip(){
	 echo '<li><a href="./plugin.php?plugin=block_ip-master" id="block-ip">防CC攻击</a></li>';
}
addAction('adm_sidebar_ext', 'block_ip');
function checkip() {  
include(EMLOG_ROOT.'/content/plugins/block_ip-master/block_ip-master_config.php');
$block_ip = strtolower($block_ip);
$block_ip_array = explode(",", $block_ip);
$banned_ip = $block_ip_array;
if ( in_array( getenv("REMOTE_ADDR"), $banned_ip ) )
{
echo "<script>alert('$block_des');location.href = \"http://127.0.0.1\";</script>";
}
}  
function ip_cc(){
include(EMLOG_ROOT.'/content/plugins/block_ip-master/block_ip-master_config.php');
if ($is_via == 'true') {
empty($_SERVER['HTTP_VIA']) or exit('Access Denied');
}
session_start();
$seconds = $block_second; 
$refresh = $block_refresh; 
$cur_time = time();
if(isset($_SESSION['last_time'])){
$_SESSION['refresh_times'] += 1;
}else{
$_SESSION['refresh_times'] = 1;
$_SESSION['last_time'] = $cur_time;
}
//处理监控结果
if($cur_time - $_SESSION['last_time'] < $seconds){
if($_SESSION['refresh_times'] >= $refresh){
//跳转至攻击者服务器地址
header(sprintf('Location:%s', 'https://127.0.0.1'));
$DB = Database::getInstance();
$ip=getIp();
$date=time();
$DB->query("INSERT IGNORE INTO `".DB_PREFIX . "block` (`date`,`serverip`) values ('$date','$ip')"); 
exit('Access Denied');
}
}else{
$_SESSION['refresh_times'] = 0;
$_SESSION['last_time'] = $cur_time;
}
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if(!headers_sent()) {
        header("Status: 301 Moved Permanently");
                header(sprintf(
            'Location: https://%s%s',
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI']
            ));
        exit();
        }
        }
 }
 addAction('index_head', 'ip_cc');
  addAction('index_head', 'checkip');
 
