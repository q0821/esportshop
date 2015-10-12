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
class hikaserialDownloadClass extends hikaserialClass {

	protected function getOrderFileSerials(&$file) {
		$config = hikaserial::config();
		$display_serial_statuses = $config->get('display_serial_statuses','');
		if(empty($display_serial_statuses)) {
			$display_serial_statuses = explode(',', $config->get('used_serial_status','assigned,used'));
		} else {
			$display_serial_statuses = explode(',', $display_serial_statuses);
		}
		$statuses = array();
		foreach($display_serial_statuses as $s) {
			$statuses[] = $this->db->Quote($s);
		}

		$app = JFactory::getApplication();
		if(empty($file->order_id) && $app->isAdmin())
			$file->order_id = JRequest::getInt('order_id', 0);

		$serials = array();
		if(!empty($file->order_id)) {
			$query = 'SELECT s.*, p.*, op.product_id '.
				' FROM ' . hikaserial::table('serial') . ' AS s '.
				' INNER JOIN ' . hikaserial::table('pack') . ' AS p ON s.serial_pack_id = p.pack_id '.
				' LEFT JOIN ' . hikaserial::table('shop.order_product') . ' AS op ON op.order_product_id = s.serial_order_product_id AND op.order_id = s.serial_order_id '.
				' LEFT JOIN ' . hikaserial::table('shop.product') . ' AS product ON product.product_id = op.product_id '.
				' WHERE s.serial_status IN ('.implode(',',$statuses).') AND s.serial_order_id = '. $file->order_id . ' AND (product.product_id = ' . $file->file_ref_id . ' OR product.product_parent_id = ' . $file->file_ref_id . ' OR op.product_id IS NULL) ' .
				' ORDER BY s.serial_id';
			$this->db->setQuery($query);
			$serials = $this->db->loadObjectList();
		}
		return $serials;
	}

