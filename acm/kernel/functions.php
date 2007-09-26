<?php

if(!defined('ACM_ROOT')) exit();

function get_cache($sName, $iLifeTime = 3600)
{
	if( !file_exists( ACM_ROOT.'cache/'.$sName ) ) return null;

	if( $iLifeTime > 0 ) {

		$fmtime = @filemtime( ACM_ROOT.'cache/'.$sName);
	
		if( ( $fmtime + $iLifeTime ) < time() ) {
			
			@unlink( ACM_ROOT.'cache/'.$sName );
			return null;
		}
	}

	return @file_get_contents( ACM_ROOT.'cache/'.$sName );
}

function set_cache($sName, $content)
{
	return @file_put_contents( 'cache/'.$sName , $content);
}

function del_cache($sName) 
{
	@unlink( 'cache/'.$sName );
}

function generate_navlinks()
{
	global $acm_config, $lang_common, $cur_user;
	
	if( !defined('ACM_ADMIN') ) {
	
		if( $cur_user['is_guest'] )
		{
			$links[] = '<li id="navregister"><a href="register.php">'.$lang_common['Register'].'</a>';
			$links[] = '<li id="navlogin"><a href="login.php">'.$lang_common['Login'].'</a>';
		}
		else {
			
			$links[] = '<li id="navlogout"><a href="login.php?action=logout">'.$lang_common['Logout'].'</a>';
			$links[] = '<li id="navcharlist"><a href="players.php">'.$lang_common['Char list'].'</a>';
			$links[] = '<li id="navcharcreate"><a href="create.php">'.$lang_common['Char create'].'</a>';
			$links[] = '<li id="navchangepass"><a href="chpass.php">'.$lang_common['Change pass'].'</a>';
		}
	}
	else {
		
		if( !$cur_user['is_admin'] )
		{
			$links[] = '<li id="navadminlogin"><a href="admin.php">'.$lang_common['Login'].'</a>';
		}
		else {
			
			$links[] = '<li id="navadminlogout"><a href="admin.php?action=logout">'.$lang_common['Logout'].'</a>';
			$links[] = '<li id="navadminoptions"><a href="admin_options.php">'.$lang_common['Options'].'</a>';
			$links[] = '<li id="navadminprofiles"><a href="admin_profiles.php">'.$lang_common['Chars profiles'].'</a>';
		}
	}
	
	return '<ul>'."\n\t\t\t\t".implode($lang_common['Link separator'].'</li>'."\n\t\t\t\t", $links).'</li>'."\n\t\t\t".'</ul>';
}

function load_config($bAllowCache = true)
{
	global $db;
	
	// load config form cache, cache life time is 12h(43200s)
	if( $bAllowCache == true ) $config_tmp = get_cache('config.dump', 43200);
	
	if( !$config_tmp ) { // if cache is out of date load form db
		
		// if we dont have correct configured cron
		if( defined('CACHE_CLEAN') ) @include(ACM_ROOT.'cron.php');
		
		$db->query('SELECT * FROM '.$db->prefix.'acm_config') or error('Unable to fetch acm config', __FILE__, __LINE__, true);
		
		while( $row = $db->fetch_assoc() ) $acm_config[ $row['name'] ] = $row['value'];
		
		set_cache('config.dump', serialize($acm_config));
	}
	else $acm_config = unserialize( $config_tmp );
	
	return $acm_config;
}

function error($message, $file, $line, $sql = false)
{
	@ob_end_clean();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<p>
<?php
	echo $message; 

	if( defined('ACM_DEBUG') ) {
?>
	<?php echo $file;?> 
	<?php echo $line;?>
	<br />
<?php
	if( $sql == true) echo $GLOBALS['db']->error();
	}
?>
</p>
</body>
</html>
<?php	
	exit();
}

/**
 * Message
 * 
 * @param string $message
 * @param string $link	url
 * @param string $link_title default $lang_common['Go back']
 * @return void
 */
function message($message, $link = null, $link_title = null)
{
	global $lang_common, $db, $acm_config, $acm_start, $cur_user, $redirect, $page_title;

	ob_clean();
	
	if( isset($link) ) {
		
		$links =  '<p><br /><a href="'.$link.'" >'.( isset($link_title) ? $link_title : $lang_common['Go back'] ).'</a></p>';
	}
	else $links = '';
	
	$tpl_msg = file_get_contents(ACM_ROOT.'kernel/template/message.tpl');

	$tpl_msg = str_replace('<acm_message>', $message, $tpl_msg);
	$tpl_msg = str_replace('<acm_links>', $links, $tpl_msg);
	
	echo $tpl_msg;
	
	$page_style = 'message';
	require ACM_ROOT.'kernel/finalize.php';
}

/**
 * Make sure that HTTP_REFERER matches $acm_config['base_url']/$script
 * 
 * @param string 
 */
