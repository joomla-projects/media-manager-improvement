<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

$img = $this->_tmp_img;
$params     = new Registry;
$dispatcher = JEventDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_media.file', &$img, &$params));

$iconExtensions = array('avi',
'doc',
'mov',
'mp3',
'mp4',
'odc',
'odd',
'odt',
'ogg',
'pdf',
'ppt',
'rar',
'rtf',
'svg',
'sxd',
'tar',
'tgz',
'wma',
'wmv',
'xls',
'zip');

if (in_array($img->extension, $iconExtensions))
{
	$icon = $img->extension . '.png';
}
?>

	<li class="imgOutline thumbnail height-80 width-80 center">
		<a class="img-preview" href="javascript:ImageManager.populateFields('<?php echo $img->path_relative; ?>')" title="<?php echo $img->name; ?>" >
			<div class="height-50">
				<?php echo JHtml::_('image', 'media/media/images/mime-icon-32/' . $icon, JText::sprintf('COM_MEDIA_IMAGE_TITLE', $img->title, JHtml::_('number.bytes', $img->size)), array('width' => 32, 'height' => 32)); ?>
			</div>
			<div class="small">
				<?php echo JHtml::_('string.truncate', $img->name, 10, false); ?>
			</div>
		</a>
	</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_media.file', &$img, &$params));
