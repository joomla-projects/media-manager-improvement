<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Manager Component Controller
 *
 * @since  1.5
 */
class MediaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController        This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JPluginHelper::importPlugin('content');

		$document = JFactory::getDocument();
		$vType = $document->getType();
		$vName = $this->input->get('view', 'folders');

		switch ($vName)
		{
			case 'editor':
				$vLayout = 'default';
				$mName = 'editor';

				break;

			case 'folders':

				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'folders';

				break;

			case 'file':
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'file';

				break;

			case 'files':
			default:
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName = 'files';

				break;

		}

		// Get/Create the view
		$view = $this->getView($mName, $vType, '', array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));

		// Get/Create the model
		if ($model = $this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}

	/**
	 * Validate FTP credentials
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}
}
