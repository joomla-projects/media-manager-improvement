<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var $this MediaViewFolders */

defined('_JEXEC') or die;

$actionUrl = 'index.php?option=com_media&amp;task=ftpValidate';
?>
<form action="<?php echo $actionUrl ?>" name="ftpForm" id="ftpForm" method="post">
	<fieldset title="<?php echo JText::_('COM_MEDIA_DESCFTPTITLE'); ?>">
		<legend><?php echo JText::_('COM_MEDIA_DESCFTPTITLE'); ?></legend>
		<?php echo JText::_('COM_MEDIA_DESCFTP'); ?>
		<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
		<input type="text" id="username" name="username" size="70" value="" />

		<label for="password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
		<input type="password" id="password" name="password" size="70" value="" />
	</fieldset>
</form>
