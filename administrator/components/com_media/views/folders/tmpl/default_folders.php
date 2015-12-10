<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//JFactory::getDocument()->addScriptDeclaration($script);

// Set up the sanitised target for the ul
//$ulTarget = str_replace('/', '-', $this->folders['data']->relative);


//if (isset($this->folders['children']))
//{
//	echo $folder['data']->relative;
//	echo $folder['data']->name;
//}

?>
<div id="tree">
	<ul id="treeData">
		<?php if (isset($this->folders['children'])) :
			foreach ($this->folders['children'] as $folder) :
				// Get a sanitised name for the target
				$target = str_replace('/', '-', $folder['data']->relative); ?>
				<li class="expanded folder">
					<a href="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $folder['data']->relative; ?>" target="folderframe">
						<span class="icon-folder-2 pull-left"></span>
						<?php echo $folder['data']->name; ?>
					</a>
					<?php echo $this->getFolderLevel($folder); ?>
				</li>
			<?php endforeach;
		endif; ?>
	</ul>
</div>