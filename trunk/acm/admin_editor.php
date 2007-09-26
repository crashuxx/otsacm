<?php

define('ACM_ADMIN', true);
define('ACM_ROOT', './');
require ACM_ROOT.'kernel/common.php';

if( $cur_user['is_admin'] != true ) redirect('index.php');

require ACM_ROOT.'lang/'.$acm_config['lang'].'/editor.php';

$profile = (int)$_GET['profile'] >= 0 ? (int)$_GET['profile'] : 0;

function loadTemplate()
{
	global $db, $profile;
	
	$container = array();
	$tree = array();
	$rows = array();
	
	$db->query('SELECT * FROM '.$db->prefix.'acm_containers WHERE profile = '.$profile.' ORDER BY slot ASC');
	
	while( $row = $db->fetch_assoc() ) {
		
		array_push($rows, array('id' => $row['id'], 'item' => (int)$row['content'], 'count' => (int)$row['count'], 'slot' => (int)$row['slot']) );
		$id = (int)$row['id'];
		
		$container[$id]['id'] = (int)$id;
		$container[$id]['content'] = (int)$row['content'];
		$container[$id]['count'] = (int)$row['count'];
		$container[$id]['slot'] = (int)$row['slot'];
		
		if( !is_array($container[$id]['subs']) ) $container[$id]['subs'] = array();
					
		if( $row['slot'] < 1000 ) {
				
			array_push($tree, & $container[$id]);
		}
		else {
	
			if( !is_array($container[ $row['slot'] ]) ) $container[ $row['slot'] ] = array();
			if( !is_array($container[ $row['slot'] ]['subs']) ) $container[ $row['slot'] ]['subs'] = array();
			
			array_push($container[ $row['slot'] ]['subs'], & $container[$id]);
		}
	}
	
	return array($container, $tree, $rows);
}

if( $_POST['foo'] == 'bar' ) {

	$content = (int)$_POST['item'];
	$slot = (int)$_POST['slot'];
	$count = (int)$_POST['count'];
	
	$db->query('INSERT INTO '.$db->prefix.'acm_containers ( content , slot , count , profile ) VALUES ('.$content.', '.$slot.', '.$count.', '.$profile.')');
}
else if( !strcmp( strtolower($_GET['action']), 'delete') ) {
	
	$id = (int)$_GET['id'];
	
	list($container, $tree) = loadTemplate();
	
	if( @count($container[$id]['subs']) == 0 ) {
		$db->query('DELETE FROM '.$db->prefix.'acm_containers WHERE id ='.(int)$id.' LIMIT 1');
	}
	else {
		
		function items_tree_delete($ar)
		{
			global $db;
			
			if( is_array($ar) ) {
				
				if( isset($ar['id']) )
					$db->query('DELETE FROM '.$db->prefix.'acm_containers WHERE id ='.(int)$ar['id'].' LIMIT 1');
				
				if( is_array($ar['subs']) ) array_map('items_tree_delete', $ar['subs']);
			}
		}
		
		items_tree_delete($container[$id]);
	}
}

$items = get_cache('items.dump', 0);
if( !$items ) {
		
	$items = ItemsReader($acm_config['ots_dir'].'/items/items.xml');
	if( $items ) set_cache('items.dump', serialize($items));
}
else $items = unserialize($items);

list($container, $tree, $rows) = loadTemplate();

$slots = array($lang_editor['head'], $lang_editor['necklace'], $lang_editor['backpack'], $lang_editor['armor'], $lang_editor['right hand'], $lang_editor['left hand'], $lang_editor['legs'], $lang_editor['feet'], $lang_editor['ring'], $lang_editor['ammo']);

