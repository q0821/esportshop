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
class hikaserialPackClass extends hikaserialClass {

	protected $tables = array('pack');
	protected $pkeys = array('pack_id');
	protected $toggle = array('pack_published' => 'pack_id');

	public function get($element, $default = null) {
		$ret = parent::get($element, $default);
		if(!empty($ret->pack_params))
			$ret->pack_params = unserialize($ret->pack_params);

		return $ret;
	}

	public function save(&$element) {
		if(!isset($element->pack_params)) {
			$element->pack_params = null;
		}
		$unserializedParams = $element->pack_params;
		$element->pack_params = serialize($element->pack_params);

		$ret = parent::save($element);

		$element->pack_params = $unserializedParams;

		return $ret;
	}

	public function saveForm() {
		$pack = new stdClass();
		$pack->pack_id = hikaserial::getCID('pack_id');
		$formData = JRequest::getVar('data', array(), '', 'array');
		foreach($formData['pack'] as $col => $val) {
			hikaserial::secureField($col);
			$pack->$col = strip_tags($val);
		}
		$pack->pack_params = null;
		if(!empty($formData['pack_params'])) {
			$pack->pack_params = new stdClass();
			foreach($formData['pack_params'] as $k => $v) {
				hikaserial::secureField($k);
				$pack->pack_params->$k = $v;
			}
		}
		$pack->pack_description = JRequest::getVar('pack_description', '', '', 'string', JREQUEST_ALLOWRAW);

		$status = $this->save($pack);

		if($status) {

		}
		return $status;
	}

	public function delete($elements) {
		if(!is_array($elements)) {
			$elements = array($elements);
		}
		JArrayHelper::toInteger($elements);

		$query = 'SELECT serial_pack_id, count(*) as `cpt` FROM '.hikaserial::table('serial').' WHERE serial_pack_id IN ( '.implode(',',$elements).');';
		$this->db->setQuery($query);
		$serialPacks = $this->db->loadObjectList();
		$exclude = array();
		foreach($serialPacks as $serialPack) {
			if($serialPack->cpt > 0) {
				$exclude[] = $serialPack->serial_pack_id;
			}
		}
		if(!empty($exclude)) {
			$elements = array_diff($elements, $exclude);
		}
		return parent::delete($elements);
	}

	public function checkQuantity(&$pack) {
		if(empty($pack->pack_params->stock_level_notify) || $pack->pack_params->stock_level_notify <= 0)
			return true;

		$status = 'free';
		$query = 'SELECT count(*) as qty FROM ' . hikaserial::table('serial') . ' AS a WHERE a.serial_status = '.$this->db->Quote($status).' AND a.serial_pack_id='.$pack->pack_id;
		$this->db->setQuery($query);
		$pack->current_quantity = $this->db->loadResult();

		if(!empty($pack->pack_params->stock_level_notify) && (int)$pack->current_quantity <= (int)$pack->pack_params->stock_level_notify) {
			$mailClass = hikaserial::get('class.mail');
			$mail = $mailClass->load('pack_quantity_low', $pack);

			if(!empty($mail)) {
				$mail->subject = JText::sprintf($mail->subject, HIKASERIAL_LIVE);
				$shopConfig =& hikaserial::config(false);
				if(!empty($pack->email))
					$mail->dst_email = $pack->email;
				else
					$mail->dst_email = $shopConfig->get('from_email');

				if(!empty($pack->name))
					$mail->dst_name = $pack->name;
				else
					$mail->dst_name = $shopConfig->get('from_name');

				$mailClass->sendMail($mail);
			}
		}
		unset($pack->current_quantity);
		return true;
	}
}
