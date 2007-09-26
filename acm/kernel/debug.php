<?php

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