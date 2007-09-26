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

/** 
 * Dump query(or other array data) into string
 * used by db Layer
 * 
 * @param array $queries
 * @return string
 */
function query_dump($queries)
{
	$GLOBALS['__query_dump'] = '';
	
	function __callback_query_map($arg)
	{
		if( is_array($arg) ) return array_map('__callback_query_map' ,$arg);
		$GLOBALS['__query_dump'] .= $arg.'<br />';
	}
	
	array_map('__callback_query_map' ,$queries);
	return $GLOBALS['__query_dump'];
}

?>