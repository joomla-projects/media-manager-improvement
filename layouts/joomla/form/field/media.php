<?php
/**
 * @package     Joomla.Admin
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Component\ComponentHelper;

/**
 * Layout variables
 * ---------------------
 *
 * @var  string   $asset The asset text
 * @var  string   $authorField The label text
 * @var  integer  $authorId The author id
 * @var  string   $class The class text
 * @var  boolean  $disabled True if field is disabled
 * @var  string   $folder The folder text
 * @var  string   $id The label text
 * @var  string   $link The link text
 * @var  string   $name The name text
 * @var  string   $preview The preview image relative path
 * @var  integer  $previewHeight The image preview height
 * @var  integer  $previewWidth The image preview width
 * @var  string   $onchange  The onchange text
 * @var  boolean  $readonly True if field is readonly
 * @var  integer  $size The size text
 * @var  string   $value The value text
 * @var  string   $src The path and filename of the image
 */
extract($displayData);

$attr = '';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="form-control hasTooltip field-media-input ' . $class . '"' : ' class="form-control hasTooltip field-media-input"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

switch ($preview)
{
	case 'no': // Deprecated parameter value
	case 'false':
	case 'none':
		$showPreview = false;
		$showAsTooltip = false;
		break;
	case 'yes': // Deprecated parameter value
	case 'true':
	case 'show':
		$showPreview = true;
		$showAsTooltip = false;
		break;
	case 'tooltip':
	default:
		$showPreview = true;
		$showAsTooltip = true;
		break;
}

// Pre fill the contents of the popover
if ($showPreview)
{
	if ($value && file_exists(JPATH_ROOT . '/' . $value))
	{
		$src = JUri::root() . $value;
	}
	else
	{
		$src = JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY');
	}

	if ($showAsTooltip) {
		$showPreview = 'tooltip';
	} else {
		$showPreview = 'static';
	}
}

// The url for the modal
$url    = ($readonly ? ''
	: ($link ? $link
		: 'index.php?option=com_media&amp;tmpl=component&amp;asset='
		. $asset . '&amp;author=' . $authorId)
	. '&amp;fieldid={field-media-id}&amp;path=' . $folder);
?>
<joomla-field-media class="field-media-wrapper"
		basepath="<?php echo JUri::root(); ?>"
		rootfolder="<?php echo ComponentHelper::getParams('com_media')->get('file_path', 'images'); ?>"
		url="<?php echo $url; ?>"
		modalcont=".modal"
		modalwidth="100%"
		modalheight="400px"
		input=".field-media-input"
		buttonselect=".button-select"
		buttonclear=".button-clear"
		buttonsaveselected=".button-save-selected"
		preview="<?php echo $showPreview; ?>"
		previewcontainer=".field-media-preview"
		previewwidth="<?php echo $previewWidth; ?>"
		previewheight="<?php echo $previewHeight; ?>"
>
	<?php
	// Render the modal
	echo JHtml::_('bootstrap.renderModal',
		'imageModal_'. $id,
		array(
			'url'         => $url,
			'title'       => JText::_('JLIB_FORM_CHANGE_IMAGE'),
			'closeButton' => true,
			'height' => '100%',
			'width'  => '100%',
			'modalWidth'  => '80',
			'bodyHeight'  => '60',
			'footer'      => '<button class="btn btn-secondary button-save-selected">' . JText::_('JSELECT') . '</button><button class="btn btn-secondary" data-dismiss="modal">' . JText::_('JCANCEL') . '</button>'
		)
	);

	JHtml::_('webcomponent', ['joomla-field-media' => 'system/joomla-field-media.min.js'], ['relative' => true, 'version' => 'auto', 'detectBrowser' => false, 'detectDebug' => false]);
	JText::script('JLIB_FORM_MEDIA_PREVIEW_EMPTY', true);
	?>
	<div class="input-group">
		<?php if ($showPreview && $showAsTooltip) : ?>
			<div rel="popover" class="input-group-addon pop-helper field-media-preview"
					title="<?php echo JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'); ?>"
					data-original-title="<?php echo JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'); ?>" data-trigger="hover">
				<span class="icon-eye" aria-hidden="true"></span>
			</div>
		<?php endif; ?>
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" readonly="readonly"<?php echo $attr; ?>>
		<?php if ($disabled != true) : ?>
			<div class="input-group-btn">
				<a class="btn btn-secondary button-select"><?php echo JText::_("JLIB_FORM_BUTTON_SELECT"); ?></a>
				<a class="btn btn-secondary hasTooltip button-clear" title="<?php echo JText::_("JLIB_FORM_BUTTON_CLEAR"); ?>"><span class="icon-remove" aria-hidden="true"></span></a>
			</div>
		<?php endif; ?>
	</div>
</joomla-field-media>