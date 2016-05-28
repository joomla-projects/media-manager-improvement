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
 * Media Manager Component Controller
 *
 * @since  1.5
 */
class MediaController extends JControllerLegacy
{
	/**
	 * MediaController constructor.
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$rt = parent::__construct($config);

		$viewName = $this->input->get('view');

		if (empty($viewName))
		{
			$this->input->set('view', 'folders');
		}

		return $rt;
	}

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

		return parent::display($cachable, $urlparams);
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
