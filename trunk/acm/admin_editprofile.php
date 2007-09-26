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

define('ACM_ADMIN', true);
define('ACM_ROOT', './');
require ACM_ROOT.'kernel/common.php';

if( $cur_user['is_admin'] != true ) redirect('index.php');

include ACM_ROOT.'kernel/template/colors.php';

$id = (int)$_GET['id'];

$result = $db->query('SELECT * FROM '.$db->prefix.'acm_profiles WHERE id = '.(int)$id.' LIMIT 1 ');

$profile = $db->fetch_assoc($result);

if( $_POST['foo'] == 'bar' ) {
	
	foreach( $profile as $key => $val ) {
		
		if( $key == 'id' ) continue;
		
		$update[] = ' '.$key.' = '.(int)$_POST[$key].' ';
	}
	
	$db->query('UPDATE '.$db->prefix.'acm_profiles SET '.implode(',', $update).' WHERE id = '.(int)$id.' LIMIT 1 ');
	
	message('', 'admin_editprofile.php?id='.$id);
}

function vocation($id) {
	
	global $lang_common;
	
	switch( $id ) {
					
			default:
			case 1:
				return $lang_common['voc_none'];
						
			case 2:
				return $lang_common['voc_sorc'];
						
			case 3:
				return $lang_common['voc_druid'];
						
			case 4:
				return $lang_common['voc_paladin'];
						
			case 5:
				return $lang_common['voc_knight'];				
		}
}

if( $id <= 5 ) {

	$profile_name = $lang_common['female'].' - '.vocation($id);
}
else {

	$profile_name = $lang_common['male'].' - '.vocation($id - 10);
}

?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	
	<form method="post" action="admin_editprofile.php?id=<?php echo $id; ?>">
	<table class="formTable">
	<tr>
		<th>Profile</th>
		<td><?php echo $profile_name; ?></td>
	</tr>
	<tr>
		<th>Fist fighting</th>
		<td><input type="text" name="skill0" value="<?php echo $profile['skill0']; ?>"/></td>
	</tr>
	<tr>
		<th>Club fighting</th>
		<td><input type="text" name="skill1" value="<?php echo $profile['skill1']; ?>"/></td>
	</tr>
	<tr>
		<th>Sword fighting</th>
		<td><input type="text" name="skill2" value="<?php echo $profile['skill2']; ?>"/></td>
	</tr>
	<tr>
		<th>Axe fighting</th>
		<td><input type="text" name="skill3" value="<?php echo $profile['skill3']; ?>"/></td>
	</tr>
	<tr>
		<th>Distance fighting</th>
		<td><input type="text" name=skill4" value="<?php echo $profile['skill4']; ?>"/></td>
	</tr>
	<tr>
		<th>Shielding skill</th>
        <td><input type="text" name="skill5" value="<?php echo $profile['skill5']; ?>"/></td>
	</tr>
	<tr>
		<th>Fishing</th>
        <td><input type="text" name="skill6" value="<?php echo $profile['skill6']; ?>"/></td>
	</tr>
	<tr>
		<th>Experience points</th>
		<td><input type="text" name="experience" value="<?php echo $profile['experience']; ?>"/></td>
	</tr>
	<tr>
		<th>Magic level</th>
		<td><input type="text" name="maglevel" value="<?php echo $profile['maglevel']; ?>"/></td>
	</tr>
	<tr>
		<th>Mana</th>
		<td><input type="text" name="mana" value="<?php echo $profile['mana']; ?>"/></td>
	</tr>
	<tr>
		<th>Maximum mana</th>
		<td><input type="text" name="manamax" value="<?php echo $profile['manamax']; ?>"/></td>
	</tr>
	<tr>
		<th>Spent mana</th>
		<td><input type="text" name="manaspent" value="<?php echo $profile['manaspent']; ?>"/></td>
	</tr>
	<tr>
		<th>Soul points</th>
		<td><input type="text" name="soul" value="<?php echo $profile['soul']; ?>"/></td>
	</tr>
	<tr>
		<th>Hit points</hd>
		<td><input type="text" name="health" value="<?php echo $profile['health']; ?>"/></td>
	</tr>
	<tr>
		<th>Maximum hit points</th>
		<td><input type="text" name="healthmax" value="<?php echo $profile['healthmax']; ?>"/></td>
	</tr>
	<tr>
		<th>Direction</th>
		<td>
			<select name="direction">
<?php
$direction = array($lang_common['north'], $lang_common['east'], $lang_common['south'], $lang_common['west']);

foreach( $direction as $key => $val ) {
	
	$key++;
	if( $key == $profile['direction'] )
		echo '<option value="'.$key.'" selected="selected" >'.$val.'</option>';
	else echo '<option value="'.$key.'">'.$val.'</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Outfit type</th>
		<td><input type="text" name="looktype" value="<?php echo $profile['looktype']; ?>"/></td>
	</tr>
	<tr>
		<th>Hairs color</th>
		<td>
			<select name="lookhead">
<?php
foreach( $colors as $key => $val ) {
	
	if( $key == $profile['lookhead'] )
		echo '<option value="'.$key.'" selected="selected" style="background-color: '.$val.';">'.$key.'</option>';
	else echo '<option value="'.$key.'" style="background-color: '.$val.';">'.$key.'</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Body color</th>
		<td>
			<select name="lookbody">
<?php
foreach( $colors as $key => $val ) {
	
	if( $key == $profile['lookbody'] )
		echo '<option value="'.$key.'" selected="selected" style="background-color: '.$val.';">'.$key.'</option>';
	else echo '<option value="'.$key.'" style="background-color: '.$val.';">'.$key.'</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Legs color</th>
		<td>
			<select name="looklegs">
<?php
foreach( $colors as $key => $val ) {
	
	if( $key == $profile['looklegs'] )
		echo '<option value="'.$key.'" selected="selected" style="background-color: '.$val.';">'.$key.'</option>';
	else echo '<option value="'.$key.'" style="background-color: '.$val.';">'.$key.'</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Boots color</th>
		<td>
			<select name="lookfeet">
<?php
foreach( $colors as $key => $val ) {
	
	if( $key == $profile['lookfeet'] )
		echo '<option value="'.$key.'" selected="selected" style="background-color: '.$val.';">'.$key.'</option>';
	else echo '<option value="'.$key.'" style="background-color: '.$val.';">'.$key.'</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Capacity</th>
		<td><input type="text" name="cap" value="<?php echo $profile['cap']; ?>"/></td>
	</tr>
	<tr>
		<th>Food</th>
		<td><input type="text" name="food" value="<?php echo $profile['food']; ?>"/></td>
	</tr>
	<tr>
		<th>Percents of experience to lose</th>
		<td><input type="text" name="loss_experience" value="<?php echo $profile['loss_experience']; ?>"/></td>
	</tr>
	<tr>
		<th>Percents of spent mana to lose</th>
		<td><input type="text" name="loss_mana" value="<?php echo $profile['loss_mana']; ?>"/></td>
	</tr>
	<tr>
		<th>Percents of skills to lose </th>
		<td><input type="text" name="loss_skills" value="<?php echo $profile['loss_skills']; ?>"/></td>
	</tr>
	<tr>
		<th></th>
		<td>
			<input type="hidden" name="foo" value="bar" />
			<input type="submit" value="<?php echo $lang_common['save']; ?>"/>
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