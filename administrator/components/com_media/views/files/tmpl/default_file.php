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

$file = $this->_tmp_file;
$params = new Registry;
$dispatcher = JEventDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_media.file', &$file, &$params));
?>

	<li class="imgOutline thumbnail height-80 width-80 center">
		<a class="img-preview"
		   href="index.php?option=com_media&view=file&file=<?php echo $file->path_relative; ?>"
		   <?php /* href="javascript:ImageManager.populateFields('<?php echo $file->path_relative; ?>')" */ ?>
		   title="<?php echo $file->name; ?>">
			<div class="thumbnail height-50">
				<?php
				$layoutFile = 'file.thumb_' . $file->file_type;
				$layout = new JLayoutFile($layoutFile);
				echo $layout->render(array('file' => $file));
				?>
			</div>
			<div class="filename small">
				<?php echo JHtml::_('string.truncate', $file->name, 10, false); ?>
			</div>
		</a>
	</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_media.file', &$file, &$params));
