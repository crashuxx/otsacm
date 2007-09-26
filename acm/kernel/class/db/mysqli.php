<?php

class MySQLiLayer {

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
		if( strpos($host, ':') !== false)
			list($host, $port) = explode(':', $host);
			
		if ( isset($port) )
			$this->hDB = @mysqli_connect($host, $username, $password, $name, $port);
		else
			$this->hDB = @mysqli_connect($host, $username, $password, $name);
			
		if ( !$this->hDB )
			error('Unable to connect to MySQL and select database. MySQL reported: '.mysqli_connect_error(), __FILE__, __LINE__);
		
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
				@mysqli_free_result($this->hResult);

			@mysqli_close($this->hDB);
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

		$this->hResult = @mysqli_query($this->hDB, $sQuery);
		
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
		return ($result) ? @mysqli_fetch_assoc($result) : false;
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
		return ($result) ? @mysqli_fetch_row($result) : false;
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
		return ($result) ? @mysqli_num_rows($result) : false;
	}
	
	/**
	 * Last insert id
	 *
	 * @return int
	 */
	public function insert_id()
	{
		return @mysqli_insert_id($this->hDB);
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
		return ($result) ? @mysqli_free_result($result) : false;
	}
	
	/**
	 * Escape string
	 * 
	 * @param string	$string
	 * @return string
	 */
	public function escape($string)
	{
		return is_string($string) ? mysqli_real_escape_string($this->hDB, $string) : '';
	}

	public function query_dump()
	{
		if( defined('ACM_DEBUG') ) return query_dump($this->queries);
	}
	
	public function error() 
	{
		return end($this->queries).' '.mysqli_error($this->hDB);
	}
}

?>