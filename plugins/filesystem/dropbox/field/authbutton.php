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
		$formData  = $this->form->getData();
		$formData  = $formData->data;
		$app_secret = '';


		$html   = '<a href="https://www.dropbox.com/oauth2/authorize?response_type=code&redirect_uri='. urlencode('http://localhost/media-manager-improvement/administrator/index.php?option=com_media&task=plugin.oauthcallback&plugin=dropbox') . '&client_id=' . $app_secret . '">Button</a>';
		return $html;
	}
}