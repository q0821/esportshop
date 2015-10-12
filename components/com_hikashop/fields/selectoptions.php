<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2015 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class JFormFieldSelectoptions extends JFormField {

	protected $type = 'selectoptions';

	protected function getInput() {
		if(!defined('DS'))
			define('DS', DIRECTORY_SEPARATOR);
		if(!function_exists('hikashop_config') && !include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')){
			return 'This menu options cannot be displayed without the Hikashop Component';
		}

		$id = JRequest::getInt('id');
		if(HIKASHOP_J30){
			$empty='';
			jimport('joomla.html.parameter');
			$params = new HikaParameter($empty);
			$js = '';
			$params->set('id',$this->id);
			$params->set('name',$this->name);
			$params->set('value',$this->value);
			$params->set('type',$this->getAttribute('content'));
			$params->set('menu',$this->getAttribute('menu'));
			$content = hikashop_getLayout('menus','options',$params,$js);
			$text = '</div></div>'.$content.'<div><div>';
		}elseif(!empty($id)){
			$config =& hikashop_config();
			if(!hikashop_isAllowed($config->get('acl_menus_manage','all'))){
				return 'Access to the HikaShop options of the menus is restricted';
			}
			$text = '<a title="'.JText::_('HIKASHOP_OPTIONS').'"  href="'.JRoute::_('index.php?option=com_hikashop&ctrl=menus&fromjoomla=1&task=edit&cid[]='.$id).'" >'.JText::_('HIKASHOP_OPTIONS').'</a>';
		}else{
			$text = JText::_('HIKASHOP_OPTIONS_EDIT');
		}
		return $text;
	}
}