function confirm_referrer($script = null)
{
	global $acm_config, $lang_common;

	if( ip2long($_SERVER['REMOTE_ADDR']) == 2130706433 ) return;
	
	if( !isset($script) || empty($script) ) $script = basename( $_SERVER['PHP_SELF'] );

	$url = preg_quote( str_replace('www.', '', $acm_config['base_url']).'/'.$script );
	$HTTP_REFERER = str_replace('www.', '', (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''));
	
	if( !preg_match('#^'.$url.'#i', $HTTP_REFERER) ) message($lang_common['Bad referrer'], $acm_config['base_url'], $lang_common['acm']);		
}

function sendMail($to, $subject, $content)
{
	global $acm_config;
	
	require_once ACM_ROOT.'kernel/class/phpmailer.php';

	$mail = new PHPMailer();
	$mail->SMTPDebug = 0;

	$mail->From     = $acm_config['admin_email'];
	
	if( $acm_config['mail_via_smtp'] ) {
		
		require_once ACM_ROOT.'kernel/class/smtp.php';
	
		$mail->Host     = $acm_config['smtp_host'];
		$mail->Username = $acm_config['smtp_user'];
		$mail->Password = $acm_config['smtp_pass'];
		$mail->Mailer   = 'smtp';
		$mail->SMTPAuth = true;
	}
	else $mail->Mailer   = 'mail';
	
	$mail->SetLanguage();
	$mail->Subject = $subject;
	$mail->CharSet = 'utf-8';
	$mail->WordWrap = 75;
	
	$mail->Body = str_replace('<board_mailer>', $acm_config['admin_email'], $content);
	$mail->IsHTML(false);
	
	$mail->AddAddress($to);

	if( !$mail->Send() ) return false;
	return true;	
}

function random_chars($iLength)
{
	$Chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$iFLength = @strlen($Chars) - 1;
	
	if( $iFLength < 1 ) return null;
	$key = '';
	
	for( $i = 0 ; $i < $iLength ; $i++)
		$key .= $Chars{rand(0,$iFLength)};
		
	return $key;
}

function redirect($url)
{
	ob_end_clean();
	header('Location: '.$url);
	exit();
}

/**
 * unregister_globals
 * 
 * Special thanks to PunBB for the unregister_globals function
 * 
 * Unset any variables instantiated as a result of register_globals being enabled
 */
function unregister_globals()
{
	$register_globals = @ini_get('register_globals');
	if ($register_globals === "" || $register_globals === "0" || strtolower($register_globals === "off"))
		return;

	// Prevent script.php?GLOBALS[foo]=bar
	if (isset($_REQUEST['GLOBALS']) || isset($_GET['GLOBALS']) || isset($_FILES['GLOBALS']))
		exit('I\'ll have a steak sandwich and... a steak sandwich.');
	
	// Variables that shouldn't be unset
	$no_unset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

	// Remove elements in $GLOBALS that are present in any of the superglobals
	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ($input as $k => $v)
	{
		if (!in_array($k, $no_unset) && isset($GLOBALS[$k]))
		{
			unset($GLOBALS[$k]);
			unset($GLOBALS[$k]);	// Double unset to circumvent the zend_hash_del_key_or_index hole in PHP <4.4.3 and <5.1.4
		}
	}
}


/**
 * ItemsReader
 * 
 * Special thanks to OTSCMS for the ItemsReader
 * 
 * @param string $file
 * @return array
 */
function ItemsReader($file)
{
	$xml = new DOMDocument();
	
	if( !$xml->load($file) ) return null;
	
	$items = array();
	
	foreach( $xml->getElementsByTagName('item') as $tag)
	{
		$slot = false;
		$container = false;
	
		// searches for slot in which item can be put
		foreach( $tag->getElementsByTagName('attribute') as $attribute)
		{
			if( $attribute->getAttribute('key') == 'slotType')
			{
				$slot = true;
			}
			elseif( $attribute->getAttribute('key') == 'containerSize')
			{
				$container = true;
			}
		}
	
		// not wearable
		if(!$slot)
		{
			continue;
		}
	
		$items[ (int)$tag->getAttribute('id') ] = array('name' => $tag->getAttribute('name'), 'container' => $container);
	}
	
	return $items;
}

/**
 * SpawnsReader
 * 
 * Special thanks to OTSCMS for the SpawnsReader
 * 
 * @param string $file
 * @return array
 */
function SpawnsReader($file)
{
	// opens file for reading
	$file = @fopen($file, 'rb');

	$spawns = array();
	
	// checks if file is opened correctly
	if($file)
	{
		// skips version
		fseek($file, 4);
	
		// reads nodes chain
		while( !feof($file) )
		{
			// reads byte
			switch( ord( fgetc($file) ) )
			{
			// maybe a town node
			case 0xFE:
				// reads node type
				if( ord( fgetc($file) ) == 13)
				{
					$id = unpack('L', fread($file, 4) );
					$length = unpack('S', fread($file, 2) );
		
					// reads town name
					$spawns[ $id[1] ] = fread($file, $length[1]);
				}
		  		break;
	
			// escape next character - it might be NODE_START character which is in fact not
			case 0xFD:
				fgetc($file);
				break;
			}
		}
	}
	else return null;
	
	fclose($file);
	
	return $spawns;
}

?>