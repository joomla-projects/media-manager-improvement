<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Cms\Controller\Controller;

/**
 * Api Media Controller
 *
 * This is NO public api controller, it is internal for the com_media component only!
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaControllerApi extends Controller
{
	/**
	 * Api endpoint for the media manager front end. The HTTP methods GET, PUT, POST and DELETE
	 * are supported.
	 *
	 * The following query parameters are processed:
	 * - path: The path of the resource, if not set then the default / is taken.
	 *
	 * Some examples with a more understandable rest url equivalent:
	 * - GET a list of folders below the root:
	 * 		index.php?option=com_media&task=api.files
	 * 		/api/files
	 * - GET a list of files and subfolders of a given folder:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop
	 * 		/api/files/sampledata/fruitshop
	 * - GET a list of files and subfolders of a given folder for a given filter:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop&filter=apple
	 * 		/api/files/sampledata/fruitshop?filter=apple
	 * - GET file information for a specific file:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test.jpg
	 * 		/api/files/sampledata/fruitshop/test.jpg
	 *
	 *
	 * - POST a new file or folder into a specific folder, the file or folder information is returned:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop
	 * 		/api/files/sampledata/fruitshop
	 *
	 * 		New file body:
	 * 		{
	 * 			"name": "test.jpg",
	 * 			"content":"base64 encoded image"
	 * 		}
	 * 		New folder body:
	 * 		{
	 * 			"name": "test",
	 * 		}
	 *
	 * - PUT a media file, the file or folder information is returned:
	 * 		index.php?option=com_media&task=api.files&format=json&path=/sampledata/fruitshop/test.jpg
	 * 		/api/files/sampledata/fruitshop/test.jpg
	 *
	 * 		Update file body:
	 * 		{
	 * 			"content":"base64 encoded image"
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
		// Get the required variables
		$path = $this->input->getPath('path', '/', 'path');

		// Determine the method
		$method = $this->input->getMethod() ? : 'GET';

		// Check token for requests which do modify files
		if (in_array(strtolower($method), array('post', 'put', 'delete')) && !JSession::checkToken('request'))
		{
			$this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));
			return;
		}

		try
		{
			// Gather the data according to the method
			switch (strtolower($method))
			{
				case 'get':
					$data = $this->getModel()->getFiles($path, $this->input->getWord('filter'));
					break;
				case 'delete':
					$this->getModel()->delete($path);
					break;
				case 'post':
					$content      = $this->input->json;
					$name         = $content->get('name');
					$mediaContent = base64_decode($content->get('content'));

					if ($mediaContent)
					{
						// A file needs to be created
						$this->getModel()->createFile($name, $path, $mediaContent);
					}
					else
					{
						// A file needs to be created
						$this->getModel()->createFolder($name, $path);
					}

					$data = $this->getModel()->getFile($path . '/' . $name);
					break;
				case 'put':
					$content      = $this->input->json;
					$name         = basename($path);
					$mediaContent = base64_decode($content->get('content'));

					$this->getModel()->updateFile($name, str_replace($name, '', $path), $mediaContent);

					$data = $this->getModel()->getFile($path . '/' . $name);
					break;
				default:
					throw new BadMethodCallException('Method not supported yet!');
			}

			// Return the data
			$this->sendResponse($data);
		}
		catch (MediaFileAdapterFilenotfoundexception $e)
		{
			$this->sendResponse($e, 404);
		}
		catch (Exception $e)
		{
			$errorCode = 500;

			if ($e->getCode() > 0)
			{
				$errorCode = $e->getCode();
			}
			$this->sendResponse($e, $errorCode);
		}
	}

	/**
	 * Send the given data as JSON response in the following format:
	 *
	 * {"success":true,"message":"ok","messages":null,"data":[{"type":"dir","name":"banners","path":"//"}]}
	 *
	 * @param   mixed   $data          The data to send
	 * @param   number  $responseCode  The response code
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function sendResponse($data = null, $responseCode = 200)
	{
		// Set the correct content type
		JFactory::getApplication()->setHeader('Content-Type', 'application/json');

		// Set the status code for the response
		http_response_code($responseCode);

		// Send the data
		echo new JResponseJson($data);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Model|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   3.0
	 */
	public function getModel($name = 'Api', $prefix = 'MediaModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
