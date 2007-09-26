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

require ACM_ROOT.'lang/'.$acm_config['lang'].'/create.php';

$page_title = $lang_common['Char create'];

$result = $db->query('SELECT id FROM '.$db->prefix.'players WHERE account_id = '.$cur_user['id'].' ');
if( $db->num_rows($result) >= $acm_config['max_acc_chars'] ) message($lang_create['max chars reach']);

// read spawns form map
$spawns = SpawnsReader();

if( $_POST['foo'] == 'bar' ) {
	
	$name = trim($_POST['nick']);
	$sex = ( $_POST['sex'] == 1 ) ? 1 : 0;
	
	if( !preg_match("/^[a-zA-Z0-9\ ]+$/", $name) ) message($lang_create['name uncorrect'], 'javascript:history.go(-1)');
	
	if( !$acm_config['rook'] ) { 
	 	
		$voc = (int)$_POST['vocation'];
		if( $voc > 4 ) $voc = 4;
		if( $voc < 0 ) $voc = 0;
		
		$town = isset( $spawns[(int)$_POST['town']] ) ? (int)$_POST['town'] : 1;
	}
	else {
		
		$voc = 0;
		$town = $acm_config['rook_town'];
	}
	
	$profile = $voc + 1 + 10 * $sex;
	
	
	$result = $db->query('SELECT id FROM '.$db->prefix.'players WHERE name = "'.$db->escape($name).'" LIMIT 1');
	
	if( $db->num_rows($result) == 1 ) message($lang_create['exists'], 'javascript:history.go(-1)');
	
	
	$rProfile = $db->query('SELECT * FROM '.$db->prefix.'acm_profiles WHERE id = '.$profile.' LIMIT 1') or error('Unable to fetch acm_profiles table.',__FILE__, __LINE__, true);
	$rContainer = $db->query('SELECT * FROM '.$db->prefix.'acm_containers WHERE id = '.$voc.' ORDER BY slot ASC') or error('Unable to fetch acm_containers table.',__FILE__, __LINE__, true);
	
	if( $db->num_rows($rProfile) != 1 ) message($lang_common['unknown error'],'create.php');
	
	$pt = $db->fetch_assoc($rProfile);
	
	$level = 1;
	while(50 / 3 * pow($level + 1, 3) - 100 * pow($level + 1, 2) + (850 / 3) * ($level + 1) - 200 <= (int) $pt['experience']) $level++;
	
	$db->query('START TRANSACTION');
	
	$db->query( 'INSERT INTO '.$db->prefix.'players (name ,account_id ,group_id ,sex ,vocation ,experience ,level ,maglevel ,health ,healthmax ,mana ,manamax ,manaspent ,soul ,direction ,lookbody ,lookfeet ,lookhead ,looklegs ,looktype ,lookaddons ,posx ,posy ,posz ,cap ,lastlogin ,lastip ,save ,redskulltime ,redskull ,guildnick ,rank_id ,town_id ,loss_experience ,loss_mana ,loss_skills) '.
				'VALUES ("'.$db->escape($name).'", '.$cur_user['id'].', '.$acm_config['default_group'].', '.$sex.', '.$voc.', '.$pt['experience'].', '.$level.', '.$pt['maglevel'].', '.$pt['health'].', '.$pt['healthmax'].', '.$pt['mana'].', '.$pt['manamax'].', '.$pt['manaspent'].', '.$pt['soul'].', '.$pt['direction'].', '.$pt['lookbody'].', '.$pt['lookfeet'].', '.$pt['lookhead'].', '.$pt['looklegs'].', '.$pt['looktype'].', 0, 0, 0, 0, '.$pt['cap'].', 0, 0, 0, 0, 0, "", 0, '.$town.', '.$pt['loss_experience'].', '.$pt['loss_mana'].', '.$pt['loss_skills'].')') or error('Cannot insert into players table.',__FILE__ , __LINE__, true);
	
	$playerID = $db->insert_id();
	
	for( $i = 0; $i < 7; $i++ ) {
		
		$db->query('UPDATE '.$db->prefix.'player_skills SET value = '.$pt['skill'.(int)$i].' WHERE player_id = ' . $playerID . ' AND skillid = '.$i.' LIMIT 1 ');
	}
	
	$rContainers = $db->query('SELECT * FROM '.$db->prefix.'acm_containers WHERE profile = '.(int)$voc.' ORDER BY id ASC');

	$pids = array(0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10);
	$sid = 10;
	
	$dsid = 100;
	$dpids = array();
	
	for($i = 1; $i <= $acm_config['ots_depots']; $i++) {
		
		$db->query('INSERT INTO '.$db->prefix.'player_depotitems (player_id, depotid, sid, pid, itemtype, count) VALUES (' . $playerID . ', '.$i.', '.( 100 + $i ).', 0, '.(int)$acm_config['depots_item'].', 0)');
		$db->query('INSERT INTO '.$db->prefix.'player_depotitems (player_id, depotid, sid, pid, itemtype, count) VALUES (' . $playerID . ', '.$i.', '.( 200 + $i ).', '.( 100 + $i ).', '.(int)$acm_config['depots_item'].', 0)');

	    $dpids[ 99 + $i ] = array('pid' => 200 + $i, 'depot' => $i);
	}
	
	$dsid = 200 +  $acm_config['ots_depots'];
		
	while( $row = $db->fetch_assoc($rContainers) ) {
		
		if( $row['slot'] <= 10 ) {

			$sid++;
			$db->query('INSERT INTO '.$db->prefix.'player_items (player_id, sid, pid, itemtype, count) VALUES('.$playerID.', '.$sid.', '.$pids[ (int)$row['slot']].', '.$row['content'].', '.(int)$row['count'].')');
			$pids[ $row['id'] ] = $sid;
		}
		else {
	
			if( isset( $pids[ $row['slot'] ] ) ) {
				
				$sid++;
				$db->query('INSERT INTO '.$db->prefix.'player_items (player_id, sid, pid, itemtype, count) VALUES('.$playerID.', '.$sid.', '.$pids[ (int)$row['slot'] ].', '.$row['content'].', '.(int)$row['count'].')');
				$pids[ $row['id'] ] = $sid;
			}
			else {
				
				$dsid++;
				
				$db->query('INSERT INTO '.$db->prefix.'player_depotitems (player_id, depotid, sid, pid, itemtype, count) VALUES (' . $playerID . ', '.$dpids[ $row['slot'] ]['depot'].', '.(int)$dsid.', '. $dpids[ $row['slot'] ]['pid'].', '.$row['content'].', '.$row['count'].')');
				
				$dpids[ $row['id'] ] = array('pid' => $dsid, 'depot' => $dpids[ $row['slot'] ]['depot']);
			}
		}
	}
	
	$db->query('COMMIT');
//	$db->query('ROLLBACK');
	message($lang_create['created'], 'players.php', $lang_common['Char list']);
}

