<?php

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