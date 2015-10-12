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
class plgHikaserialSerialperorder extends hikaserialPlugin {

	protected $type = 'plugin';
	protected $multiple = true;
	protected $doc_form = 'serialperorder-';

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function configurationHead() {
		return array(
			'pack' => array(
				'title' => JText::_('SERIAL_PACK'),
				'cell' => 'width="30%"'
			)
		);
	}

	public function configurationLine($id = 0, $conf = null) {
		if(empty($this->toggleHelper))
			$this->toggleHelper = hikaserial::get('helper.toggle');

		switch($id) {
			case 'pack':
				if(empty($this->packs)) {
					$db = JFactory::getDBO();
					$db->setQuery('SELECT * FROM '.hikaserial::table('pack'));
					$this->packs = $db->loadObjectList('pack_id');
				}
				$ret = array();
				if(!empty($conf->plugin_params->pack_id) && isset($this->packs[(int)$conf->plugin_params->pack_id]))
					return '<a href="'.hikaserial::completeLink('pack&task=edit&cid='.(int)$conf->plugin_params->pack_id).'">'.$this->packs[(int)$conf->plugin_params->pack_id]->pack_name.'</a>';
		}
		return null;
	}

	public function onPluginConfigurationSave(&$element) {
		if(empty($element->plugin_params->quantity) || (int)$element->plugin_params->quantity == 0)
			$element->plugin_params->quantity = 1;
	}

	public function onSerialOrderPreUpdate($new, &$order, &$order_serial_params) {
		if(!$new)
			return;

		$ids = array();
		parent::listPlugins('serialperorder', $ids, false);
		foreach($ids as $id) {
			parent::pluginParams($id);

			if((int)$this->plugin_params->quantity == 0 || (int)$this->plugin_params->pack_id == 0)
				continue;

			if(empty($order_serial_params['order'][ (int)$this->plugin_params->pack_id ]))
				$order_serial_params['order'][ (int)$this->plugin_params->pack_id ] = array(0, 0);
			if(!is_array($order_serial_params['order'][ (int)$this->plugin_params->pack_id ]))
				$order_serial_params['order'][ (int)$this->plugin_params->pack_id ] = array($order_serial_params['order'][ (int)$this->plugin_params->pack_id ], 0);
			$order_serial_params['order'][ (int)$this->plugin_params->pack_id ][1] += (int)$this->plugin_params->quantity;
		}
	}
}
