<?php

/**
 * [ACM]Account Manager
 * 
 * Account Manager for OpenTibia Server
 * 
 * PHP versions 5
 *
 * Copyright (c) 2006-2007 Lukasz Pajak
 * 
 * LICENSE:
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * @author 		Lukasz Pajak <droopsik@gmail.com>
 * @copyright 		2006-2007 Lukasz Pajak
 * @license		GPL 
 * @package		acm
 * @version 		3
 */

if( isset($_SERVER['REMOTE_ADDR']) || !empty($_SERVER['REMOTE_ADDR']) ) die('Access Denied');

define('ACM_ROOT', './');
require_once ACM_ROOT.'config.php';

if( !file_exists(ACM_ROOT.'config.php') ) die('ACM is not installed.');

// if debug mode on, load additional functions
if( defined('ACM_DEBUG')) require_once ACM_ROOT.'kernel/debug.php';

function error($message, $file, $line, $sql = false)
{
	echo $message."\n";
	echo file.' '. $line ."\n";
	if( $sql == true ) echo $GLOBALS['db']->error()."\n"; 
	
	die();
}

switch($db_type) {

	case 'mysql':
		require_once ACM_ROOT.'kernel/class/db/mysql.php';
		$db = new MySQLLayer($db_host, $db_username, $db_password, $db_name, $db_prefix);
		break;

	case 'mysqli':
		require_once ACM_ROOT.'kernel/class/db/mysqli.php';
		$db = new MySQLiLayer($db_host, $db_username, $db_password, $db_name, $db_prefix);
		break;

	case 'sqlite':
		require_once ACM_ROOT.'kernel/class/db/sqlite.php';
		$db = new SQLiteLayer($db_file, $db_prefix);
		break;

	default:
		error('Unknown database type. DB type: '.$db_type, __FILE__, __LINE__);
}

$dir = opendir(ACM_ROOT.'cache/');
if( $dir ) {

	while( ($file = readdir($dir)) )
	{
		if( is_file(ACM_ROOT.'cache/'.$file) && !preg_match("/^\.htaccess|index\.html/i", $file)) {
			
			unlink(ACM_ROOT.'cache/'.$file);
			echo 'Deleted '.ACM_ROOT.'cache/'.$file."\n";
		}
	}
	closedir($dir);
}


echo 'DROP TABLE '.$db->prefix.'acm_config...'."\n";
$db->query('DROP TABLE '.$db->prefix.'acm_config');

echo 'DROP TABLE '.$db->prefix.'acm_containers...'."\n";
$db->query('DROP TABLE '.$db->prefix.'acm_containers');

echo 'DROP TABLE '.$db->prefix.'acm_profiles...'."\n";
$db->query('DROP TABLE '.$db->prefix.'acm_profiles');

unlink(ACM_ROOT.'config.php');
echo 'Deleted '.ACM_ROOT.'config.php'."\n";

echo "\n".'uninstaled'."\n";

?>