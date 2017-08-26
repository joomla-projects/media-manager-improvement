<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAuthButton extends JFormField
{
	protected $type = "AuthButton";

	public function getLabel()
	{
		return '';
	}

	public function getInput()
	{
		$params = \Joomla\CMS\Plugin\PluginHelper::getPlugin('dropbox');
		$token  = '';

		$html   = '<a href="https://www.dropbox.com/oauth2/authorize?response_type=code&client_id="'. $token . '&redirect_uri="'. urldecode('http://localhost/media-manager-improvement/administrator/index.php?option=com_media&task=plugin.oauthcallback&plugin=dropbox') . '">Button</a>';
		return $html;
	}
}