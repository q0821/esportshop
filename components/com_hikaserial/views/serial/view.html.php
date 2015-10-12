<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.9.1
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2015 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class serialViewSerial extends hikaserialView {

	public function display($tpl = null, $params = null) {
		$this->params =& $params;
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function check($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$serialClass = hikaserial::get('class.serial');
		$serials = null;

		$formData = JRequest::getVar('hikaserial', array(), '', 'array');
		if(!empty($formData['serial'])) {
			$serial_data = $formData['serial'];
			$pack = null;
			if(!empty($formData['pack_name']) && is_string($formData['pack_name']))
				$pack = $formData['pack_name'];
			if(!empty($formData['pack_id']))
				$pack = (int)$formData['pack_id'];
			$serials = $serialClass->check($serial_data, $pack);
		}
		$this->assignRef('serials', $serials);

		$format = 'html';
		if(!empty($formData['format'])) {
			$format = $formData['format'];
		}
		$this->assignRef('format', $format);
	}

	public function consume($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$consumed = JRequest::getVar('consumed_serial', null);
		$this->assignRef('consumed', $consumed);

		$confirmation = JRequest::getVar('confirmation', 0);
		$this->assignRef('confirmation', $confirmation);

		$formData = JRequest::getVar('hikaserial', array(), '', 'array');

		$serial_data = '';
		if(!empty($formData['data']))
			$serial_data = (string)$formData['data'];
		$this->assignRef('serial_data', $serial_data);

		$format = 'html';
		if(!empty($formData['format'])) {
			$format = $formData['format'];
		}
		$this->assignRef('format', $format);
	}

	public function consumed($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$serial = JRequest::getVar('consumed_serial', null);
		$this->assignRef('serial', $serial);

		$serial_object = JRequest::getVar('consumed_full_serial', null);
		$this->assignRef('serial_object', $serial_object);

		$formData = JRequest::getVar('hikaserial', array(), '', 'array');
		$format = 'html';
		if(!empty($formData['format'])) {
			$format = $formData['format'];
		}
		$this->assignRef('format', $format);
	}

	public function select() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaserial::config();
		$this->assignRef('config', $config);
		$shopConfig = hikaserial::config(false);
		$this->assignRef('shopConfig', $shopConfig);

		$serialStatusType = hikaserial::get('type.serial_status');
		$this->assignRef('serialStatusType', $serialStatusType);
		$packType = hikaserial::get('type.pack');
		$this->assignRef('packType', $packType);

		$filterType = $app->getUserStateFromRequest($this->paramBase.".filter_type", 'filter_type', 0, 'int');

		$singleSelection = JRequest::getVar('single', false);
		$this->assignRef('singleSelection', $singleSelection);
		$confirm = JRequest::getVar('confirm', true);
		$this->assignRef('confirm', $confirm);

		$elemStruct = array(
			'serial_data',
			'serial_id',
			'serial_pack_id',
			'serial_status',
			'pack_name'
		);
		$this->assignRef('elemStruct', $elemStruct);

		$cfg = array(
			'table' => 'serial',
			'main_key' => 'serial_id',
			'order_sql_value' => 'a.serial_id'
		);

		$manage = true; // TODO
		$this->assignRef('manage', $manage);
		$manage_shop_order = hikaserial::isAllowed($shopConfig->get('acl_order_manage', 'all'));
		$this->assignRef('manage_shop_order', $manage_shop_order);
		$manage_shop_user = hikaserial::isAllowed($shopConfig->get('acl_user_manage', 'all'));
		$this->assignRef('manage_shop_user', $manage_shop_user);

		$pageInfo = new stdClass();
		$filters = array();

		$pageInfo->filter = new stdClass();
		$pageInfo->filter->serial_status = $app->getUserStateFromRequest($this->paramBase.".filter_status", 'filter_status', '', 'string');
		$pageInfo->filter->pack = $app->getUserStateFromRequest($this->paramBase.".filter_pack", 'filter_pack', '', 'string');
		$pageInfo->filter->order = new stdClass();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.".filter_order", 'filter_order', $cfg['order_sql_value'], 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');

		$pageInfo->limit = new stdClass();
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		if(JRequest::getVar('search') != $app->getUserState($this->paramBase.".search")) {
			$app->setUserState($this->paramBase.'.limitstart',0);
			$pageInfo->limit->start = 0;
		} else {
			$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int' );
		}

		$pageInfo->search = JString::strtolower($app->getUserStateFromRequest($this->paramBase.".search", 'search', '', 'string'));
		$this->assignRef('pageInfo', $pageInfo);

		$filters = array();
		$searchMap = array(
			'a.serial_id',
			'a.serial_data',
			'a.serial_status',
			'b.pack_name',
			'd.username'
		);

		if(!empty($pageInfo->search)) {
			$searchVal = '\'%' . $db->getEscaped(JString::strtolower($pageInfo->search), true) . '%\'';
			$filters[] = '('.implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal.')';
		}
		if(!empty($pageInfo->filter->serial_status)) {
			$filters[] = ' a.serial_status = ' . $db->quote($pageInfo->filter->serial_status);
		}
		if(!empty($pageInfo->filter->pack)) {
			if((int)$pageInfo->filter->pack > 0) {
				$filters[] = ' b.pack_id = ' . (int)$pageInfo->filter->pack;
			} else {
				$filters[] = ' b.pack_name = ' . $db->quote($pageInfo->filter->pack);
			}
		}
		if(!empty($filters)) {
			$filters = ' WHERE '. implode(' AND ', $filters);
		} else {
			$filters = '';
		}

		$order = '';
		if(!empty($pageInfo->filter->order->value)) {
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$query = 'FROM '.hikaserial::table($cfg['table']).' AS a INNER JOIN '.
			hikaserial::table('pack') . ' AS b ON a.serial_pack_id = b.pack_id LEFT JOIN '.
			hikaserial::table('shop.user') . ' AS c ON a.serial_user_id = c.user_id LEFT JOIN '.
			hikaserial::table('users', false) . ' AS d ON c.user_cms_id = d.id LEFT JOIN '.
			hikaserial::table('shop.order') . ' AS e ON a.serial_order_id = e.order_id '.
			$filters.$order;
		$db->setQuery('SELECT * '.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);

		$rows = $db->loadObjectList();
		if(!empty($pageInfo->search)) {
			$rows = hikaserial::search($pageInfo->search, $rows, $cfg['main_key']);
		}
		$this->assignRef('rows',$rows);

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDisplaySerials', array(&$rows, 'back-serial-listing'));

		$db->setQuery('SELECT COUNT(*) '.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		if($pageInfo->limit->value == 500)
			$pageInfo->limit->value = 100;
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);
		$this->assignRef('pagination', $pagination);

		$doOrdering = !$filterType;
		$this->assignRef('doOrdering', $doOrdering);
		if($doOrdering) {
			$ordering = new stdClass();
			$ordering->ordering = false;
			$ordering->orderUp = 'orderup';
			$ordering->orderDown = 'orderdown';
			$ordering->reverse = false;
			if($pageInfo->filter->order->value == 'a.ordering') {
				$ordering->ordering = true;
				if($pageInfo->filter->order->dir == 'desc') {
					$ordering->orderUp = 'orderdown';
					$ordering->orderDown = 'orderup';
					$ordering->reverse = true;
				}
			}
			$this->assignRef('ordering', $ordering);
		}
	}

	public function useselection() {
		$serials = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';
		$confirm = JRequest::getVar('confirm', true);
		$singleSelection = JRequest::getVar('single', false);

		$elemStruct = array(
			'serial_data',
			'pack_id',
			'pack_name',
			'pack_data',
			'pack_generator'
		);

		if(!empty($serials)) {
			JArrayHelper::toInteger($users);
			$db = JFactory::getDBO();
			$query = 'SELECT a.* FROM '.hikaserial::table('serial').' AS a INNER JOIN '.hikaserial::table('pack').' AS b ON a.serial_pack_id = b.pack_id WHERE a.serial_id IN ('.implode(',',$serials).')';
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!empty($rows)) {
				$data = array();
				foreach($rows as $v) {
					$d = '{id:'.$v->pack_id;
					foreach($elemStruct as $s) {
						if($s == 'id')
							continue;
						$d .= ','.$s.':\''. str_replace('"','\'',$v->$s).'\'';
					}
					$data[] = $d.'}';
				}
				if(!$singleSelection)
					$data = '['.implode(',',$data).']';
				else {
					$data = $data[0];
					$rows = $rows[0];
				}
			}
		}
		$this->assignRef('rows', $rows);
		$this->assignRef('data', $data);
		$this->assignRef('confirm', $confirm);
		$this->assignRef('singleSelection', $singleSelection);

		if($confirm == true) {
			if(!HIKASHOP_J30)
				JHTML::_('behavior.mootools');
			else
				JHTML::_('behavior.framework');
			$js = 'window.addEvent("domready", function(){window.top.hikaserial.submitBox('.$data.');});';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}
	}
}
