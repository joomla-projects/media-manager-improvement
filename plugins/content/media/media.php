<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.finder
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// @todo Remove -> Sample Data for testing
class TestMedia {
	public function getFileExtension()
	{
		return 'jpg';
	}

	public function getFileRoute()
	{
		return 'images/powered_by.png';
	}

	public $title = 'Test Title';
}

/**
 * Media Manager Plugin
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgContentMedia extends JPlugin
{
	/**
	 * Regex to search for
	 *
	 * @var     string
	 * @since   __DEPLOY_VERSION__
	 */
	protected $regex = '/{media\s(.*?)}/i';

	/**
	 * Replaces the {media} tags with the fitting media
	 *
	 * @param   string   $context   The context of the content being passed to the plugin
	 * @param   object   &$article  The article object
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed  void or true
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if (strpos($article->text, 'media') === false && strpos($article->text, 'media') === false)
		{
			return true;
		}

		// Find all instances of plugin and put in $matches for media
		// $matches[0] is full pattern match, $matches[1] is the id
		preg_match_all($this->regex, $article->text, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matchesList = explode(',', $match[1]);
				$id          = trim($matchesList[0]);

				$output = $this->getReplacement($id);

				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}
	}

	/**
	 * Get the actual content for the media element
	 *
	 * @param   int  $mediaId  The id of the media element
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getReplacement($mediaId)
	{
		$mediaFile = new TestMedia(); //MediaFile::load($mediaId);

		if (!$mediaFile)
		{
			// @todo Add error handling
			return '';
		}

		/** @var array $plugins */
		$plugins   = JPluginHelper::getPlugin('media-type');
		$extension = $mediaFile->getFileExtension();
		$out       = array();

		foreach ($plugins as $plugin)
		{
			// Load Plugin @todo improve
			include_once JPATH_BASE . '/plugins/media-type/' . $plugin->name . '/' . $plugin->name . '.php';
			$className = 'PlgMediaType' . ucfirst($plugin->name);

			$supportedExtensions = call_user_func($className . '::getMediaExtensions');

			if (in_array($extension, $supportedExtensions))
			{
				// Instantiate Plugin
				$handler = new $className();

				$out[] = $handler->render($mediaFile);
			}
		}

		return implode("\n", $out);
	}
}
