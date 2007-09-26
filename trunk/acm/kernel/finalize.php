<?php

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