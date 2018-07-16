<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

// Add javascripts
HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.formvalidator');

// Add stylesheets
HTMLHelper::_('stylesheet', 'media/com_media/css/mediamanager.css');
HTMLHelper::_('script', 'com_media/edit-images.js', array('version' => 'auto', 'relative' => true));

$params = ComponentHelper::getParams('com_media');

/**
 * @var JForm $form
 */
$form = $this->form;

$tmpl = Factory::getApplication()->input->getCmd('tmpl');

// Load the toolbar when we are in an iframe
if ($tmpl == 'component')
{
	echo Toolbar::getInstance('toolbar')->render();
}

// Populate the media config
$config = [
	'apiBaseUrl'              => Uri::root() . 'administrator/index.php?option=com_media&format=json',
	'csrfToken'               => Session::getFormToken(),
	'uploadPath'              => $this->file->path,
	'editViewUrl'             => Uri::root() . 'administrator/index.php?option=com_media&view=file' . (!empty($tmpl) ? ('&tmpl=' . $tmpl) : ''),
	'allowedUploadExtensions' => $params->get('upload_extensions', ''),
	'maxUploadSizeMb'         => $params->get('upload_maxsize', 10),
	'contents'                => $this->file->content,
];

Factory::getDocument()->addScriptOptions('com_media', $config);

$this->useCoreUI = true;
?>
<div class="row">
	<form action="#" method="post" name="adminForm" id="media-form" class="form-validate col-md-12">
	<?php $fieldSets = $form->getFieldsets(); ?>
	<?php if ($fieldSets) : ?>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'attrib-' . reset($fieldSets)->name)); ?>
		<div class="row">
			<div id="media-manager-edit-container" class="media-manager-edit d-flex justify-content-around form-validate col-md-9 p-4"></div>
			<div class="col-md-3">
				<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	<?php endif; ?>
	</form>
</div>
