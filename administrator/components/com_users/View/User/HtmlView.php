<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Users\Administrator\View\User;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * User view class.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * Gets the available groups
	 *
	 * @var  array
	 */
	protected $grouplist;

	/**
	 * The groups this user is assigned to
	 *
	 * @var     array
	 * @since   1.6
	 */
	protected $groups;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * Configuration forms for all two-factor authentication methods
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $tfaform;

	/**
	 * Returns the one time password (OTP) – a.k.a. two factor authentication –
	 * configuration for the user.
	 *
	 * @var    \stdClass
	 * @since  3.2
	 */
	protected $otpConfig;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function display($tpl = null)
	{
		$this->form      = $this->get('Form');
		$this->item      = $this->get('Item');
		$this->state     = $this->get('State');
		$this->tfaform   = $this->get('Twofactorform');
		$this->otpConfig = $this->get('otpConfig');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		// Prevent user from modifying own group(s)
		$user = \JFactory::getUser();

		if ((int) $user->id != (int) $this->item->id || $user->authorise('core.admin'))
		{
			$this->grouplist = $this->get('Groups');
			$this->groups    = $this->get('AssignedGroups');
		}

		$this->form->setValue('password', null);
		$this->form->setValue('password2', null);

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		\JFactory::getApplication()->input->set('hidemainmenu', true);

		$user      = \JFactory::getUser();
		$canDo     = ContentHelper::getActions('com_users');
		$isNew     = ($this->item->id == 0);
		$isProfile = $this->item->id == $user->id;

		\JToolbarHelper::title(
			\JText::_(
				$isNew ? 'COM_USERS_VIEW_NEW_USER_TITLE' : ($isProfile ? 'COM_USERS_VIEW_EDIT_PROFILE_TITLE' : 'COM_USERS_VIEW_EDIT_USER_TITLE')
			),
			'user ' . ($isNew ? 'user-add' : ($isProfile ? 'user-profile' : 'user-edit'))
		);

		$toolbarButtons = [];

		if ($canDo->get('core.edit') || $canDo->get('core.create'))
		{
			$toolbarButtons[] = ['apply', 'user.apply'];
			$toolbarButtons[] = ['save', 'user.save'];
		}

		if ($canDo->get('core.create') && $canDo->get('core.manage'))
		{
			$toolbarButtons[] = ['save2new', 'user.save2new'];
		}

		\JToolbarHelper::saveGroup(
			$toolbarButtons,
			'btn-success'
		);

		if (empty($this->item->id))
		{
			\JToolbarHelper::cancel('user.cancel');
		}
		else
		{
			\JToolbarHelper::cancel('user.cancel', 'JTOOLBAR_CLOSE');
		}

		\JToolbarHelper::divider();
		\JToolbarHelper::help('JHELP_USERS_USER_MANAGER_EDIT');
	}
}
