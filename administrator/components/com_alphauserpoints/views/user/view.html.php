<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.pagination' );

class alphauserpointsViewUser extends JViewLegacy {

	function _displaylist($tpl = null) {
		
		$document	=  JFactory::getDocument();
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_alphauserpoints/assets/images/icon-48-alphauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title(  $logo . 'AlphaUserPoints :: ' . JText::_( 'AUP_ACTIVITY' ) . ': ' . $this->name , 'searchtext' );
		getCpanelToolbar();
		JToolBarHelper::back( 'Back' );
		JToolBarHelper::divider();
		if (JFactory::getUser()->authorise('core.edit', 'com_alphauserpoints')) {
			JToolBarHelper::editList( 'edituserdetails' );
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_alphauserpoints')) {
			JToolBarHelper::custom( 'deleteuserdetails', 'trash.png', 'delete.png', JText::_('AUP_DELETE') );
			JToolBarHelper::custom( 'deleteuserallactivities', 'delete.png', 'delete.png', JText::_('AUP_DELETE_ALL'), false );

			JToolBarHelper::divider();
		
		}
		if (JFactory::getUser()->authorise('core.create', 'com_alphauserpoints')) {
			JToolBarHelper::custom( 'exportallactivitiesuser', 'upload.png', 'upload.png', JText::_('AUP_EXPORT_ACTIVITIES'), false );
		}
		JToolBarHelper::divider();
		$bar =  JToolBar::getInstance('toolbar');
		
		JHtml::_('bootstrap.modal', 'collapseModal');
		$title = JText::_('AUP_CUSTOM_POINTS');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#generatorModal\" class=\"btn btn-small\">
					<i class=\"icon-apply icon-white\" title=\"$title\"></i>
					$title</button>";
		$bar->appendButton('Custom', $dhtml, 'applycustom');
		
		//$bar->appendButton( 'Popup', 'apply', JText::_('AUP_CUSTOM_POINTS'), 'index.php?option=com_alphauserpoints&task=applycustom&layout=modal&tmpl=component&cid='.$this->cid.'&name='.$this->name, 800, 460, 0, 0, 'window.top.location.reload(true);document.location.reload(true);' );
		
		getPrefHelpToolbar();	
		
		$this->assignRef( 'userDetails', $this->userDetails );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef('pagination', $pagination );
		
		$this->assignRef('name', $this->name );
		
		parent::display( $tpl) ;
	}
	
	function _edit_pointsDetails () {
		
		$document	=  JFactory::getDocument();
		
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		
		JToolBarHelper::title( 'AlphaUserPoints :: ' . JText::_( 'AUP_ACTIVITY' ) . ': ' . $this->name, 'addedit' );
		getCpanelToolbar();
		if (JFactory::getUser()->authorise('core.edit.state', 'com_alphauserpoints')) {
			JToolBarHelper::save( 'saveuserdetails' );
		}
		JToolBarHelper::cancel( 'canceluserdetails' );
		getPrefHelpToolbar();
		
		JHTML::_('behavior.calendar');
		$document->addScriptDeclaration("window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });");

		$this->assignRef( 'row', $this->row );
		$this->assignRef('name', $this->name );
		$this->assignRef('rulename', $this->rulename );
		$this->assignRef('cid', $this->cid );
		
		parent::display( "form" ) ;
	
	}
}
?>