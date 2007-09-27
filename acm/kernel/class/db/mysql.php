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
 * @subpackage 	DBLayer
 * @version 		3
 */

/**
 * MySQL Layer
 *
 */
class MySQLLayer {

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
	 * DB name
	 *
	 * @var string
	 */
	protected $sDBName = null;
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
	 * @param string	$host		example: host:port
	 * @param string	$username
	 * @param string	$password
	 * @param string	$name		db name
	 * @param string	$prefix		table prefix
	 */
	public function __construct($host, $username, $password, $name, $prefix = '')
	{
		$this->hDB = @mysql_connect($host, $username, $password);
			
		if ( !$this->hDB ) error('Unable to connect to MySQL. MySQL reported: '.mysql_error(), __FILE__, __LINE__);
			
		if( !mysql_select_db($name, $this->hDB) ) error('Unable to select database. MySQL reported: '.mysql_error(), __FILE__, __LINE__);
		
		$this->sDBName = $name;
		
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
			if ($this->hResult)
				@mysql_free_result($this->hResult);

			@mysql_close($this->hDB);
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

		$this->hResult = mysql_query($sQuery, $this->hDB);
		
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
		return ($result) ? @mysql_fetch_assoc($result) : false;
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
		return ($result) ? @mysql_fetch_row($result) : false;
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
		return ($result) ? @mysql_num_rows($result) : false;
	}
	
	/**
	 * Last insert id
	 *
	 * @return int
	 */
	public function insert_id()
	{
		return @mysql_insert_id($this->hDB);
	}
	
	/**
	 * Free Result
	 * 
	 * @param resource	$hResult
	 * @return void
	 */
	public function free($hResult = null)
	{
		$result = ($hResult) ? $hResult : $this->hResult;
		return ($result) ? @mysql_free_result($result) : false;
	}
	
	/**
	 * Escape string
	 * 
	 * @param string	$string
	 * @return string
	 */
	public function escape($string)
	{
		return is_string($string) ? mysql_real_escape_string($string, $this->hDB) : '';
	}

	public function query_dump()
	{
		if( defined('ACM_DEBUG') ) return query_dump($this->queries);
	}
	
	public function error() 
	{
		return end($this->queries).' '.mysql_error($this->hDB);
	}
}

?>