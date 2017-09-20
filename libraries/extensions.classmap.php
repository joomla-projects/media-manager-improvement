<?php
/**
 * @package    Joomla.Libraries
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access.
defined('_JEXEC') or die;

// Class map of the core extensions
JLoader::registerAlias('FieldsPlugin',     '\\Joomla\\Component\\Fields\\Administrator\\Plugin\\FieldsPlugin', '4.0');
JLoader::registerAlias('FieldsListPlugin', '\\Joomla\\Component\\Fields\\Administrator\\Plugin\\FieldsListPlugin', '4.0');

JLoader::registerAlias('TagsTableTag',     '\\Joomla\\Component\\Tags\\Administrator\\Table\\TagTable', '4.0');
