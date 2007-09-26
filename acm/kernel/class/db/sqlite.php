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

class SQLiteLayer {

	/**
	 * DB handle
	 *
	 * @var resource
	 */
	protected $hDB	= null;
	/**
	 * Last result
	 *
	 * @var resource
	 */
	protected $hResult = null;
	/**
	 * Query Count
	 *
	 * @var int
	 */
	public $iQueryCount = 0;
	protected $queries = array();
	public $prefix;
	
	/**
	 * Connecting to db
	 * 
	 * @param string	$file
	 * @param string	$prefix		table prefix
	 */
	public function __construct($file, $prefix = '')
	{
		$this->hDB = sqlite_open($filename, 0666, $error);
			
		if ( !$this->hDB ) error('Unable to load SQLite file. SQLite reported: '.$error, __FILE__, __LINE__);
			
		$this->prefix = $prefix;
	}
	
	/**
	 * Destructor
	 */
	public function __destructor()
	{
		$this->close();
	}
	
	/**
	 * Closing connection
	 *
	 */
	public function close()
	{
		if ($this->hDB)
		{
			@sqlite_close($this->hDB);
		}
		unset($this->hResult);
		unset($this->hDB);
	}
	
	/**
	 * Query
	 * 
	 * @param string $query
	 * @return resource
	 */
	public function query($sQuery)
	{
		if( empty($sQuery) ) return null;
		
		if( defined('ACM_DEBUG') ) $this->queries[] = $sQuery;
		
		$this->iQueryCount++;

		$this->hResult = sqlite_query($sQuery, $this->hDB);
		
		return $this->hResult;
	}
	
	/**
	 * Fetch assoc
	 * 
	 * @param resource	$hResult
	 * @return array
	 */
	public function fetch_assoc($hResult = null)
	{
		$result = ($hResult) ? $hResult : $this->hResult;
		return ($result) ? @sqlite_fetch_array($result, SQLITE_ASSOC) : false;
	}

	/**
	 * Fetch row
	 * 
	 * @param resource	$hResult
	 * @return array
	 */
	public function fetch_row($hResult = null)
	{
		$result = ($hResult) ? $hResult : $this->hResult;
		return ($result) ? @sqlite_fetch_array($result, SQLITE_NUM) : false;
	}

	/**
	 * Number rows
	 * 
	 * @param resource	$hResult
	 * @return int
	 */
	public function num_rows($hResult = null)
	{
		$result = ($hResult) ? $hResult : $this->hResult;
		return ($result) ? @sqlite_num_rows($result) : false;
	}
	
	/**
	 * Last insert id
	 *
	 * @return int
	 */
	public function insert_id()
	{
		return @sqlite_last_insert_rowid($this->hDB);
	}
	
	/**
	 * Free Result
	 * 
	 * @param resource	$hResult
	 * @return void
	 */
	public function free($hResult = null)
	{
//		$result = ($hResult) ? $hResult : $this->hResult;
	}
	
	/**
	 * Escape string
	 * 
	 * @param string	$string
	 * @return string
	 */
	public function escape($string)
	{
		return is_string($string) ? sqlite_escape_string($string) : '';
	}

	public function query_dump()
	{
		if( defined('ACM_DEBUG') ) return query_dump($this->queries);
	}
	
	public function error() 
	{
		return end($this->queries).' '.sqlite_error_string($this->hDB);
	}
}

?>