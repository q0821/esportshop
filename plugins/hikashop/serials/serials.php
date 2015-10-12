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
class plgHikashopSerials extends JPlugin {
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	private function init() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKASERIAL_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKASERIAL_COMPONENT');
			}
		}
		return $init;
	}

	public function onProductFormDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		if(!empty($product->product_id)) {
			$params->set('product_id', $product->product_id);
		} else {
			$params->set('product_id', 0);
		}
		$js = '';
		$ret = hikaserial::getLayout('productserial', 'shop_form', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onProductBlocksDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		if(!empty($product->product_id)) {
			$params->set('product_id', $product->product_id);
		} else {
			$params->set('product_id', 0);
		}
		$js = '';
		$ret = hikaserial::getLayout('productserial', 'shop_block', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onMarketProductBlocksDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		if(!defined('HIKAMARKET_COMPONENT'))
			return;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0)) return;
		if(!hikamarket::acl('product_edit_plugin_hikaserial')) return;

		$params = new HikaParameter('');
		if(!empty($product->product_id)) {
			$params->set('product_id', $product->product_id);
		} else {
			$params->set('product_id', 0);
		}
		$js = '';
		$ret = hikaserial::getLayout('productserial', 'market_block', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onMarketAclPluginListing(&$categories) {
		$categories['product'][] = 'hikaserial';
	}

	public function onAfterProductCreate(&$product) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() & !hikaserial::initMarket())
			return;

		$class = hikaserial::get('class.product');
		$class->saveForm($product);
	}

	public function onAfterProductUpdate(&$product) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() & !hikaserial::initMarket())
			return;

		$class = hikaserial::get('class.product');
		$class->saveForm($product);
	}

	public function onBeforeOrderCreate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->preUpdate($order);
	}

	public function onAfterOrderCreate(&$order, &$send_email) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->postUpdate($order);
	}

	public function onBeforeOrderUpdate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->preUpdate($order);
	}

	public function onAfterOrderUpdate(&$order, &$send_email) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->postUpdate($order);
	}

	public function onAfterOrderProductsListingDisplay(&$order, $type) {
		if(!$this->init())
			return;

		$types = array(
			'order_back_show', 'order_back_invoice', 'order_front_show', 'email_notification_html',
			'order_frontvendor_show', 'order_frontvendor_invoice'
		);

		if(in_array($type, $types)) {
			$params = new HikaParameter('');
			if(isset($order->order_id)) {
				$params->set('order_id', (int)$order->order_id);
			} else {
				$params->set('order_id', (int)$order->products[0]->order_id);
			}
			$params->set('order_obj', $order);
			$js = '';
			echo hikaserial::getLayout('orderserial', 'show_'.$type, $params, $js);
		}
	}

	public function onBeforeMailSend(&$mail, &$mailer) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.mail');
		$class->beforeMailSend($mail, $mailer);
	}

	public function onBeforeDownloadFile(&$filename, &$do, &$file) {
		if(!$this->init())
			return;
		$downloadClass = hikaserial::get('class.download');
		if(!empty($downloadClass))
			$downloadClass->beforeDownloadFile($filename, $do, $file);
	}

	public function onBeforeOrderExport(&$rows, &$view) {
		if(!$this->init())
			return;
		$orderClass = hikaserial::get('class.order');
		$orderClass->beforeOrderExport($rows, $view);
	}

	public function onViewsListingFilter(&$views, $client) {
		if(!$this->init())
			return;

		switch($client){
			case 0:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_FRONT.'views'.DS
				);
				break;
			case 1:
				$views[] = array(
					'client_id' => 1,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_BACK.'views'.DS
				);
				break;
			default:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_FRONT.'views'.DS
				);
				$views[] = array(
					'client_id' => 1,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_BACK.'views'.DS
				);
				break;
		}
	}

	public function onHikashopBeforeDisplayView(&$viewObj) {
		$app = JFactory::getApplication();

		$viewName = $viewObj->getName();

		if($viewName == 'menu') {
			if(!$app->isAdmin() || !$this->init())
				return;
			$class = hikaserial::get('class.menu');
			$class->processView($viewObj);
			return;
		}
	}

	public function onMailListing(&$files) {
		if(!$this->init())
			return;

		jimport('joomla.filesystem.folder');
		$emailFiles = JFolder::files(HIKASERIAL_MEDIA.'mail'.DS, '^([-_A-Za-z]*)(\.html)?\.php$');
		if(empty($emailFiles))
			return;
		foreach($emailFiles as $emailFile) {
			$file = str_replace(array('.html.php', '.php'), '', $emailFile);
			if(substr($file, -9) == '.modified')
				continue;
			$key = strtoupper($file);
			$files[] = array(
				'folder' => HIKASERIAL_MEDIA.'mail'.DS,
				'name' => JText::_('SERIAL_' . $key),
				'filename' => $file,
				'file' => 'serial.'.$file
			);
		}
	}

	public function onCheckoutStepList(&$list) {
		$list['plg.serial.coupon'] = 'HikaSerial ' . JText::_('HIKASHOP_COUPON');
	}

	public function onCheckoutStepDisplay($layoutName, &$html, &$view) {
		if($layoutName == 'plg.serial.coupon') {
			if(!$this->init())
				return;
			$params = new stdClass();
			$params->view = $view;
			$js = null;
			$html .= hikaserial::getLayout('checkoutserial', 'coupon', $params, $js);
		}
	}

	public function onBeforeCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
	}

	public function onAfterCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
		if($controllerName == 'plg.serial.coupon') {
			if(!$this->init())
				return;

			$checkoutClass = hikaserial::get('class.checkout');
			$checkoutClass->afterCheckoutStep($controllerName, $go_back, $original_go_back, $controller);
		}
	}
}
