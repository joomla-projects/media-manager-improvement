<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Config\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Modules\Administrator\Controller\ModuleController;

/**
 * Component Controller
 *
 * @since  1.5
 */
class ModulesController extends BaseController
{
	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   \JInput              $input    Input
	 *
	 * @since  1.6
	 * @see    \JControllerLegacy
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
	}

	/**
	 * Method to handle cancel
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function cancel()
	{
		// Redirect back to home(base) page
		$this->setRedirect(\JUri::base());
	}

	/**
	 * Method to save module editing.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function save()
	{
		// Check for request forgeries.
		if (!\JSession::checkToken())
		{
			$this->app->enqueueMessage(\JText::_('JINVALID_TOKEN'));
			$this->app->redirect('index.php');
		}

		// Check if the user is authorized to do this.
		$user = \JFactory::getUser();

		if (!$user->authorise('module.edit.frontend', 'com_modules.module.' . $this->input->get('id'))
			&& !$user->authorise('module.edit.frontend', 'com_modules'))
		{
			$this->app->enqueueMessage(\JText::_('JERROR_ALERTNOAUTHOR'));
			$this->app->redirect('index.php');
		}

		// Set FTP credentials, if given.
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Get sumitted module id
		$moduleId = '&id=' . $this->input->getInt('id');

		// Get returnUri
		$returnUri = $this->input->post->get('return', null, 'base64');
		$redirect = '';

		if (!empty($returnUri))
		{
			$redirect = '&return=' . $returnUri;
		}

		\JLoader::register('ModulesDispatcher', JPATH_ADMINISTRATOR . '/components/com_modules/dispatcher.php');

		/** @var AdministratorApplication $app */
		$app = Factory::getContainer()->get(AdministratorApplication::class);
		$app->loadLanguage($this->app->getLanguage());

		/** @var \Joomla\CMS\Dispatcher\Dispatcher $dispatcher */
		$dispatcher = $app->bootComponent('com_modules')->getDispatcher($app);

		/** @var ModuleController $controllerClass */
		$controllerClass = $dispatcher->getController('Module');

		// Get a document object
		$document = \JFactory::getDocument();

		// Set backend required params
		$document->setType('json');

		// Execute backend controller
		Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_modules/forms');
		$return = $controllerClass->save();

		// Reset params back after requesting from service
		$document->setType('html');

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$data = $this->input->post->get('jform', array(), 'array');

			$this->app->setUserState('com_config.modules.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->app->enqueueMessage(\JText::_('JERROR_SAVE_FAILED'));
			$this->app->redirect(\JRoute::_('index.php?option=com_config&view=modules' . $moduleId . $redirect, false));
		}

		// Redirect back to com_config display
		$this->app->enqueueMessage(\JText::_('COM_CONFIG_MODULES_SAVE_SUCCESS'));

		// Set the redirect based on the task.
		switch ($this->input->getCmd('task'))
		{
			case 'apply':
				$this->app->redirect(\JRoute::_('index.php?option=com_config&view=modules' . $moduleId . $redirect, false));
				break;

			case 'save':
			default:

				if (!empty($returnUri))
				{
					$redirect = base64_decode(urldecode($returnUri));

					// Don't redirect to an external URL.
					if (!\JUri::isInternal($redirect))
					{
						$redirect = \JUri::base();
					}
				}
				else
				{
					$redirect = \JUri::base();
				}

				$this->setRedirect($redirect);
				break;
		}
	}
}
