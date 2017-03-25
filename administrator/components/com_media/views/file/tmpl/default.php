<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @var JForm $form
 */
$form = $this->form;

$fieldSets = $form->getFieldsets();

if ($fieldSets)
{
	echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'attrib-' . reset($fieldSets)->name));

	echo JLayoutHelper::render('joomla.edit.params', $this);

	echo JHtml::_('bootstrap.endTabSet');
}
?>

<img id="media-edit-file" src="<?php echo $this->fullFilePath ?>"
