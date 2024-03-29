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
 * @subpackage 	ModAdmin
 * @version 		3
 */

define('ACM_ADMIN', true);
define('ACM_ROOT', './');
require ACM_ROOT.'kernel/common.php';

if( $cur_user['is_admin'] != true ) redirect('index.php');

require ACM_ROOT.'lang/'.$acm_config['lang'].'/options.php';

?>
<div id="brdmain" >
<div class="box" >
	<div class="inbox" >
	
	<p><?php echo $lang_common['female']; ?></p>
	<p>
	&nbsp;<a href="admin_editprofile.php?id=1" ><?php echo $lang_common['voc_none']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=2" ><?php echo $lang_common['voc_sorc']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=3" ><?php echo $lang_common['voc_druid']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=4" ><?php echo $lang_common['voc_paladin']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=5" ><?php echo $lang_common['voc_knight']; ?></a>
	</p>
	<p><?php echo $lang_common['male']; ?></p>
	<p>
	&nbsp;<a href="admin_editprofile.php?id=11" ><?php echo $lang_common['voc_none']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=12" ><?php echo $lang_common['voc_sorc']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=13" ><?php echo $lang_common['voc_druid']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=14" ><?php echo $lang_common['voc_paladin']; ?></a>
	&nbsp;<a href="admin_editprofile.php?id=15" ><?php echo $lang_common['voc_knight']; ?></a>
	</p>
	<p>Items sheet</p>
	<p>
	&nbsp;<a href="admin_editor.php?profile=0" ><?php echo $lang_common['voc_none']; ?></a>
	&nbsp;<a href="admin_editor.php?profile=1" ><?php echo $lang_common['voc_sorc']; ?></a>
	&nbsp;<a href="admin_editor.php?profile=2" ><?php echo $lang_common['voc_druid']; ?></a>
	&nbsp;<a href="admin_editor.php?profile=3" ><?php echo $lang_common['voc_paladin']; ?></a>
	&nbsp;<a href="admin_editor.php?profile=4" ><?php echo $lang_common['voc_knight']; ?></a>
	</p>

	</div>
</div>
</div>
<?php

$page_title = $lang_common['Chars profiles'];
$page_style = 'admin_profiles';
require ACM_ROOT.'kernel/finalize.php';

?>