?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<form method="post" action="create.php">
	<table class="tablecreate">
	<tr>
		<th><?php echo $lang_common['name']; ?></th>
		<td><input type="text" name="nick" value="" /></td>
	</tr>
	<tr>
		<th><?php echo $lang_common['sex']; ?></th>
		<td>
			<label><input class="radio" name="sex" type="radio" value="0"/>&nbsp;<?php echo $lang_common['female']; ?></label>
			<label><input class="radio" name="sex" type="radio" value="1"/>&nbsp;<?php echo $lang_common['male']; ?></label>
         </td>
	</tr>
<?php if( !$acm_config['rook'] ) { ?>
	<tr>
		<th><?php echo $lang_common['voc']; ?></th>
		<td>
			<label><input class="radio" name="vocation" type="radio" value="0"/>&nbsp;<?php echo $lang_common['voc_none']; ?></label>
			<label><input class="radio" name="vocation" type="radio" value="1"/>&nbsp;<?php echo $lang_common['voc_sorc']; ?></label>
			<label><input class="radio" name="vocation" type="radio" value="2"/>&nbsp;<?php echo $lang_common['voc_druid']; ?></label>
			<label><input class="radio" name="vocation" type="radio" value="3"/>&nbsp;<?php echo $lang_common['voc_paladin']; ?></label>
			<label><input class="radio" name="vocation" type="radio" value="4"/>&nbsp;<?php echo $lang_common['voc_knight']; ?></label>
         </td>
	</tr>
	<tr>
		<th><?php echo $lang_common['city']; ?></th>
		<td>
		<select name="town" size="1">
<?php
	foreach( $spawns as $key => $val) {
		
		echo '<option value="'.$key.'">'.htmlspecialchars($val);
	}
?>
  		</select>
		</td>
	</tr>
<?php } ?>
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

require ACM_ROOT.'kernel/finalize.php';

?>