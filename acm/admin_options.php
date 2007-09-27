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

if( $cur_user['is_admin'] != true ) redirect('index.php');

require ACM_ROOT.'lang/'.$acm_config['lang'].'/options.php';

$page_title = $lang_common['Options'];

foreach( $_POST as $key => $val ) {

	if( isset( $acm_config[$key] ) ) {
		
		if( strcmp($acm_config[$key], $val) ) {
			
				if( $key == 'base_url' ) {

					if (substr($val, -1) == '/')
						$val = substr($val, 0, -1);
				}
				elseif( $key == 'ots_dir' ) {
				
				$val = str_replace('\\', '/', $val);
				// Make sure base_url doesn't end with a slash
				if (substr($val, -1) == '/')
					$val = substr($val, 0, -1);
			}
			else if( $key == 'map_name' ) del_cache( 'spawns.dump' );
			
			$db->query('UPDATE '.$db->prefix.'acm_config SET value = "'.$db->escape($val).'" WHERE name = "'.$db->escape($key).'" LIMIT 1');
		}
	}
}

$acm_config = load_config(false);

?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	
	<form method="post" action="admin_options.php">
	<table>
	
	<tr>
		<th colspan="2"><h1><?php echo $lang_options['board']; ?></h1></th>
	</tr>
	<tr>
		<th><?php echo $lang_options['title']; ?></th>
		<td><input type="text" name="title" value="<?php echo $acm_config['title']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['base_url']; ?></th>
		<td><input type="text" name="base_url" value="<?php echo $acm_config['base_url']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['admin_email']; ?></th>
		<td><input type="text" name="admin_email" value="<?php echo $acm_config['admin_email']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['use_gz']; ?></th>
		<td>
			<label><input class="radio" name="use_gz" type="radio" value="1" <?php if( $acm_config['use_gz'] == 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['yes']; ?></label>
			<label><input class="radio" name="use_gz" type="radio" value="0" <?php if( $acm_config['use_gz'] != 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['no']; ?></label><br/>
			<span><?php echo $lang_options['gz info']; ?></span>
		</td>
	</tr>
	<tr>
		<th><?php echo $lang_options['lang']; ?></th>
		<td><input type="text" name="lang" value="<?php echo $acm_config['lang']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['style']; ?></th>
		<td><input type="text" name="style" value="<?php echo $acm_config['style']; ?>" /></td>
	</tr>
<!-- <tr>
		<th><?php echo $lang_options['timeout_online']; ?></th>
		<td><input type="text" name="timeout_online" value="<?php echo $acm_config['timeout_online']; ?>" /></td>
	</tr>  -->

	<tr>
		<th colspan="2" style="text-align: center;"><h1><?php echo $lang_options['E-mail']; ?></h1></th>
	</tr>
	<tr>
		<th><?php echo $lang_options['mail_via_smtp']; ?></th>
		<td>
			<label><input class="radio" name="mail_via_smtp" type="radio" value="1" <?php if( $acm_config['mail_via_smtp'] == 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['yes']; ?></label>
			<label><input class="radio" name="mail_via_smtp" type="radio" value="0" <?php if( $acm_config['mail_via_smtp'] != 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['no']; ?></label>
		</td>
	</tr>
	<tr>
		<th><?php echo $lang_options['smtp_host']; ?></th>
		<td><input type="text" name="smtp_host" value="<?php echo $acm_config['smtp_host']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['smtp_user']; ?></th>
		<td><input type="text" name="smtp_user" value="<?php echo $acm_config['smtp_user']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['smtp_pass']; ?></th>
		<td><input type="text" name="smtp_pass" value="<?php echo $acm_config['smtp_pass']; ?>" /></td>
	</tr>
	
	
	<tr>
		<th colspan="2" style="text-align: center;"><h1><?php echo $lang_options['ots']; ?></h1></th>
	</tr>
	<tr>
		<th><?php echo $lang_options['ots_dir']; ?></th>
		<td><input type="text" name="ots_dir" value="<?php echo $acm_config['ots_dir']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['map_name']; ?></th>
		<td><input type="text" name="map_name" value="<?php echo $acm_config['map_name']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['ots_depots']; ?></th>
		<td><input type="text" name="ots_depots" value="<?php echo $acm_config['ots_depots']; ?>" /></td>
	</tr>
		<tr>
		<th><?php echo $lang_options['depots_item']; ?></th>
		<td><input type="text" name="depots_item" value="<?php echo $acm_config['depots_item']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['depots_chest']; ?></th>
		<td><input type="text" name="depots_chest" value="<?php echo $acm_config['depots_chest']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['pass_min_length']; ?></th>
		<td><input type="text" name="pass_min_length" value="<?php echo $acm_config['pass_min_length']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['acc_max_number']; ?></th>
		<td><input type="text" name="acc_max_number" value="<?php echo $acm_config['acc_max_number']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['acc_min_number']; ?></th>
		<td><input type="text" name="acc_min_number" value="<?php echo $acm_config['acc_min_number']; ?>" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_options['use_md5']; ?></th>
		<td>
			<label><input class="radio" name="use_md5" type="radio" value="1" <?php if( $acm_config['use_md5'] == 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['yes']; ?></label>
			<label><input class="radio" name="use_md5" type="radio" value="0" <?php if( $acm_config['use_md5'] != 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['no']; ?></label>
		</td>
	</tr>
	<tr>
		<th><?php echo $lang_options['rook']; ?></th>
		<td>
			<label><input class="radio" name="rook" type="radio" value="1" <?php if( $acm_config['rook'] == 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['enable']; ?></label>
			<label><input class="radio" name="rook" type="radio" value="0" <?php if( $acm_config['rook'] != 1 ) echo 'checked="checked"'; ?> />&nbsp;<?php echo $lang_common['disable']; ?></label>
		</td>
	</tr>
	<tr>
		<th><?php echo $lang_options['rook_town']; ?></th>
		<td>
		<select name="rook_town" size="1">
<?php
// read spawns form map
$spawns = SpawnsReader();

if( $spawns ) {
	
	foreach( $spawns as $key => $val) {
			
		echo '<option value="'.$key.'">'.htmlspecialchars($val);
	}
}
?>
  		</select>
		</td>
	</tr>
	
	<tr>
		<th></th>
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

$page_style = 'admin_options';
require ACM_ROOT.'kernel/finalize.php';

?>