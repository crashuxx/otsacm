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

if( !defined('ACM_ROOT') ) define('ACM_ROOT', './');

$dir = opendir(ACM_ROOT.'cache/');
if( $dir ) {

	while( ($file = readdir($dir)) )
	{
		if( is_file(ACM_ROOT.'cache/'.$file) && !preg_match("/^\.htaccess|.*\.dump|index\.html/i", $file)) {
			
			$fmtime = @filemtime(ACM_ROOT.'cache/'.$file);

			if( ($fmtime+7200) != time() ) {

				unlink(ACM_ROOT.'cache/'.$file);
			}	
		}
	}
	closedir($dir);
}

?>