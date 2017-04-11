<?php
/**
 * @package    media-manager-improvement
 *
 * @author     Kasun <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

/**
 * Local plugin.
 *
 * @package  media-manager-improvement
 * @since    1.0
 */
class PlgFileSystemLocal extends JPlugin
{
    /**
     * Application object
     *
     * @var    JApplicationCms
     * @since  1.0
     */
    protected $app;

    /**
     * Database object
     *
     * @var    JDatabaseDriver
     * @since  1.0
     */
    protected $db;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $autoloadLanguage = true;


}
