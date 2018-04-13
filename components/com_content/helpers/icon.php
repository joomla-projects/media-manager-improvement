<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Content Component HTML Helper
 *
 * @since  1.5
 */
abstract class JHtmlIcon
{
	/**
	 * Method to generate a link to the create item page for the given category
	 *
	 * @param   object    $category  The category information
	 * @param   Registry  $params    The item parameters
	 * @param   array     $attribs   Optional attributes for the link
	 * @param   boolean   $legacy    True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the create item link
	 *
	 * @deprecated 5.0 Use the class \Joomla\Component\Content\Site\Service\HTML\Icon instead
	 */
	public static function create($category, $params, $attribs = array(), $legacy = false)
	{
		return self::getIcon()->create($category, $params, $attribs, $legacy);
	}

	/**
	 * Method to generate a link to the email item page for the given article
	 *
	 * @param   object    $article  The article information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the email item link
	 *
	 * @deprecated 5.0 Use the class \Joomla\Component\Content\Site\Service\HTML\Icon instead
	 */
	public static function email($article, $params, $attribs = array(), $legacy = false)
	{
		return self::getIcon()->email($article, $params, $attribs, $legacy);
	}

	/**
	 * Display an edit icon for the article.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   object    $article  The article information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string	The HTML for the article edit icon.
	 *
	 * @since   1.6
	 *
	 * @deprecated 5.0 Use the class \Joomla\Component\Content\Site\Service\HTML\Icon instead
	 */
	public static function edit($article, $params, $attribs = array(), $legacy = false)
	{
		return self::getIcon()->edit($article, $params, $attribs, $legacy);
	}

	/**
	 * Method to generate a popup link to print an article
	 *
	 * @param   object    $article  The article information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 *
	 * @deprecated 5.0 Use the class \Joomla\Component\Content\Site\Service\HTML\Icon instead
	 */
	public static function print_popup($article, $params, $attribs = array(), $legacy = false)
	{
		return self::getIcon()->print_popup($article, $params, $attribs, $legacy);
	}

	/**
	 * Method to generate a link to print an article
	 *
	 * @param   object    $article  Not used, @deprecated for 4.0
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Not used, @deprecated for 4.0
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 *
	 * @deprecated 5.0 Use the class \Joomla\Component\Content\Site\Service\HTML\Icon instead
	 */
	public static function print_screen($article, $params, $attribs = array(), $legacy = false)
	{
		return self::getIcon()->print_screen($article, $params, $attribs, $legacy);
	}

	/**
	 * Creates an icon instance.
	 *
	 * @return  \Joomla\Component\Content\Site\Service\HTML\Icon
	 */
	private static function getIcon()
	{
		return (new \Joomla\Component\Content\Site\Service\HTML\Icon(Joomla\CMS\Factory::getApplication()));
	}
}
