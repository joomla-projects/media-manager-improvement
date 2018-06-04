<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Administrator\Controller;

defined('_JEXEC') or die;

/**
 * Featured content controller class.
 *
 * @since  1.6
 */
class FeaturedController extends ArticlesController
{
	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function delete()
	{
		// Check for request forgeries
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		$user = \JFactory::getUser();
		$ids  = $this->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.delete', 'com_content.article.' . (int) $id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				\JFactory::getApplication()->enqueueMessage(\JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'notice');
			}
		}

		if (empty($ids))
		{
			\JFactory::getApplication()->enqueueMessage(\JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');
		}
		else
		{
			// Get the model.
			/** @var \Joomla\Component\Content\Administrator\Model\FeatureModel $model */
			$model = $this->getModel();

			// Remove the items.
			if (!$model->featured($ids, 0))
			{
				\JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect('index.php?option=com_content&view=featured');
	}

	/**
	 * Method to publish a list of articles.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function publish()
	{
		parent::publish();

		$this->setRedirect('index.php?option=com_content&view=featured');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Feature', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
