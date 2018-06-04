<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$form  = $displayData->getForm();
$title = $form->getField('title') ? 'title' : ($form->getField('name') ? 'name' : '');

?>
<div class="m-t-2 m-b-3">
	<?php
	echo $title ? $form->renderField($title) : '';
	echo $form->renderField('alias');
	?>
</div>
