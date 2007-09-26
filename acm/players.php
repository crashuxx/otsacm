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

if( $cur_user['is_guest'] == true ) redirect('login.php');

require ACM_ROOT.'lang/'.$acm_config['lang'].'/players.php';

$page_title = $lang_players['Page char list'];

if( !strcmp( strtolower($_GET['action']), 'delete') ) {
	
	if( $_POST['foo'] == 'bar' ) {
	
		if( isset($_POST['confirm']) && ( !isset($_POST['cancel']) || empty($_POST['cancel']) ) ) {
			
			$playerid = (int)$_POST['id'];
			
			if( $playerid > 0) {
			
				$db->query('DELETE FROM '.$db->prefix.'players WHERE id = '.$playerid.' AND account_id = '.$cur_user['id'].' LIMIT 1') or error('Unable to delete from players table', __FILE__, __LINE__, true);
			}
			
			message($lang_players['Char deleted'], 'players.php', $lang_common['Char list']);
		}
		else message($lang_players['No deleted'], 'players.php', $lang_common['Char list']);
	}
	else {
		
		$playerid = (int)$_GET['id'];
		
		if( $playerid > 0) {
		
			$db->query('SELECT name FROM '.$db->prefix.'players WHERE id = '.$playerid.' AND account_id = '.$cur_user['id'].' LIMIT 1') or error('Unable to fetch players table', __FILE__, __LINE__, true);
			
			if( $db->num_rows() == 1) {
			
				$row = $db->fetch_assoc();
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<div class="message">
		<p><?php echo str_replace('<player_name>', $row['name'], $lang_players['Del question']); ?></p>
		<form action="players.php?action=delete" method="post">
		<p>
			<input type="hidden" name="id" value="<?php echo $playerid; ?>" />
			<input type="hidden" name="foo" value="bar" />
			<input type="submit" name="confirm" value="<?php echo $lang_common['yes']; ?>" />&nbsp;
			<input type="submit" name="cancel" value="<?php echo $lang_common['no']; ?>" />
		</p>
		</form>
	</div>
	</div>
</div>
</div>
<?php
			}
		}
	}
}
else {
	
	$db->query('SELECT p.id, p.name, p.vocation, p.level, g.name as guild FROM '.$db->prefix.'players AS p LEFT JOIN ('.$db->prefix.'guild_ranks r, '.$db->prefix.'guilds g) ON (p.rank_id > 0 AND r.id = rank_id AND g.id = r.guild_id) WHERE p.account_id = '.$cur_user['id'].' ORDER BY p.name ') or error('Unable to fetch players table', __FILE__, __LINE__, true);

?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	
	<table>
  	<tr>
		<th class="name"><?php echo $lang_players['Name']; ?></th>
		<th class="guild"><?php echo $lang_players['Guild']; ?></th>
		<th class="voc"><?php echo $lang_common['voc']; ?></th>
		<th class="level"><?php echo $lang_players['level']; ?></th>
		<th class="links"><?php echo $lang_players['hm?']; ?></th>
	</tr>
<?php
	while( $row = $db->fetch_assoc() ) {
		
		switch( $row['vocation'] ) {
					
			default:
			case 0:
				$voc = $lang_common['voc_none'];
				break;
						
			case 1:
				$voc = $lang_common['voc_sorc'];
				break;
						
			case 2:
				$voc = $lang_common['voc_druid'];
				break;
						
			case 3:
				$voc = $lang_common['voc_paladin'];
				break;
						
			case 4:
				$voc = $lang_common['voc_knight'];
				break;				
		}
		
		$links = '<a href="players.php?action=delete&id='.$row['id'].'" ><img src="img/delete.png" /></a>';
?>
	<tr>
		<td class="name"><?php echo $row['name']; ?></td>
		<td class="guild"><?php echo $row['guild']; ?></td>
		<td class="voc"><?php echo $voc; ?></td>
		<td class="level"><?php echo $row['level']; ?></td>
		<td class="links"><?php echo $links; ?></td>
	</tr>
<?php
	}
?>
	</table>
	</div>
</div>
</div>
<?php
}

$page_style = 'players';
require ACM_ROOT.'kernel/finalize.php';

?>