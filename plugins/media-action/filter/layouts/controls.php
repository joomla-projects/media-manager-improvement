<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.filter
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>

<div class="filter-buttons">
	<p>
		<label for="brightness"><?php echo JText::_('PLG_MEDIA-ACTION_FILTER_BRIGHTNESS'); ?></label>
		<input type="range" min="-255" max="255" name="brightness" id="brightness" value="0" />
	</p>
	<p>
		<label for="contrast"><?php echo JText::_('PLG_MEDIA-ACTION_FILTER_CONTRAST'); ?></label>
		<input type="range" min="-255" max="255" name="contrast" id="contrast" value="0" />
	</p>
</div>
