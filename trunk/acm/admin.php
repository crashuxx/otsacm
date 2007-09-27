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
 * @subpackage 	ModAdmin
 * @version 		3
 */

define('ACM_ADMIN', true);
define('ACM_ROOT', './');
require ACM_ROOT.'kernel/common.php';

require ACM_ROOT.'lang/'.$acm_config['lang'].'/login.php';

if( $cur_user['is_admin'] == true ) {
	
	$session->unRegister('admin', 'atime', 'aip');
	$cur_user['is_admin'] = false;
}

//if action == logout draw a message box
if( !strcmp( strtolower($_GET['action']), 'logout') ) {

	$page_title = $lang_login['Page logout'];
	$redirect = 'index.php';
	message($lang_login['Logout redirect']);
}
else {
	
	$page_title = $lang_login['Page login'];
	
	// if submit login form
	if( $_POST['foo'] == 'bar' ) {
		
		confirm_referrer();

		$message = $lang_login['invalid'];
		$link = 'login.php';
		$link_title = $lang_common['Go back'];

		$login = $_POST['login'];
		
		if( !strcmp( $_POST['password'], $acm_config['admin_password'] ) &&  !strcmp( $_POST['login'], $acm_config['admin_login'] ) ) {

			$redirect = 'admin_options.php';
			$cur_user['is_admin'] = true;
				
			$session->admin = true;
			$session->atime = time();
			$session->aip = ip2long($_SERVER['REMOTE_ADDR']);
					
			$message = $lang_login['Login redirect']; 
			unset($link);
			unset($link_title);
		}
		else $cur_user['is_admin'] = false; 

		message($message, $link, $link_title);
	}	// submit login form
	// login form
	else {
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<form method="post" action="admin.php">
	<table class="tableform">
	<tr>
		<th><?php echo $lang_login['login']; ?></th>
		<td><input type="text" name="login" value="" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_login['password']; ?></th>
		<td><input type="password" name="password" value="" /></td>
	</tr>
	<tr>
    	<th><a href="login.php?action=lost"><?php echo $lang_login['lost pass']?></a></th>
    	<td>
    	<input type="hidden" name="foo" value="bar" />
		<input type="submit" value="<?php echo $lang_login['submit']; ?>" />
		<input type="reset" value="<?php echo $lang_login['reset']; ?>" />
		</td>
	</tr>
	</table>
	</form>
	</div>
</div>
</div>
<?php
	}
}


$page_style = 'login';
require ACM_ROOT.'kernel/finalize.php';

?>