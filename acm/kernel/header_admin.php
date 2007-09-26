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