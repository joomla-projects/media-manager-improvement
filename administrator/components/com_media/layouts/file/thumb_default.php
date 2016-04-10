<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$file = $displayData['file'];
$imagePath = JURI::root() . 'media/media/images/' . $file->icon_32;
?>
<?php echo JHtml::_('image', $imagePath, JText::sprintf('COM_MEDIA_IMAGE_TITLE', $file->title, JHtml::_('number.bytes', $file->size)), array(
	'width' => 32,
	'height' => 32,
)); ?>
<!--
@todo: Debugging info. Remove when ready:
<?php print_r($file); ?>
-->