	public function beforeDownloadFile(&$filename, &$do, &$file) {
		$serials = $this->getOrderFileSerials($file);

		$f12 = '';
		if(strlen($filename) >= 12)
			$f12 = substr($filename, 0, 12);
		if( ($f12 == '@hikaserial:' || $f12 == '#hikaserial:') || !empty($serials)) {
			JPluginHelper::importPlugin('hikaserial');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onBeforeSerialDownloadFile', array(&$filename, &$do, &$file, &$serials));

			if(strlen($filename) >= 11 && ($f12 == '@hikaserial:' || $f12 == '#hikaserial:')) {
				$do = false;
			}
			return;
		}
	}
	public function downloadHikaShopFile($file_id, $order_id = 0, $file_pos = 1, $serial = '') {
		$app = JFactory::getApplication();
		$fileClass = hikaserial::get('shop.class.file');
		$file = $fileClass->get($file_id);
		$file_pos = (int)$file_pos;
		if($file_pos <= 0)
			$file_pos = 1;

		if(!$app->isAdmin() && empty($file->file_free_download)) {

			$orderClass = hikaserial::get('shop.class.order');
			$order = $orderClass->get($order_id);
			$file->order = $order;
			$file->order_id = $order_id;

			if(empty($order) || $order->order_type != 'sale') {
				$app->enqueueMessage(JText::_('WRONG_ORDER'));
				return 'wrong_order';
			}

			$shopConfig = hikaserial::config(false);
			$order_status_for_download = $shopConfig->get('order_status_for_download', 'confirmed,shipped');
			if(!in_array($order->order_status, explode(',', $order_status_for_download))) {
				$app->enqueueMessage(JText::_('BECAUSE_STATUS_NO_DOWNLOAD'));
				return 'status';
			}

			$download_time_limit = $shopConfig->get('download_time_limit',0);
			if(!empty($download_time_limit) && ($download_time_limit + $order->order_created) < time()) {
				$app->enqueueMessage(JText::_('TOO_LATE_NO_DOWNLOAD'));
				return 'date';
			}

			$query = 'SELECT a.* FROM '.hikaserial::table('shop.order_product').' AS a WHERE a.order_id = ' . $order_id;
			$this->db->setQuery($query);
			$order->products = $this->db->loadObjectList();
			$product_ids = array();
			foreach($order->products as $product) {
				if((int)$product->order_product_quantity >= $file_pos || $file_pos == 1)
					$product_ids[] = $product->product_id;
			}
			if(empty($product_ids)) {
				$app->enqueueMessage(JText::_('INVALID_FILE_NUMBER'));
				return 'status';
			}

			$query = 'SELECT * FROM '.hikaserial::table('shop.product').' WHERE product_id IN ('.implode(',',$product_ids).') AND product_type=\'variant\'';
			$this->db->setQuery($query);
			$products = $this->db->loadObjectList();
			if(!empty($products)) {
				foreach($products as $product) {
					foreach($order->products as $item) {
						if($product->product_id == $item->product_id && !empty($product->product_parent_id)) {
							$item->product_parent_id = $product->product_parent_id;
							$product_ids[] = $product->product_parent_id;
						}
					}
				}
			}

			$filters = array(
				'a.file_ref_id IN ('.implode(',',$product_ids).')',
				'a.file_type=\'file\'',
				'a.file_id='.$file_id
			);

			if(substr($file->file_path, 0, 1) == '@' || substr($file->file_path, 0, 1) == '#') {
				$query = 'SELECT a.*,b.* FROM '.hikaserial::table('shop.file').' AS a '.
					' LEFT JOIN '.hikaserial::table('shop.download').' AS b ON b.order_id='.$order->order_id.' AND a.file_id = b.file_id AND b.file_pos = '.$file_pos.
					' WHERE '.implode(' AND ',$filters);
			} else {
				$query = 'SELECT a.*, b.*, c.order_product_quantity FROM '.hikaserial::table('shop.file').' AS a '.
					' LEFT JOIN '.hikaserial::table('shop.download').' AS b ON b.order_id='.$order->order_id.' AND a.file_id = b.file_id '.
					' LEFT JOIN '.hikaserial::table('shop.order_product').' AS c ON c.order_id='.$order->order_id.' AND c.product_id = a.file_ref_id '.
					' WHERE '.implode(' AND ',$filters);
			}

			$this->db->setQuery($query);
			$fileData = $this->db->loadObject();
			if(!empty($fileData)) {
				if(!empty($file->file_limit) && (int)$file->file_limit != 0)
					$download_number_limit = (int)$file->file_limit;
				else
					$download_number_limit = $shopConfig->get('download_number_limit',0);

				if($download_number_limit < 0)
					$download_number_limit = 0;

				if(isset($fileData->order_product_quantity) && (int)$fileData->order_product_quantity > 0)
					$download_number_limit *= (int)$fileData->order_product_quantity;

				if(!empty($download_number_limit) && $download_number_limit <= $fileData->download_number) {
					$app->enqueueMessage(JText::_('MAX_REACHED_NO_DOWNLOAD'));
					return 'limit';
				}
			} else {
				$app->enqueueMessage(JText::_('FILE_NOT_FOUND'));
				return 'no_file';
			}

			$serials = $this->getOrderFileSerials($file);
			if(empty($serials)) {
				return 'no_serial';
			}
			$f = false;
			foreach($serials as $s) {
				if($s->serial_data == $serial) {
					$f = true;
					break;
				}
			}
			if(!$f)
				return 'wrong_serial';
		}

		if(!empty($file)) {
			$path = $fileClass->getPath('file');
			if(substr($file->file_path,0,7) == 'http://' || substr($file->file_path,0,1) == '@' || substr($file->file_path,0,1) == '#' || file_exists($path.$file->file_path) || file_exists($file->file_path)) {
				if(!$app->isAdmin()){
					if(!empty($file->file_free_download))
						$order_id = 0;

					$query = 'SELECT * FROM '.hikaserial::table('shop.download').' WHERE file_id='.$file->file_id.' AND order_id='.$order_id.' AND file_pos='.$file_pos;
					$this->db->setQuery($query);
					$download = $this->db->loadObject();

					if(empty($download))
						$query = 'INSERT INTO '.hikaserial::table('shop.download').' (file_id,order_id,download_number,file_pos) VALUES('.$file->file_id.','.$order_id.',1,'.$file_pos.');';
					else
						$query = 'UPDATE '.hikaserial::table('shop.download').' SET download_number=download_number+1 WHERE file_id='.$file->file_id.' AND order_id='.$order_id.' AND file_pos='.$file_pos;
					$this->db->setQuery($query);
					$this->db->query();
				}
				$file->order_id = (int)$order_id;
				$file->file_pos = $file_pos;
				$fileClass->sendFile($file, true, $path);
			}
		}
		$app->enqueueMessage(JText::_('FILE_NOT_FOUND'));
		return false;
	}
}
