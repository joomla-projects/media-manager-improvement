<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$folder = $this->getFolder();
$folderData = $folder['data'];
?>
<li class="imgOutline thumbnail height-80 width-80 center">
	<a href="#" data-href="<?php echo $folderData->name; ?>" class="ajaxInit">
		<div class="height-50">
			<span class="icon-folder-2"></span>
		</div>
		<div class="small">
			<?php echo $folderData->name; ?>
		</div>
	</a>
</li>