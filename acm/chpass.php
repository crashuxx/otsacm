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
 * @subpackage 	Mod
 * @version 		3
 */

define('ACM_ROOT', './');
require ACM_ROOT.'kernel/common.php';

if( $cur_user['is_guest'] == true ) redirect('login.php');

$page_title = $lang_common['Page chpass'];
	
if( $_POST['foo'] == 'bar' ) {
	
	$old = trim($_POST['oldpass']);
	$old = $acm_config['use_md5'] ? md5($old) : $old ;
	
	$new = trim($_POST['newpass']);
	$confirm = trim($_POST['confirmpass']);
	
	if( !strcmp( $cur_user['password'], $old) ) {
		
		if( strlen($new) >= $acm_config['pass_min_length'] ) {
			
			if( !strcmp( $new, $confirm) ) {
				
				$db->query('UPDATE '.$db->prefix.'accounts SET password = "'.($acm_config['use_md5'] ? md5($new) : $db->escape($new)).'" WHERE id = '.$cur_user['id'].' LIMIT 1') or error('Unable to update account info', __FILE__, __LINE__, true);
				
				//logout
				unset($cur_user);
				$cur_user['is_guest'] = true;
				$session->unRegister('user', 'time', 'ip');
				
				message($lang_common['Pass updated'], 'login.php', $lang_common['Login']);
			}
			else message($lang_common['Pass not match'], 'chpass.php');
		}
		else message( str_replace('<pass_min_length>', $acm_config['pass_min_length'], $lang_common['Pass too short']), 'chpass.php');
	}
	else message($lang_common['Wrong old pass'], 'chpass.php');
}
else {
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<form method="post" action="chpass.php">
	<table class="tableform">
	<tr>
		<th><?php echo $lang_common['Old pass']; ?></th>
		<td><input type="password" name="oldpass" value="" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_common['New pass']; ?></th>
		<td><input type="password" name="newpass" value="" /></td>
	</tr>
		<tr>
		<th><?php echo $lang_common['Confirm new pass']; ?></th>
		<td><input type="password" name="confirmpass" value="" /></td>
	</tr>
	<tr>
		<td></td>
    	<td>
    	<input type="hidden" name="foo" value="bar" />
		<input type="submit" value="<?php echo $lang_common['submit']; ?>" />
		<input type="reset" value="<?php echo $lang_common['reset']; ?>" />
		</td>
	</tr>
	</table>
	</form>
	</div>
</div>
</div>
<?php
}

$page_style = 'chpass';
require ACM_ROOT.'kernel/finalize.php';

?>