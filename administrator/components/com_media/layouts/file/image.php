<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$image = $displayData['path_relative'];
$imagePath = JURI::root() . 'images/' . $image;
$name = $displayData['name'];
?>
<img src="<?php echo $imagePath; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" />