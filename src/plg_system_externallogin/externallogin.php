<?php

/**
 * @package     External Login
 * @subpackage  External Login Plugin
 * @copyright   Copyright (C) 2008-2012 Christophe Demko, Ioannis Barounis, Alexandre Gandois. All rights reserved.
 * @author      Christophe Demko
 * @author      Ioannis Barounis
 * @author      Alexandre Gandois
 * @link        http://www.chdemko.com
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * External Login - External Login plugin.
 *
 * @package     External Login
 * @subpackage  External Login Plugin
 *
 * @since  2.0.0
 */
class plgSystemExternallogin extends JPlugin
{
	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   2.0.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * After initialise event
	 *
	 * @return	void
	 *
	 * @since	2.0.0
	 */
	public function onAfterInitialise()
	{
		// Get the application
		$app = JFactory::getApplication();

		// Get the router
		$router = $app->getRouter();

		// attach build rules for language SEF
		$router->attachBuildRule(array($this, 'buildRule'));
	}

	/**
	 * Redirect to com_externallogin in case of login view
	 *
	 * @since	2.0.0
	 */
	public function buildRule(&$router, &$uri)
	{
		if (JFactory::getApplication()->isSite() && $uri->getVar('option') == 'com_users' && $uri->getVar('view') == 'login' && JPluginHelper::isEnabled('authentication', 'externallogin'))
		{
			$uri->setVar('option', 'com_externallogin');
		}
	}

	/**
	 * Remove server information about a user
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param	array    $user     Holds the user data
	 * @param	boolean  $success  True if user was succesfully stored in the database
	 * @param	string   $msg      Message
	 *
	 * @return	boolean
	 *
	 * @since	2.0.0
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success) {
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__externallogin_users')->where('user_id = ' . (int) $user['id']);
		$db->setQuery($query);
		$db->query();
		return true;
	}
}
