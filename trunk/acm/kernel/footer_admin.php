<?php

if(!defined('ACM_ROOT')) exit();
if(defined('ACM_FOOTER_INCLUDED')) return; else define('ACM_FOOTER_INCLUDED', true);

$acm_stop = explode(' ', microtime());
$acm_stop = round(($acm_stop[0] + $acm_stop[1]), 4);
$time = $acm_stop - $acm_start;
if( $time < 0 ) $time = 0;

$tpl_main = str_replace('<acm_main>', ob_get_contents(), $tpl_main);
ob_end_clean();

$foot_tpl[] = '<div id="brdfoot" class="inbox">';
$foot_tpl[] = '<p>Powered by <a href="http://code.google.com/p/otsacm">OTSACM</a> © Copyright 2006–2007 Lukas Pajak</p>';
$foot_tpl[] = '<p><a href="index.php" >'.$lang_common['acm'].'</a></p>';

if( defined('ACM_DEBUG') ) {
	$foot_tpl[] = '<p>&nbsp;</p>';
	if( strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && $acm_config['use_gz'] && extension_loaded('zlib') )
		$foot_tpl[] = '<p>Debug mode ON. version '.ACM_VERSION.'. GZ output</p>';
	else $foot_tpl[] = '<p>Debug mode ON. version '.ACM_VERSION.'</p>';

	$foot_tpl[] = '<p>Generated in '.round($time, 3).'s. DB Queries: '.$db->iQueryCount.'</p>';
	$foot_tpl[] = '<p>&nbsp;</p>';
	$foot_tpl[] = '<p>'.$db->query_dump().'</p>';
}
$foot_tpl[] = '</div>';

$tpl_main = str_replace('<acm_footer>', implode("\n", $foot_tpl), $tpl_main);


if( strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && $acm_config['use_gz'] )
{
	if( extension_loaded('zlib') )
	{
		header('Content-Encoding: gzip');

		$gzip_size = strlen($tpl_main);

		$tpl_main = gzcompress($tpl_main, 9);
		$tpl_main = substr($tpl_main, 0, strlen($tpl_main) - 4);

		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		echo $tpl_main;
	}
}
else echo $tpl_main;

?>