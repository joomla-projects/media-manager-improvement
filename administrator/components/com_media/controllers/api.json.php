<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Api Media Controller
 *
 * This is NO public api controller, it is internal for the com_media component only!
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaControllerApi extends JControllerLegacy
{
	/**
	 * The local file adapter to work with.
	 *
	 * @var MediaFileAdapterInterface
	 */
	protected $adapter = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   3.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (!isset($config['fileadapter']))
		{
			// Compile the root path
			$root = JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('file_path', 'images');
			$root = rtrim($root) . '/';

			// Default to the local adapter
			$config['fileadapter'] = new MediaFileAdapterLocal($root);
		}

		$this->adapter = $config['fileadapter'];
	}

	/**
	 * Api endpoint for the media manager front end. The HTTP methods GET, PUT, POST and DELETE
	 * are supported.
	 *
	 * The following query parameters are processed:
	 * - path: The path of the resource, if not set then the default / is taken.
	 *
	 * Some examples with a more understandable rest url equivalent:
	 * - GET a list of files and subfolders of a given folder, if none is set, the root folder is used:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop
	 * 		/api/files/sampledata/fruitshop
	 * 		Return body:
	 * 		[
	 * 			{"type":"dir","name":"banners","path":"\/banners"},
	 * 			{"type":"dir","name":"headers","path":"\/headers"},
	 * 			{"type":"file","name":"index.html","path":"\/index.html","extension":"html","size":31},
	 * 			{"type":"file","name":"joomla_black.png","path":"\/joomla_black.png","extension":"png","size":4979},
	 * 			{"type":"file","name":"powered_by.png","path":"\/powered_by.png","extension":"png","size":3197}
	 * 		]
	 * - GET file information for a specific file:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test.jpg
	 * 		/api/files/sampledata/fruitshop/test.jpg
	 * 		Return body:
	 * 		[
	 * 			{
	 * 				"type":"file",
	 * 				"name":"index.html",
	 * 				"path":"\/index.html",
	 * 				"extension":"html",
	 * 				"size":31
	 * 			}
	 * 		]
	 * - GET file information for a specific file:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test.jpg&action=preview
	 * 		/api/files/sampledata/fruitshop/test.jpg/preview
	 * 		Return body:
 * 			{
 * 				"type":"file",
 * 				"name":"index.html",
 * 				"path":"\/index.html",
 * 				"extension":"html",
 * 				"size":31,
 * 				"content":"a base 64 encoded string of the content of the file",
 * 			}
	 *
	 * - POST a new file or folder into a specific folder:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop
	 * 		/api/files/sampledata/fruitshop
	 * 		Request body new file:
	 * 		{
	 * 			"name": "test.jpg",
	 * 			"content":"base64 encoded image"
	 * 		}
	 * 		Request body new folder:
	 * 		{
	 * 			"name": "test",
	 * 		}
	 *
	 * - PUT a media file:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test.jpg
	 * 		/api/files/sampledata/fruitshop/test.jpg
	 * 		Request body:
	 * 		{
	 * 			"content":"base64 encoded image"
	 * 		}
	 *
	 * - PUT process a media file:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test.jpg&action=process
	 * 		/api/files/sampledata/fruitshop/test.jpg/process
	 * 		Request body:
	 * 		{
	 * 			"options":
	 * 			{
	 * 				"rotate":[45],
	 * 				"crop":
	 * 				{
	 * 					"x":100,
	 * 					"y":25,
	 * 					"width":200,
	 * 					"height":300
	 * 				}
	 * 			}
	 * 		}
	 *
	 * - DELETE an existing folder in a specific folder:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test
	 * 		/api/files/sampledata/fruitshop/test
	 * - DELETE an existing file in a specific folder:
	 * 		index.php?option=com_media&task=api.files&path=/sampledata/fruitshop/test.jpg
	 * 		/api/files/sampledata/fruitshop/test.jpg
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function files()
	{
		// @todo add ACL check

		// Get the required variables
		$path = $this->input->getPath('path', '/');

		// Determine the method
		$method = $this->input->getMethod() ? : 'GET';

		// The data to return
		$data = array();
		try
		{
			// Gather the data according to the method
			switch (strtolower($method))
			{
				case 'get':
					$data = $this->adapter->getFiles($path);

					// Add the file contents when a preview is requested
					if ($data && pathinfo($path, PATHINFO_EXTENSION) && $this->input->get('action') == 'preview')
					{
						$data[0]->content = $this->process($this->adapter->getLocalFilePath($path), false);
					}
					break;
				case 'delete':
					$data = $this->adapter->delete($path);
					break;
				case 'post':
					$content      = $this->input->json;
					$name         = $content->get('name');
					$mediaContent = base64_decode($content->get('content'));

					if (pathinfo($path, PATHINFO_EXTENSION))
					{
						// A file needs to be created
						$data = $this->adapter->createFile($name, $path, $mediaContent);
					}
					else
					{
						// A file needs to be created
						$data = $this->adapter->createFolder($name, $path);
					}
					break;
				case 'put':
					if ($this->input->get('action') == 'process')
					{
						$this->process($this->adapter->getLocalFilePath($path));
					}
					else
					{
						$content      = $this->input->json;
						$name         = basename($path);
						$mediaContent = base64_decode($content->get('content'));

						$this->adapter->updateFile($name, str_replace($name, '', $path), $mediaContent);
					}
					break;
				default:
					throw new BadMethodCallException('Method not supported yet!');
			}

			// Return the data
			$this->sendResponse($data);
		}
		catch (Exception $e)
		{
			$this->sendResponse($e);
		}
	}

		/**
	 * Trigger the process event.
	 *
	 * @param   string   $path        The full local path of the file to process
	 * @param   boolean  $useTmpFile  Should the processing be done on a temporary file
	 *
	 * @return  string  The contents of the file base64 encoded
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function process($path, $useTmpFile = false)
	{
		// Work on a temp file when requested
		if ($useTmpFile)
		{
			// Create the tmp path
			$tmpFile = JPATH_ROOT . '/' . basename($path);

			// Copy the original file to the temp one
			JFile::copy($path, $tmpFile);

			// Assign the new path
			$path = $tmpFile;
		}

		// Load the media action plugins
		JPluginHelper::importPlugin('media-action');

		// Trigger the event
		JEventDispatcher::getInstance()->trigger('onMediaProcess', $path, $this->input->json->get('options', array()));

		// Return the file contents base64 encoded
		return base64_encode(file_get_contents($path));
	}

	/**
	 * Send the given data as JSON response in the following format:
	 *
	 * {"success":true,"message":"ok","messages":null,"data":[{"type":"dir","name":"banners","path":"//"}]}
	 *
	 * @param   mixed  $data  The data to send
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function sendResponse($data = null)
	{
		echo new JResponseJson($data);
	}
}
