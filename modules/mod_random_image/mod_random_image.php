<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_random_image
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\RandomImage\Site\Helper\RandomImageHelper;

$link   = $params->get('link');
$folder = RandomImageHelper::getFolder($params);
$images = RandomImageHelper::getImages($params, $folder);
$image  = RandomImageHelper::getRandomImage($params, $images);

require ModuleHelper::getLayoutPath('mod_random_image', $params->get('layout', 'default'));
