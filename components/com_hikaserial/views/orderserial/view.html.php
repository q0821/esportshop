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
class orderserialViewOrderserial extends hikaserialView {

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function show($params = null, $viewName = 'email-notification') {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$data = null;
		$order_id = 0;

		if(!empty($params)) {
			$order_id = (int)$params->get('order_id');
		}

		$display_serial_statuses = $config->get('display_serial_statuses','');
		if(empty($display_serial_statuses)) {
			$display_serial_statuses = array($config->get('used_serial_status','used'));
		} else {
			$display_serial_statuses = explode(',', $display_serial_statuses);
		}
		foreach($display_serial_statuses as &$s) {
			$s = $db->Quote($s);
		}
		unset($s);

		if($order_id > 0) {
			$query = 'SELECT a.*, b.*, c.* FROM '.
				hikaserial::table('serial') . ' as a '.
				'INNER JOIN '. hikaserial::table('pack') . ' as b ON a.serial_pack_id = b.pack_id '.
				'LEFT JOIN ' . hikaserial::table('shop.order_product') . ' as c ON a.serial_order_product_id = c.order_product_id '.
				'WHERE a.serial_status IN ('.implode(',',$display_serial_statuses).') AND a.serial_order_id = ' . $order_id;
			$db->setQuery($query);
			$data = $db->loadObjectList();
		}

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDisplaySerials', array(&$data, $viewName));

		if(!empty($data)) {
			foreach($data as &$serial) {
				if(!isset($serial->serial_text_data)) {
					$serial->serial_text_data = $serial->serial_data;
					$serial->serial_data = str_replace(array("\r\n","\r","\n"), '<br/>', $serial->serial_data);
				}
				unset($serial);
			}
		}

		$this->assignRef('data', $data);
		$this->assignRef('order_id', $order_id);
	}

	public function show_order_front_show($params = null) {
		$this->show($params, 'front-order-show');
	}

	public function show_order_frontvendor_show($params = null) {
		$this->show($params, 'front-order-show');
	}

	public function show_order_frontvendor_invoice($params = null) {
		$this->show($params, 'front-order-invoice');
	}

	public function show_email_notification_html($params = null) {
		$this->show($params, 'email-notification');
	}
}
