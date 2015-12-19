<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$video = $displayData['path_relative'];
$videoPath = JURI::root() . 'images/' . $video;
?>
<video width="320" height="240" controls>
	<source src="<?php echo $videoPath; ?>" type="video/<?php echo $displayData['extension'] ?>">
	Your browser does not support the video tag.
</video>