<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
jimport('joomla.plugin.plugin');

class alphauserpointsModelUser extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	

	function _load_details_user() {
		
		$app = JFactory::getApplication();
		
		$option = JFactory::getApplication()->input->get('option', '', 'cmd');
		 
		$db			    = JFactory::getDBO();
		
		$referreuserid		= JFactory::getApplication()->input->get( 'cid', '' );
		
		if ( !$referreuserid ) $referreuserid = JFactory::getApplication()->input->get( 'c2id', '' );
		
		$orderby = " ORDER BY a.insert_date DESC";
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_alphauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		
		$where = " AND a.referreid='$referreuserid' ";
		$query  = "SELECT a.*, r.rule_name FROM #__alpha_userpoints_details AS a, #__alpha_userpoints_rules AS r WHERE a.rule=r.id " . $where . $orderby;
		$total  = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		
		$lists['order_Dir']	= '';
		$lists['order']		= '';
		$lists['search']    = '';
		
		return array($result, $total, $limit, $limitstart, $lists);
		
	}
	
	function _load_details_user_edit_user($cid) {
		
		$app = JFactory::getApplication();
		
		$option = JFactory::getApplication()->input->get('option', '');
		 
		$db			    = JFactory::getDBO();

		$query = "SELECT referreid FROM #__alpha_userpoints WHERE `id`='".$cid."'";
		$db->setQuery( $query );
		$referreuserid = $db->loadResult();		
		
		if ( !$referreuserid ) $referreuserid = JFactory::getApplication()->input->get( 'c2id', '' );
		
		$orderby = " ORDER BY a.insert_date DESC";
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_alphauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		
		$where = " AND a.referreid='$referreuserid' ";
		$query  = "SELECT a.*, r.rule_name FROM #__alpha_userpoints_details AS a, #__alpha_userpoints_rules AS r WHERE a.rule=r.id " . $where . $orderby;
		$total  = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		
		$lists['order_Dir']	= '';
		$lists['order']		= '';
		$lists['search']    = '';
		
		return array($result, $total, $limit, $limitstart, $lists);
		
	}	

	
	function _edit_pointsDetails() {
	
		$db     = JFactory::getDBO();

		$cid 	= JFactory::getApplication()->input->get('cid', array(0), 'array');
		$option = JFactory::getApplication()->input->get('option', '');
		
		if (!is_array( $cid )) {
			$cid = array(0);
		}

		$lists = array();

		$row = JTable::getInstance('userspointsdetails');
		$row->load( $cid[0] );
		
		return $row;
		
	}
	
	function _get_rule_name ( $id ) {
	
		$db     = JFactory::getDBO();
		
		$query  = "SELECT rule_name FROM #__alpha_userpoints_rules WHERE id='" . $id . "'";
		$db->setQuery( $query );
		$rulename = $db->loadResult();

		return $rulename;	
	
	}
	
	function _save_user_details() {
		$app = JFactory::getApplication();

		// initialize variables
		$db = JFactory::getDBO();
		$post	= JRequest::get( 'post' );
		$row = JTable::getInstance('userspointsdetails');

		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$this->checkNewTotal( $post['referreid'], $post['rule'] );

		$msg = JText::_( 'AUP_DETAILSSAVED' );
		$redirect = 'index.php?option=com_alphauserpoints&task=' . $post['redirect'];
		JControllerLegacy::setRedirect($redirect, $msg);
		JControllerLegacy::redirect();
	}
	
	function _delete_user_details () {
		$app = JFactory::getApplication();

		// initialize variables
		$db			= JFactory::getDBO();
		$cid		= JFactory::getApplication()->input->get('cid', array(), 'array');
		$c2id		= JFactory::getApplication()->input->get('c2id', '', 'string');
		
		$post	= JRequest::get( 'post' );
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('alphauserpoints');
		$results = $dispatcher->trigger( 'onBeforeDeleteUserActivityAlphaUserPoints', array($cid, $c2id) );  // c2id = referreid

		
		//$msgType	= '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {		
			
			$msg = JText::_('AUP_SUCCESSFULLYDELETED');
		
			$query = "DELETE FROM #__alpha_userpoints_details"
					. "\n WHERE (`id` = " . implode(' OR `id` = ', $cid) . ")"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
			
			$results = $dispatcher->trigger( 'onAfterDeleteUserActivityAlphaUserPoints', array($c2id) );  // c2id = referreid

		}
		
		// recalculate for this user 
		$this->checkNewTotal( $c2id );
				
		$app->enqueueMessage( $msg );
		
		$redirect = 'index.php?option=com_alphauserpoints&task=' . $post['redirect'];
		JControllerLegacy::setRedirect($redirect);
		JControllerLegacy::redirect();	
	}
	
	function _delete_user_all_activities() {
	
		$app = JFactory::getApplication();

		// initialize variables
		$db			= JFactory::getDBO();
		$c2id		= JFactory::getApplication()->input->get('c2id', '', 'string');
		
		$post	= JRequest::get( 'post' );
		
		//$msgType	= '';
		
		//JArrayHelper::toInteger($cid);
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('alphauserpoints');
		$results = $dispatcher->trigger( 'onBeforeDeleteAllUserActivitiesAlphaUserPoints', array($c2id) );  // c2id = referreid

		
		if ($c2id) {		
			
			$msg = JText::_('AUP_SUCCESSFULLYDELETED');
		
			$query = "DELETE FROM #__alpha_userpoints_details"
					. "\n WHERE `referreid` = '" .$c2id . "'"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
			
			$results = $dispatcher->trigger( 'onAfterDeleteAllUserActivitiesAlphaUserPoints', array($c2id) );  // c2id = referreid

		}
		
		// recalculate for this user 
		$this->checkNewTotal( $c2id );
				
		$app->enqueueMessage( $msg );		
		
		$redirect = 'index.php?option=com_alphauserpoints&task=' . $post['redirect'];
		JControllerLegacy::setRedirect($redirect);
		JControllerLegacy::redirect();	
	
	}
	
	function checkNewTotal( $referrerid, $rule_id=0 )
	{
		$db			= JFactory::getDBO();
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');		
		
		// recalculate for this user 
		$query = "SELECT SUM(points) FROM #__alpha_userpoints_details WHERE `referreid`='" . $referrerid . "' AND `approved`='1' AND (`expire_date`>'$now' OR `expire_date`='0000-00-00 00:00:00')";
		$db->setQuery($query);
		$newtotal = $db->loadResult();

		$query = "UPDATE #__alpha_userpoints SET `points`='" . $newtotal . "', `last_update`='$now' WHERE `referreid`='" . $referrerid . "'";
		$db->setQuery( $query );
		$db->query();
		
		// update Ranks / Medals if necessary		
		AlphaUserPointsHelper::checkRankMedal ( $referrerid, $rule_id );
	
	}
	
}
?>