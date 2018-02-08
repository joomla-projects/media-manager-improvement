<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.Joomlaupdate
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\Event\SubscriberInterface;
use Joomla\Module\Quickicon\Administrator\Event\QuickIconsEvent;

/**
 * Joomla! update notification plugin
 *
 * @since  2.5
 */
class PlgQuickiconJoomlaupdate extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onGetIcons' => 'getCoreUpdateNotification',
		];
	}

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param   QuickIconsEvent  $event  The event object
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function getCoreUpdateNotification(QuickIconsEvent $event)
	{
		$context = $event->getContext();

		if ($context !== $this->params->get('context', 'mod_quickicon') || !Factory::getUser()->authorise('core.manage', 'com_installer'))
		{
			return;
		}

		Text::script('PLG_QUICKICON_JOOMLAUPDATE_ERROR', true);
		Text::script('PLG_QUICKICON_JOOMLAUPDATE_UPDATEFOUND_BUTTON', true);
		Text::script('PLG_QUICKICON_JOOMLAUPDATE_UPDATEFOUND_MESSAGE', true);
		Text::script('PLG_QUICKICON_JOOMLAUPDATE_UPDATEFOUND', true);
		Text::script('PLG_QUICKICON_JOOMLAUPDATE_UPTODATE', true);

		Factory::getDocument()->addScriptOptions(
			'js-joomla-update',
			[
				'url'     => Uri::base() . 'index.php?option=com_joomlaupdate',
				'ajaxUrl' => Uri::base() . 'index.php?option=com_installer&view=update&task=update.ajax&' . Session::getFormToken() . '=1',
				'version' => JVERSION,
			]
		);

		HTMLHelper::_('behavior.core');
		HTMLHelper::_('script', 'plg_quickicon_joomlaupdate/jupdatecheck.min.js', array('version' => 'auto', 'relative' => true));

		// Add the icon to the result array
		$result = $event->getArgument('result', []);

		$result[] = [
			[
				'link'  => 'index.php?option=com_joomlaupdate',
				'image' => 'fa fa-joomla',
				'icon'  => '',
				'text'  => Text::_('PLG_QUICKICON_JOOMLAUPDATE_CHECKING'),
				'id'    => 'plg_quickicon_joomlaupdate',
				'group' => 'MOD_QUICKICON_MAINTENANCE',
			],
		];

		$event->setArgument('result', $result);
	}
}
