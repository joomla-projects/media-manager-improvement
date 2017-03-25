<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.resize
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Media Manager Resize Action
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgMediaActionCrop extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	public function onContentPrepareForm(JForm $form, $data)
	{
		if ($form->getName() != 'com_media.file')
		{
			return;
		}

		// THIS IS FOR DEMO PURPOSES ONLY!!!!
		JLayoutHelper::render('crop', array(), JPATH_PLUGINS . '/media-action/crop/layouts');

		$form->loadFile(JPATH_PLUGINS . '/media-action/crop/form/crop.xml');
	}
}
