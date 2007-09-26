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

if( $cur_user['is_guest'] == false ) redirect('players.php');

require ACM_ROOT.'lang/'.$acm_config['lang'].'/register.php';

$page_title = $lang_register['Page register'];
	
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
<?php

if( $_POST['foo'] == 'bar' ) {
		
	if( function_exists('filter_var') )
		$bEmail = preg_match('/^[0-9a-z\._\-]{1,25}@([0-9a-z\._\-]{1,25}\.[a-z]{2,3})$/' , $_POST['email'] );
	else $bEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	
	$link = 'register.php';
	$link_title =  $lang_common['Go back'];
	
	if( $bEmail ) {
		
		$email = strtolower(trim($_POST['email']));

		if( !strcmp( strtolower($_POST['email']), strtolower($_POST['cemail']) ) ) {
		
			$db->query('SELECT id FROM '.$db->prefix.'accounts WHERE email = "'.$db->escape($email).'" LIMIT 1');
			
			if( $db->num_rows() == 0 ) {
				
				$db->query('SELECT id FROM '.$db->prefix.'accounts');
				while( $row = $db->fetch_row() ) $exist[ $row[0] ] = true;
				
				$min = $acm_config['acc_min_number'];
				$max = $acm_config['acc_max_number'];
				
				$start = rand($min, $max);
				$number = $start;
				while(true)
				{
					if( !isset( $exist[ $number ] ) ) break;
					
					$number++;
					echo '+';
					if( $number > $max ) {
						
						$number = $min;
					}
					
					if( $number == $start ) {
						
						unset($number);
						break;
					}
				}
				
				if( isset($number) ) {
				
					$password = random_chars(8);
					
					$db->query('INSERT INTO '.$db->prefix.'accounts (id, password, email, blocked, premdays) VALUES ('.(int)$number.', "'.( $acm_config['use_md5'] ? md5($password) : $password ).'", "'.$email.'", 0, 0)');
					
					$mail = file_get_contents(ACM_ROOT.'lang/'.$acm_config['lang'].'/mail/register.tpl');
					$mail = str_replace('<title>', $acm_config['title'], $mail);
					$mail = str_replace('<base_url>', $acm_config['base_url'], $mail);
					$mail = str_replace('<login_url>', $acm_config['base_url'].'/login.php', $mail);
					
					$mail = str_replace('<account>', $number, $mail);
					$mail = str_replace('<password>', $password, $mail);
					
					sendMail($email, $lang_register['Signup mail subject'].' '.$acm_config['title'], $mail);
					
					$message = $lang_register['Reg e-mail'].' '.'<a href="mailto:'.$acm_config['admin_email'].'" >'.$acm_config['admin_email'].'</a>.';
					$link = 'login.php';
					$link_title =  $lang_common['Login'];
				}
				else {	//	Out of account numbers
					
					$message = $lang_register['Out of numbers'];
				}
			}
			else {	//	E-mail exist id db
				
				$message = $lang_register['E-mail exist'];
			}
		}
		else {	//	E-mail not match
				
			$message = $lang_register['E-mail not match'];
		}
	}
	else {	//	E-mail invalid
		
		$message = $lang_register['E-mail invalid'];
	}
		
	?>
	<div class="message">
		<p><?php echo $message; ?></p>
		<p>
			<br />
			<a href="<?php echo $link; ?>" ><?php echo $link_title; ?></a>
		</p>
	</div>
<?php
}
else {
?>
	<form method="post" action="register.php">
	<table class="tableform">
	<tr>
		<th><?php echo $lang_register['E-mail']; ?></th>
		<td><input type="text" name="email" value="" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_register['Confirm e-mail']; ?></th>
		<td><input type="text" name="cemail" value="" /></td>
	</tr>
	<tr>
    	<th></th>
    	<td>
    	<input type="hidden" name="foo" value="bar" />
		<input type="submit" value="<?php echo $lang_register['register']; ?>" />
		<input type="reset" value="<?php echo $lang_register['reset']; ?>" />
		</td>
	</tr>
	</table>
	</form>
<?php
}
?>
	</div>
</div>
</div>
<?php

$page_style = 'register';
require ACM_ROOT.'kernel/finalize.php';

?>