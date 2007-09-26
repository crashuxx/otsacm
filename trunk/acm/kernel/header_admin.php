<?php

if(!defined('ACM_ROOT')) exit();
if(defined('ACM_HEADER_INCLUDED')) return; else define('ACM_HEADER_INCLUDED', true);

// Template load
$tpl_main = trim(file_get_contents(ACM_ROOT.'kernel/template/main.tpl'));

// <acm_char_encoding>
$tpl_main = str_replace('<acm_char_encoding>', $lang_common['lang_encoding'], $tpl_main);


$head_tpl[] = '<link rel="Stylesheet" type="text/css" href="style/'.$acm_config['style'].'.css" />';
$head_tpl[]	= '<title>'.$page_title.' / '.$lang_common['acm'].'</title>';

if( isset($redirect) && !empty($redirect) )
	$head_tpl[] = '<meta http-equiv="refresh" content="4; url='.$redirect.'" />';
	
$tpl_main = str_replace('<acm_head>', implode("\n", $head_tpl), $tpl_main);

// <acm_page>
if( empty($page_style) || !isset($page_style) ) $page_style = 'index';
$tpl_main = str_replace('<acm_page>', $page_style, $tpl_main);

// <acm_title>
$tpl_main = str_replace('<acm_title>', '<h1>'.$acm_config['title'].'</h1> - '.$lang_common['acm'], $tpl_main);

// <acm_navlinks>
$tpl_main = str_replace('<acm_navlinks>', '<div id="brdmenu" class="inbox">'."\n\t\t\t". generate_navlinks()."\n\t\t".'</div>', $tpl_main);

$tpl_main = str_replace('<acm_status>', '', $tpl_main);

// <acm_announcement>
$tpl_main = str_replace('<acm_announcement>', '', $tpl_main);

?>