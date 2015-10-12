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
class hikaserialProductClass extends hikaserialClass {

	public function saveForm(&$product) {
		if(!isset($product->product_id))
			return;
		$product_id = (int)$product->product_id;

		$app = JFactory::getApplication();
		if(!$app->isAdmin()) {
			if(!hikaserial::initMarket())
				return;

			if(!hikamarket::acl('product_edit_plugin_hikaserial'))
				return;
		}

		$formData = JRequest::getVar('data', array(), '', 'array');
		if(empty($formData) || empty($formData['hikaserial']['form']))
			return;

		$serialData = $formData['hikaserial'];

		if(!$app->isAdmin()) {
			if(isset($serialData[$product_id]))
				$serialData = $serialData[$product_id];
			else
				$serialData = array();
		}

		$packs = array();
		if(isset($serialData['pack_qty']) && isset($serialData['pack_id'])) {
			$packs = array_combine($serialData['pack_id'], $serialData['pack_qty']);
		}

		$query = 'DELETE FROM ' . hikaserial::table('product_pack') . ' WHERE product_id = ' . $product_id;
		$this->db->setQuery($query);
		$this->db->query();

		if(!empty($packs)) {
			$query = 'INSERT IGNORE INTO ' . hikaserial::table('product_pack') . ' (`product_id`, `pack_id`, `quantity`) VALUES ';
			$data = array();
			foreach($packs as $id => $qty) {
				if((int)$qty > 0) {
					$data[] = '(' . $product_id . ', ' . (int)$id . ', ' .(int)$qty . ')';
				}
				if(count($data) >= 50) {
					$this->db->setQuery($query . implode(',', $data));
					$this->db->query();
					$data = array();
				}
			}
			if(count($data) > 0) {
				$this->db->setQuery($query . implode(',', $data));
				$this->db->query();
				$data = array();
			}
		}
		$this->refreshQuantity($product);
	}

	public function refreshQuantities() {
		$null = null;
		return $this->refreshQuantity($null);
	}

	public function refreshQuantity(&$product) {
		$config = hikaserial::config();
		if($config->get('link_product_quantity', false) == false)
			return;

		$filter = '';
		if($product !== null) {
			$filter = 'AND pp.product_id = ' . $product->product_id . ' ';
		}

		$query = 'SELECT p.product_id, pa.pack_id, floor(count(s.serial_id) / pp.quantity) as qty, pa.pack_generator, pa.pack_params, p.product_quantity ' .
			'FROM ' . hikaserial::table('product_pack') . ' AS pp ' .
			'INNER JOIN ' . hikaserial::table('shop.product') . ' AS p ON pp.product_id = p.product_id ' .
			'INNER JOIN ' . hikaserial::table('pack') . ' AS pa ON pp.pack_id = pa.pack_id ' .
			'LEFT JOIN ' . hikaserial::table('serial') . ' AS s ON s.serial_pack_id = pa.pack_id AND s.serial_status = \'free\' ' .
			'WHERE pp.quantity > 0 ' . $filter .
			'GROUP BY p.product_id, pa.pack_id ' .
			'ORDER BY p.product_id ASC, qty ASC';

		$this->db->setQuery($query);
		$ret = $this->db->loadObjectList();

		$products = array();
		foreach($ret as &$p) {
			if(isset($products[$p->product_id]) && isset($products[$p->product_id]->qty) && $products[$p->product_id]->qty >= 0)
				continue;

			if(!empty($p->pack_generator)) {
				$p->qty = -1;
			}

			if(!empty($p->pack_params)) {
				$p->pack_params = unserialize($p->pack_params);
				if(!empty($p->pack_params->unlimited_quantity))
					$p->qty = -1;
			}

			$products[$p->product_id] =& $p;

			unset($p);
		}
		foreach($products as $p) {
			if($p->qty != $p->product_quantity) {
				$this->db->setQuery('UPDATE ' . hikaserial::table('shop.product') . ' SET product_quantity = ' . $p->qty . ' WHERE product_id = ' . $p->product_id);
				$this->db->query();
			}
		}
		unset($products);
		unset($ret);
	}
}
