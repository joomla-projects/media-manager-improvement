<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Cache\Controller;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Cache\CacheController;

/**
 * Joomla! Cache callback type object
 *
 * @since  11.1
 */
class CallbackController extends CacheController
{
	/**
	 * Executes a cacheable callback if not found in cache else returns cached output and result
	 *
	 * @param   callable  $callback    Callback or string shorthand for a callback
	 * @param   array     $args        Callback arguments
	 * @param   mixed     $id          Cache ID
	 * @param   boolean   $wrkarounds  True to use wrkarounds
	 * @param   array     $woptions    Workaround options
	 *
	 * @return  mixed  Result of the callback
	 *
	 * @since   11.1
	 */
	public function get($callback, $args = array(), $id = false, $wrkarounds = false, $woptions = array())
	{
		if (!$id)
		{
			// Generate an ID
			$id = $this->_makeId($callback, $args);
		}

		$data = $this->cache->get($id);

		$locktest = (object) array('locked' => null, 'locklooped' => null);

		if ($data === false)
		{
			$locktest = $this->cache->lock($id);

			// If locklooped is true try to get the cached data again; it could exist now.
			if ($locktest->locked === true && $locktest->locklooped === true)
			{
				$data = $this->cache->get($id);
			}
		}

		if ($data !== false)
		{
			if ($locktest->locked === true)
			{
				$this->cache->unlock($id);
			}

			$data = unserialize(trim($data));

			if ($wrkarounds)
			{
				echo Cache::getWorkarounds(
					$data['output'],
					array('mergehead' => $woptions['mergehead'] ?? 0)
				);
			}
			else
			{
				echo $data['output'];
			}

			return $data['result'];
		}

		if (!is_array($args))
		{
			$referenceArgs = !empty($args) ? array(&$args) : array();
		}
		else
		{
			$referenceArgs = &$args;
		}

		if ($locktest->locked === false && $locktest->locklooped === true)
		{
			// We can not store data because another process is in the middle of saving
			return call_user_func_array($callback, $referenceArgs);
		}

		$coptions = array();

		if (isset($woptions['modulemode']) && $woptions['modulemode'] == 1)
		{
			$document = \JFactory::getDocument();

			if (method_exists($document, 'getHeadData'))
			{
				$coptions['headerbefore'] = $document->getHeadData();
			}

			$coptions['modulemode'] = 1;
		}
		else
		{
			$coptions['modulemode'] = 0;
		}

		$coptions['nopathway'] = $woptions['nopathway'] ?? 1;
		$coptions['nohead']    = $woptions['nohead'] ?? 1;
		$coptions['nomodules'] = $woptions['nomodules'] ?? 1;

		ob_start();
		ob_implicit_flush(false);

		$result = call_user_func_array($callback, $referenceArgs);
		$output = ob_get_clean();

		$data = array('result' => $result);

		if ($wrkarounds)
		{
			$data['output'] = Cache::setWorkarounds($output, $coptions);
		}
		else
		{
			$data['output'] = $output;
		}

		// Store the cache data
		$this->cache->store(serialize($data), $id);

		if ($locktest->locked === true)
		{
			$this->cache->unlock($id);
		}

		echo $output;

		return $result;
	}

	/**
	 * Generate a callback cache ID
	 *
	 * @param   callback  $callback  Callback to cache
	 * @param   array     $args      Arguments to the callback method to cache
	 *
	 * @return  string  MD5 Hash
	 *
	 * @since   11.1
	 */
	protected function _makeId($callback, $args)
	{
		if (is_array($callback) && is_object($callback[0]))
		{
			$vars        = get_object_vars($callback[0]);
			$vars[]      = strtolower(get_class($callback[0]));
			$callback[0] = $vars;
		}

		// A Closure can't be serialized, so to generate the ID we'll need to get its hash
		if (is_a($callback, 'closure'))
		{
			$hash = spl_object_hash($callback);

			return md5($hash . serialize(array($args)));
		}

		return md5(serialize(array($callback, $args)));
	}
}
