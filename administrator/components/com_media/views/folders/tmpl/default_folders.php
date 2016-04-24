<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//JFactory::getDocument()->addScriptDeclaration($script);

// Set up the sanitised target for the ul
//$ulTarget = str_replace('/', '-', $this->folders['data']->relative);


//if (isset($this->folders['children']))
//{
// echo $folder['data']->relative;
// echo $folder['data']->name;
//}

JFactory::getDocument()->addStyleDeclaration("
#treeData {list-style: none; }
ul {list-style: none; margin: 0 0 15px 7px;}
");
?>

<ul id="treeData">
<?php if (isset($this->folders['children'])) : ?>
	<?php foreach ($this->folders['children'] as $folder) : ?>
		<?php // Get a sanitised name for the target ?>
		<?php $target = str_replace('/', '-', $folder['data']->relative); ?>
		<li class="expanded folder">
			<a href="#" data-href="<?php echo $folder['data']->relative; ?>" class="ajaxInit">
				<span class="icon-folder-2 pull-left"></span>
				<?php echo $folder['data']->name; ?>
			</a>
			<?php echo $this->getFolderLevel($folder); ?>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
