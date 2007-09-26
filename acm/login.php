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

define('ACM_ROOT', './');
require ACM_ROOT.'kernel/common.php';

require ACM_ROOT.'lang/'.$acm_config['lang'].'/login.php';

// Logout if someone is login
if( $cur_user['is_guest'] == false ) {
	
	unset($cur_user);
	$cur_user['is_guest'] = true;
	$session->unRegister('user', 'time', 'ip');
}

//if action == logout draw a message box
if( !strcmp( strtolower($_GET['action']), 'logout') ) {

	confirm_referrer();	

	$page_title = $lang_login['Page logout'];
	$redirect = 'login.php';
	message($lang_login['Logout redirect']/*, 'login.php', $lang_common['Login']*/);
}
//	Recovery account/password
else if( !strcmp( strtolower($_GET['action']), 'recovery') ) {
	
	$key = trim($_GET['key']);
	$length = strlen($key);
	
	$link = null;
	$link_title = null;
	$message = $lang_login['E-mail key bad'].' '.'<a href="mailto:'.$acm_config['admin_email'].'" >'.$acm_config['admin_email'].'</a>.';;
	
	if( $length > 32 ) {

		$id = hexdec(substr($key, 0 , ($length-32)));
		
		$tmp = get_cache('recovery_'.$id.'.tmp', 7200);
		del_cache('recovery_'.$id.'.tmp');
		if( !empty($tmp) ) $tmp = unserialize($tmp);
		
		if( !strcmp($tmp[2], $key) && ($tmp[1]+7200) > time() ) {
			
//			$db->query('SELECT id FROM '.$db->prefix.'accounts WHERE id ='.$id.' LIMIT 1');
			$db->query('UPDATE '.$db->prefix.'accounts SET password = "'.$tmp[3].'" WHERE id = '.$id.' LIMIT 1') or error('Unable to update accounts table', __FILE__, __LINE__, true);
			
			$link = 'login.php';
			$link_title =  $lang_common['Login'];
			$message = $lang_login['Pass updated'];
		}
	}
	
	message($message, $link, $link_title);
}
//	Lost account/password
else if( !strcmp( strtolower($_GET['action']), 'lost') ) {
	
	$page_title = $lang_login['Page pass recovery'];
	
	if( $_POST['foo'] == 'bar' ) {

		confirm_referrer();
		
		$link = 'login.php?action=lost';
		$link_title = $lang_common['Go back'];
		
		$email = trim($_POST['email']);
		
		if( function_exists('filter_var') )
		$bEmail = preg_match('/^[0-9a-z\._\-]{1,25}@([0-9a-z\._\-]{1,25}\.[a-z]{2,3})$/' , $email );
		else $bEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

		if( $bEmail ) {
						
			$result = $db->query('SELECT id, password, blocked FROM '.$db->prefix.'accounts WHERE email = "'.$db->escape($email).'" LIMIT 1 ') or error('Unable to fetch accounts table', __FILE__, __LINE__, true);
			
			if( $db->num_rows($result) ) {
				
				$row = $db->fetch_assoc($result);
				
				if( !get_cache('recovery_'.$row['id'].'.tmp', 7200) ) {
				
					$password = random_chars(12);
					$key = md5( rand().uniqid(time(), true).$row['id'] );
					$key = sprintf("%x%s", $row['id'], $key);
									
					set_cache('recovery_'.$row['id'].'.tmp', serialize( array($row['id'], time(), $key, ($acm_config['use_md5'] ? md5($password) : $password ) ) ) );
					
					$mail = file_get_contents(ACM_ROOT.'lang/'.$acm_config['lang'].'/mail/activate_password.tpl');
					$mail = str_replace('<title>', $acm_config['title'], $mail);
					$mail = str_replace('<base_url>', $acm_config['base_url'], $mail);
					$mail = str_replace('<account>', $row['id'], $mail);
					$mail = str_replace('<new_password>', $password, $mail);
					$mail = str_replace('<activation_url>', $acm_config['base_url'].'/login.php?action=recovery&key='.$key, $mail);
		
					sendMail($email, $lang_login['Recovery e-mail subject'].' '.$acm_config['title'], $mail);
						
					$message = $lang_login['Recovery e-mail sent'].' '.'<a href="mailto:'.$acm_config['admin_email'].'" >'.$acm_config['admin_email'].'</a>.';
					$link = 'login.php';
					$link_title =  $lang_common['Login'];
				}
				else $message = $lang_login['E-mail already send'].' '.'<a href="mailto:'.$acm_config['admin_email'].'" >'.$acm_config['admin_email'].'</a>.';
			}
			else $message = $lang_login['No e-mail match'];
		}
		else $message = $lang_login['E-mail invalid'];
		
		message($message, $link, $link_title);
	}
	else {	// Lost password form
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<form method="post" action="login.php?action=lost">
	<table class="tableform">
	<tr>
		<th><?php echo $lang_login['email']; ?></th>
		<td><input type="text" name="email" value="" /></td>
	</tr>
	<tr>
    	<th></th>
    	<td>
    	<input type="hidden" name="foo" value="bar" />
		<input type="submit" value="<?php echo $lang_login['recovery']; ?>" />
		<a href="login.php"><?php echo $lang_common['Go back']?></a>
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
else {
	
	$page_title = $lang_login['Page login'];
	
	// if submit login form
	if( $_POST['foo'] == 'bar' ) {

		confirm_referrer();

		$message = $lang_login['invalid'];
		$link = 'login.php';
		$link_title = $lang_common['Go back'];

		$login = (int)$_POST['login'];
		
		if( $login > 0 ) {
		
			// Search for user in db
			$result = $db->query('SELECT id, password, blocked FROM '.$db->prefix.'accounts WHERE id = '.(int)$login.' LIMIT 1 ') or error('Unable to fetch accounts table', __FILE__, __LINE__, true);

			if( $db->num_rows($result) == 1 ) {
				
				$cur_user = array_merge($cur_user, $db->fetch_assoc($result));
	
				if( !strcmp( $cur_user['password'], ( $acm_config['use_md5'] ? md5($_POST['pass']) : $_POST['pass'] ) )  ) {
					
					$redirect = 'players.php';
					$cur_user['is_guest'] = false;
				
					$session->user = $cur_user['id'];
					$session->time = time();
					$session->ip = ip2long($_SERVER['REMOTE_ADDR']);
					
					$message = $lang_login['Login redirect']; 
					unset($link);
					unset($link_title);
				}
				else $cur_user['is_guest'] = true;
			}	//	$db->num_rows($result) == 1 
		}	//	$login > 0

		message($message, $link, $link_title);
	}	// submit login form
	// login form
	else {
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<form method="post" action="login.php">
	<table class="tableform">
	<tr>
		<th><?php echo $lang_login['login']; ?></th>
		<td><input type="password" name="login" value="" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_login['password']; ?></th>
		<td><input type="password" name="pass" value="" /></td>
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