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
class orderserialController extends hikaserialController {
	protected $type = 'orderserial';
	protected $rights = array(
			'display' => array('display','show','refresh'),
			'add' => array(),
			'edit' => array(),
			'modify' => array(),
			'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
	}

	public function show() {
		JRequest::setVar('layout', 'show_order_back_show');

		$tmpl = JRequest::getString('tmpl', '');
		if($tmpl === 'component') {
			ob_end_clean();
			parent::display();
			exit;
		}
		return parent::display();
	}

	public function refresh(){
		$app = JFactory::getApplication();
		$orderClass = hikaserial::get('class.order');
		$orderId = hikaserial::getCID('order_id');
		if(empty($orderId)){
			$app->redirect(hikaserial::completeLink('dashboard'));
		}
		$orderClass->refresh($orderId);
		$app->redirect(hikaserial::completeLink('shop.order&task=edit&cid[]='.$orderId, false, true));
	}
}
