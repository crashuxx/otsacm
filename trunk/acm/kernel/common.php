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
 * @subpackage 	kernel
 * @version 		3
 */

if(!defined('ACM_ROOT')) exit();
if(defined('ACM_COMMON_INCLUDED')) return; else define('ACM_COMMON_INCLUDED', true);

define('ACM_VERSION', '3.0 Alpha');

if( !file_exists(ACM_ROOT.'config.php') ) {

	header('Location: install.php');
	die('ACM is not installed.');
}

require_once ACM_ROOT.'config.php';

require_once ACM_ROOT.'kernel/functions.php';

// if debug mode on, load additional functions
if( defined('ACM_DEBUG')) require_once ACM_ROOT.'kernel/debug.php';

// Reverse the effect of register_globals
unregister_globals();

$acm_start = explode(' ', microtime());
$acm_start = round(($acm_start[0] + $acm_start[1]), 4);

// Turn off magic_quotes_runtime
set_magic_quotes_runtime(0);

// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if( get_magic_quotes_gpc() )
{
	function stripslashes_array($array)
	{
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);	
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

$db->query('SET CHARACTER SET utf8') or error('Unable to set data base characters encoding', __FILE__, __LINE__, true);

$acm_config = load_config();

ob_start();
ob_implicit_flush(false);

require_once ACM_ROOT.'kernel/class/session.php';
$session = new Session($session_prefix);

require_once ACM_ROOT.'lang/'.$acm_config['lang'].'/common.php';

if( !defined('ACM_ADMIN') ) {
	
	$cur_user['is_guest'] = true;
	
	if( defined('ACM_DEBUG')) $acm_config['timeout_online'] = 36000;
	
	if( $session->user > 0 ) {
		
		if( ($session->time + $acm_config['timeout_online']) > time() && $session->ip == ip2long($_SERVER['REMOTE_ADDR']) ) {
		
			$result = $db->query('SELECT * FROM '.$db->prefix.'accounts WHERE id = '.(int)$session->user.' LIMIT 1');
		
			if( $db->num_rows($result) == 1 ) {
				
				$cur_user = $db->fetch_assoc($result);
				$cur_user['is_guest'] = false;
				$session->time = time();
			}
			else $session->unRegister('user', 'time', 'ip');
			
			$db->free($result);
		}
		else $session->unRegister('user', 'time', 'ip');
	}
}
else {
	
	$cur_user['is_admin'] = false;
	
	if( defined('ACM_DEBUG')) $acm_config['timeout_online'] = 36000;
	
	if( $session->admin == true ) {
		
		if( ($session->atime + $acm_config['timeout_online']) > time() ) {
				
				$cur_user['is_admin'] = true;
				$session->atime = time();
		}
		else $session->unRegister('admin', 'atime', 'aip');
	}
}

?>