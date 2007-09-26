<?php

define('ACM_VERSION', '3.0 Alpha');
define('ACM_DEBUG', true);

define('ACM_ROOT', './');
if( file_exists(ACM_ROOT.'config.php') ) die('ACM is already installed.');

$tpl_main = file_get_contents(ACM_ROOT.'kernel/template/main.tpl');

// <acm_char_encoding>
$tpl_main = str_replace('<acm_char_encoding>', 'utf-8', $tpl_main);


$head_tpl[] = '<link rel="Stylesheet" type="text/css" href="install/style.css" />';
$head_tpl[]	= '<title>ACM '.ACM_VERSION.' Installation</title>';

if( $redirect )
	$head_tpl[] = '<meta http-equiv="refresh" content="4; url='.$redirect.'" />';

	$tpl_main = str_replace('<acm_head>', implode("\n", $head_tpl), $tpl_main);

// <acm_page>
$tpl_main = str_replace('<acm_page>', 'install', $tpl_main);

// <acm_title>
$tpl_main = str_replace('<acm_title>', '<h1>ACM 3.0 Alpha</h1> - Installation', $tpl_main);

// <acm_navlinks>
$tpl_main = str_replace('<acm_navlinks>', '', $tpl_main);

// <acm_announcement>
$tpl_main = str_replace('<acm_announcement>', '', $tpl_main);

ob_start();
ob_implicit_flush(0);
?>
<div id="brdmain" > 
<div class="box" >
	<div class="inbox" >