function map_itemstree($ar)
{
	global $items, $slots, $lang_editor, $profile;

	if( is_array($ar) ) {

		if( count($ar) > 0 && is_array($ar['subs']) ) {

			echo '<p>';
			
			if( $ar['slot'] < 1000 ) {

				if( $ar['slot'] <= 10 )
					echo '<label>'.$items[$ar['content']]['name'].'</label> x'.$ar['count'].' '.$slots[ $ar['slot'] ].' (#id:'.$ar['id'].' #item:'.$ar['content'].')';
				else echo '<label>'.$items[$ar['content']]['name'].'</label> x'.$ar['count'].' '.$lang_editor['depot'].(int)($ar['slot']-99).' (#id:'.$ar['id'].' #item:'.$ar['content'].')';
				
			}
			else echo '<label>'.$items[$ar['content']]['name'].'</label> x'.$ar['count'].' (#id:'.$ar['id'].' #item:'.$ar['content'].')';
			
			echo ' <a href="admin_editor.php?profile='.(int)$profile.'&action=delete&id='.(int)$ar['id'].'">Delete</a>';
			echo '</p>';
			
			array_map('map_itemstree', $ar);
		}
		else if( count($ar) > 0 ) {
			
			echo '<div class="itemstree">';
			array_map('map_itemstree', $ar);
			echo '</div>';
		}
		return;
	}
}

?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
	<div id="editor" >
	<p><label>
<?php
switch( $profile ) {
				
	default:
	case 0:
		echo $lang_common['voc_none'];
		break;
				
	case 1:
		echo $lang_common['voc_sorc'];
		break;
					
	case 2:
		echo $lang_common['voc_druid'];
		break;
					
	case 3:
		echo $lang_common['voc_paladin'];
		break;
				
	case 4:
		echo $lang_common['voc_knight'];
		break;				
}
?>
	</label></p>
	<p>
<?php
if( !strcmp( strtolower($_GET['action']), 'export') ) {
	
	$info = array(
		'version'	=>	'1.0',
		'created'	=>	time(),
		'generator' =>	'ACM '.ACM_VERSION );
	
	$tpt = serialize( array('info' => $info, 'item_sheet' => $rows ) );
	
	echo '<textarea cols="75" rows="1" readonly="readonly" wrap="off">'.md5($tpt).$tpt.'</textarea>';
	
	
}
?>
	</p>
	<p>
<?php map_itemstree($tree); ?>
	</p>
	<p>
	<form method="post" action="admin_editor.php?profile=<?php echo $profile; ?>">
	<table>
	<tr>
		<th><?php echo $lang_editor['slot']; ?></th>
		<td>
		<select name="slot" size="1">
<?php
foreach( $slots as $key => $val) {
		
	echo '<option value="'.(int)$key.'">'.$val;
}

for( $i = 0; $i < $acm_config['ots_depots']; $i++ ) {
	
	echo '<option value="'.($i+100).'">'.$lang_editor['depot'].(int)($i+1);
}

foreach( $container as $key => $val) {
		
	if( $items[$val['content']]['container'] == true )
	echo '<option value="'.(int)$key.'">'.$items[$val['content']]['name'].'&nbsp;(#id:'.$key.')';
}

?>
  		</select>
  		</td>
	<tr>
		<th><?php echo $lang_editor['item']; ?></th>
		<td>
		<select name="item" size="1">
<?php
foreach( $items as $key => $val) {
		
	echo '<option value="'.$key.'">'.htmlspecialchars($val['name']).'&nbsp;(#item:'.$key.')';
}
?>
  		</select>
  		</td>
  	</tr>
  	<tr>
  		<th><?php echo $lang_editor['count']; ?></th>
		<td><input type="text" name="count" value="1" /></td>
	</tr>
	<tr>
    	<th><a href="admin_editor.php?profile=<?php echo $profile; ?>&action=export"><?php echo $lang_common['export']; ?></a></th>
    	<td>
    	<input type="hidden" name="foo" value="bar" />
		<input type="submit" value="<?php echo $lang_common['submit']; ?>" />
		<input type="reset" value="<?php echo $lang_common['reset']; ?>" />
		&nbsp;<a href="admin_profiles.php" ><?php echo $lang_common['Go back']; ?></a>
		</td>
	</tr>
	</table>
	</form>
	</p>
	</div>
	</div>
</div>
</div>
<?php

$page_style = 'admin_editor';
require ACM_ROOT.'kernel/finalize.php';

?>