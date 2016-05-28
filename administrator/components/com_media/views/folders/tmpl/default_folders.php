<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addStyleDeclaration("
#treeData {list-style: none; }
ul {list-style: none; margin: 0 0 15px 7px;}
a.ajaxInit.active { color: black; }
");

?>

<ul id="treeData">
	<?php if (isset($this->folders['children'])) : ?>
		<?php foreach ($this->folders['children'] as $folder) : ?>
			<li class="expanded folder">
				<?php $class = array('ajaxInit'); ?>
				<?php if ($folder['data']->relative == $this->current_folder) $class[] = 'active'; ?>
				<a href="#" data-href="<?php echo $folder['data']->relative; ?>" class="<?php echo implode(' ', $class) ?>">
					<span class="icon-folder-2 pull-left"></span>
					<?php echo $folder['data']->name; ?>
				</a>
				<?php echo $this->getFolderLevel($folder); ?>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>