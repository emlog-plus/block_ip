<?php
/**
 * IP黑名单插件
 * @copyright (c) crazyus.ga All Rights Reserved
 */
if(!defined('EMLOG_ROOT')) {exit('error!');}
function callback_init(){
	$sql ="
create table if not exists `".DB_PREFIX."block` (
`id` int(10) unsigned NOT NULL auto_increment,
`date` int(10) NOT NULL default '0',
`serverip` varchar(200) NOT NULL default '',
UNIQUE KEY `serverip` (`serverip`),
KEY `block` (`id`)
)ENGINE=MyISAM;";
	$DB = Database::getInstance();
	$DB->query($sql);
}


function callback_rm(){
$DB = Database::getInstance();
$query = $DB->query("DROP TABLE IF EXISTS ".DB_PREFIX."block");

}