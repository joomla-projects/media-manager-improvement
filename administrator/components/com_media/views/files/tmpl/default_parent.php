<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$parent = $this->folders['parent'];
?>
<li class="imgOutline thumbnail height-80 width-80 center">
	<a href="#" data-href="<?php echo $parent; ?>" class="ajaxInit" title="<?php echo $parent; ?>">
		<div class="height-50">
			<span class="icon-arrow-up-2"></span>
		</div>
		<div class="small">
			..
		</div>
	</a>
</li>