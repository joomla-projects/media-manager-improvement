<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\ArticlesLatest\Site\Helper\ArticlesLatestHelper;

$model = $app->bootComponent('com_content')->createMVCFactory($app)->createModel('Articles', 'Site', ['ignore_request' => true]);
$list = ArticlesLatestHelper::getList($params, $model);

require ModuleHelper::getLayoutPath('mod_articles_latest', $params->get('layout', 'default'));
