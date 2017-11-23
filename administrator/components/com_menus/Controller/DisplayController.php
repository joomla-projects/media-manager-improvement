<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Menus\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\LanguageHelper;

/**
 * Base controller class for Menu Manager.
 *
 * @since  1.6
 */
class DisplayController extends BaseController
{
	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $default_view = 'menus';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean        $cachable   If true, the view output will be cached
	 * @param   array|boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  static    This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Check custom administrator menu modules
		if (ModuleHelper::isAdminMultilang())
		{
			$languages = LanguageHelper::getInstalledLanguages(1, true);
			$langCodes = array();

			foreach ($languages as $language)
			{
				if (isset($language->metadata['nativeName']))
				{
					$languageName = $language->metadata['nativeName'];
				}
				else
				{
					$languageName = $language->metadata['name'];
				}

				$langCodes[$language->metadata['tag']] = $languageName;
			}

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$query->select($db->qn('m.language'))
				->from($db->qn('#__modules', 'm'))
				->where($db->qn('m.module') . ' = ' . $db->quote('mod_menu'))
				->where($db->qn('m.published') . ' = 1')
				->where($db->qn('m.client_id') . ' = 1')
				->group($db->qn('m.language'));

			$mLanguages = $db->setQuery($query)->loadColumn();

			// Check if we have a mod_menu module set to All languages or a mod_menu module for each admin language.
			if (!in_array('*', $mLanguages) && count($langMissing = array_diff(array_keys($langCodes), $mLanguages)))
			{
				$app         = Factory::getApplication();
				$langMissing = array_intersect_key($langCodes, array_flip($langMissing));

				$app->enqueueMessage(\JText::sprintf('JMENU_MULTILANG_WARNING_MISSING_MODULES', implode(', ', $langMissing)), 'warning');
			}
		}

		return parent::display();
	}
}
