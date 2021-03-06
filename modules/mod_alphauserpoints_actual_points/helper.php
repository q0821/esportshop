<?php
/**
* @version		$Id: mod_alphauserpoints_actual_points.php 2015-August-03 v2.0.2 $
* @package		AlphaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2008-2015. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAlphaUserPointsActualPointsHelper {

	public static function getPoints($params) 
	{

		$db		= JFactory::getDBO();
		
		$points = 0;
		
		$user 	= JFactory::getUser();  
		$userid = $user->id;  
		
		if ( $userid ) 
		{		
			$query = "SELECT points"
				   . " FROM #__alpha_userpoints"
				   . " WHERE userid='".$userid."'"
				   ;	
			$db->setQuery($query);
			$points = $db->loadResult();			
		}
	
		return $points;	
	}
}
?>