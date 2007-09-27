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
 * @subpackage 	kernel
 * @version 		3
 */

if(!defined('ACM_ROOT')) exit();

if( !defined('ACM_ADMIN') ) {
	
	require ACM_ROOT.'kernel/header.php';
	require ACM_ROOT.'kernel/footer.php';
}
else {

	require ACM_ROOT.'kernel/header_admin.php';
	require ACM_ROOT.'kernel/footer_admin.php';
}

exit();

?>