<?php
if( $_POST['install'] == 1 ) {
	
	ob_end_clean();
	
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
	
	require_once ACM_ROOT.'kernel/functions.php';
	
	$form = $_POST;
	
	switch($form['dbdriver']) {
	
	case 'mysql':
		require_once ACM_ROOT.'kernel/class/db/mysql.php';
		$db = new MySQLLayer($form['dbhost'], $form['dblogin'], $form['dbpass'], $form['dbname'], $form['tableprefix']);
		break;
	
	case 'mysqli':
		require_once ACM_ROOT.'kernel/class/db/mysqli.php';
		$db = new MySQLiLayer($form['dbhost'], $form['dblogin'], $form['dbpass'], $form['dbname'], $form['tableprefix']);
		break;
		
	default:
		error('Unknown database driver. DB type: '.$form['dbdriver'], __FILE__, __LINE__);
	}
	
	$queries = explode(';', str_replace('$__', $form['tableprefix'], trim(file_get_contents(ACM_ROOT.'install/mysql.sql'))));
	
	foreach($queries as $query) {

		if( strlen($query) < 2 ) continue;
		$db->query($query) or error('error ', __FILE__, __LINE__, true);
	}
	
	
	$db->query('SELECT * FROM '.$db->prefix.'acm_config') or error('Unable to fetch acm config', __FILE__, __LINE__, true);
		
	while( $row = $db->fetch_assoc() ) $acm_config[ $row['name'] ] = $row['value'];
	
	foreach( $form as $key => $val ) {
	
		if( isset( $acm_config[$key] ) ) {
			
			if( strcmp($acm_config[$key], $val) ) {
				
				if( $key == 'base_url' ) {

					if (substr($val, -1) == '/')
						$val = substr($val, 0, -1);
				}
				else if( $key == 'ots_dir' ) {
					
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
	
	$config[] = '<?php ';
	$config[] = '';
	$config[] = '$db_type = \''.$form['dbdriver'].'\';';
	$config[] = '$db_host = \''.$form['dbhost'].'\';';
	$config[] = '$db_name = \''.$form['dbname'].'\';';
	$config[] = '$db_username = \''.$form['dblogin'].'\';';
	$config[] = '$db_password = \''.$form['dbpass'].'\';';
	$config[] = '$db_prefix = \''.$form['tableprefix'].'\';';
	$config[] = '';
	$config[] = '$session_prefix = \'acm_\';';
	$config[] = '';
	$config[] = 'define(\'ACM_DEBUG\', true);';
	$config[] = 'define(\'CACHE_CLEAN\', true);';
	$config[] = '';
	$config[] = '?>';
	
	file_put_contents(ACM_ROOT.'config.php', implode("\n", $config) );
	
	die('ok');
}
?>
	<form method="post" action="install.php">
	<table>
	<tr>
		<th>Data base driver</td>
		<td>
			<select name="dbdriver" size="1" style="width: 140px">
			<option value="sqlite" disabled="disabled" />SQLite
			<option value="mysql" selected="selected" />MySQL
			<option value="mysqli" />MySQLi
			</select>
		</td>
	</tr>

	<tr>
		<th>Table prefix</th>
		<td>
			<input name="tableprefix" value="" type="text" />
		</td>
	</tr>
 
 
 	<tr>
 		<th colspan="2" class="head"><h1>MySQL config</h1></th>
 	</tr>
	<tr>
		<th>Host</th>
		<td>
			<input name="dbhost" value="localhost" type="text" />
		</td>
	</tr>
	<tr>
		<th>Login</th>
		<td>
		<input name="dblogin" value="root" type="text" />
		</td>
	</tr>
	<tr>
		<th>Password</th>
		<td>
			<input name="dbpass" value="" type="text">
		</td>
	</tr>
	<tr>
		<th>DB name</th>
		<td>
			<input name="dbname" value="otserv" type="text">
		</td>
	</tr>
		
	<tr>
 		<th colspan="2" class="head"><h1>OTS</h1></th>
 	</tr>
	
	<tr>
		<th>OTServ data/ directory</th>
		<td><input type="text" name="ots_dir" value="" /></td>
	</tr>
	<tr>
		<th>Map filename</th>
		<td><input type="text" name="map_name" value="map.otbm" /></td>
	</tr>
	<tr>
		<th>Amount of depots on map</th>
		<td><input type="text" name="ots_depots" value="2" /></td>
	</tr>
	<tr>
		<th>The highest allowed user account number</th>
		<td><input type="text" name="acc_max_number" value="999999" /></td>
	</tr>
	<tr>
		<th>The lowest allowed user account number</th>
		<td><input type="text" name="acc_min_number" value="1000" /></td>
	</tr>
	<tr>
		<th>Use MD5 for passwords</th>
		<td>
			<label><input class="radio" name="use_md5" type="radio" value="1" checked="checked" />&nbsp;Yes</label>
			<label><input class="radio" name="use_md5" type="radio" value="0" />&nbsp;No</label>
		</td>
	</tr>
	
	<tr>
 		<th colspan="2" class="head"><h1>Other</h1></th>
 	</tr>
	<tr>
		<th>Base URL</th>
		<td><input type="text" name="base_url" value="http://localhost" /></td>
	</tr>
	<tr>
		<th>Administrator e-mail</th>
		<td><input type="text" name="admin_email" value="" /></td>
	</tr>

	<tr>
		<th>E-mail via SMTP</th>
		<td>
			<label><input class="radio" name="mail_via_smtp" type="radio" value="1" checked="checked" />&nbsp;Yes</label>
			<label><input class="radio" name="mail_via_smtp" type="radio" value="0" />&nbsp;No</label>
		</td>
	</tr>
	<tr>
		<th>SMTP server address</th>
		<td><input type="text" name="smtp_host" value="" /></td>
	</tr>
	<tr>
		<th>SMTP username</th>
		<td><input type="text" name="smtp_user" value="" /></td>
	</tr>
	<tr>
		<th>SMTP password</th>
		<td><input type="text" name="smtp_pass" value="" /></td>
	</tr>
	
	<tr>
		<th>Admin username</th>
		<td><input type="text" name="admin_login" value="admin" /></td>
	</tr>
	<tr>
		<th>Admin password</th>
		<td><input type="text" name="admin_password" value="pass" /></td>
	</tr>
	
	<tr>
		<th></th>
		<td>
			<input name="install" value="1" type="hidden">
			<input value="Install" type="submit">&nbsp;<input value="Reset" type="reset">
		</td>
	</tr>
		
	</table>
	</form>
	
	</div>
</div>
</div>
<?php

$tpl_main = str_replace('<acm_main>', ob_get_contents(), $tpl_main);
ob_end_clean();

$foot_tpl[] = '<div id="brdfoot" class="inbox">';
$foot_tpl[] = '<p>Powered by <a href="http://code.google.com/p/otsacm">OTSACM</a> © Copyright 2006–2007 Lukas Pajak</p>';

$tpl_main = str_replace('<acm_footer>', implode("\n", $foot_tpl), $tpl_main);

echo $tpl_main;

?>