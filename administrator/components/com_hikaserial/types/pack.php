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
class hikaserialPackType {

	protected $values = array();

	public function __construct() {
		$this->app = JFactory::getApplication();
	}

	public function load($addEmpty = false) {
		$query = 'SELECT pack_id, pack_name FROM ' . hikaserial::table('pack').' ORDER BY pack_name ASC';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$packs = $db->loadObjectList('pack_id');
		if(!empty($packs)){
			if($addEmpty){
				$this->values[0] = JHTML::_('select.option', '', JText::_('PACKS_ALL'));
			}
			foreach($packs as $pack){
				$this->values[(int)$pack->pack_id] = JHTML::_('select.option', (int)$pack->pack_id, $pack->pack_name);
			}
		}
	}

	public function display($map, $value, $autoSubmit = false, $addEmpty = false) {
		if(empty($this->values)){
			$this->load($addEmpty);
		}
		$extra = 'class="inputbox" size="1"';
		if($autoSubmit)
			$extra .= ' onchange="document.adminForm.submit();"';
		return JHTML::_('select.genericlist', $this->values, $map, $extra, 'value', 'text', $value);
	}

	public function displaySingle($map, $value, $delete = false) {
		static $jsInit = null;
		if($jsInit !== true) {
			$display_format = 'data.pack_name';
			if($this->app->isAdmin())
				$display_format = 'data.id + " - " + data.pack_name';

			$js = '
if(!window.localPage)
	window.localPage = {};
window.localPage.fieldSetSinglePack = function(el, name) {
	window.hikaserial.submitFct = function(data) {
		var d = document,
			packInput = d.getElementById(name + "_input_id"),
			packSpan = d.getElementById(name + "_span_id");
		if(packInput) { packInput.value = data.id; }
		if(packSpan) { packSpan.innerHTML = '.$display_format.'; }
	};
	window.hikaserial.openBox(el,null,(el.getAttribute("rel") == null));
	return false;
}
window.localPage.fieldRemSinglePack = function(el, name) {
	var d = document,
		packInput = d.getElementById(name + "_input_id"),
		packSpan = d.getElementById(name + "_span_id");
	if(packInput) { packInput.value = ""; }
	if(packSpan) { packSpan.innerHTML = " - "; }
}
';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		$packClass = hikaserial::get('class.pack');
		$popup = hikaserial::get('shop.helper.popup');

		$name = str_replace(array('][','[',']'), '_', $map);
		$pack_id = (int)$value;
		$pack = $packClass->get($pack_id);
		$pack_name = '';
		if(!empty($pack)) {
			$pack_name = @$pack->pack_name;
		} else {
			$pack_id = '';
		}

		$pack_display_name = $pack_name;
		if($this->app->isAdmin())
			$pack_display_name = $pack_id.' - '.$pack_name;

		$ret = '<span id="'.$name.'_span_id">'.$pack_display_name.'</span>' .
			'<input type="hidden" id="'.$name.'_input_id" name="'.$map.'" value="'.$pack_id.'"/> '.
			$popup->display(
				'<img src="'.HIKASERIAL_IMAGES.'icon-16/edit.png" style="vertical-align:middle;"/>',
				'PACK_SELECTION',
				hikaserial::completeLink('pack&task=select&single=true', true),
				'serial_set_pack_'.$name,
				760, 480, 'onclick="return window.localPage.fieldSetSinglePack(this,\''.$name.'\');"', '', 'link'
			);

		if($delete)
			$ret .= ' <a title="'.JText::_('HIKA_DELETE').'" href="#'.JText::_('HIKA_DELETE').'" onclick="return window.localPage.fieldRemSinglePack(this, \''.$name.'\');"><img src="'.HIKASERIAL_IMAGES.'icon-16/delete.png" style="vertical-align:middle;"/></a>';

		return $ret;
	}

	public function displayMultiple($map, $values) {
		if(empty($this->values)){
			$this->load(true);
		}

		if(empty($values))
			$values = array();
		else if(is_string($values))
			$values = explode(',', $values);

		$shopConfig = hikaserial::config(false);
		hikaserial::loadJslib('otree');

		if(substr($map,-2) == '[]')
			$map = substr($map,0,-2);
		$id = str_replace(array('[',']'),array('_',''),$map);
		$ret = '<div class="nameboxes" id="'.$id.'" onclick="window.oNameboxes[\''.$id.'\'].focus(\''.$id.'_text\');">';
		if(!empty($values)) {
			foreach($values as $key) {
				if(isset($this->values[$key]))
					$name = $this->values[$key]->text;
				else
					$name = JText::sprintf('UNKNOWN_PACK_X', $key);

				$ret .= '<div class="namebox" id="'.$id.'_'.$key.'">'.
					'<input type="hidden" name="'.$map.'[]" value="'.$key.'"/>'.$name.
					' <a class="closebutton" href="#" onclick="window.oNameboxes[\''.$id.'\'].unset(this,\''.$key.'\');window.oNamebox.cancelEvent();return false;"><span>X</span></a>'.
					'</div>';
			}
		}

		$ret .= '<div class="namebox" style="display:none;" id="'.$id.'tpl">'.
				'<input type="hidden" name="{map}" value="{key}"/>{name}'.
				' <a class="closebutton" href="#" onclick="window.oNameboxes[\''.$id.'\'].unset(this,\'{key}\');window.oNamebox.cancelEvent();return false;"><span>X</span></a>'.
				'</div>';

		$ret .= '<div class="nametext">'.
			'<input id="'.$id.'_text" type="text" style="width:50px;min-width:60px" onfocus="window.oNameboxes[\''.$id.'\'].focus(this);" onkeyup="window.oNameboxes[\''.$id.'\'].search(this);" onchange="window.oNameboxes[\''.$id.'\'].search(this);"/>'.
			'<span style="position:absolute;top:0px;left:-2000px;visibility:hidden" id="'.$id.'_span">span</span>'.
			'</div>';

		$data = array();
		foreach($this->values as $key => $value) {
			if(empty($key))
				continue;
			$data[$key] = $value->text;
		}

		$namebox_options = array(
			'mode' => 'list',
			'img_dir' => HIKASHOP_IMAGES,
			'map' => $map,
			'min' => $shopConfig->get('namebox_search_min_length', 3),
			'multiple' => true
		);

		$ret .= '<div style="clear:both;float:none;"></div></div>
<div class="namebox-popup">
	<div id="'.$id.'_olist" style="display:none;" class="oList namebox-popup-content"></div>
</div>
<script type="text/javascript">
new window.oNamebox(
	\''.$id.'\',
	'.json_encode($data).',
	'.json_encode($namebox_options).'
);';
			if(!empty($values)) {
				$ret .= '
try{
	window.oNameboxes[\''.$id.'\'].content.block('.json_encode($values).');
}catch(e){}';
			}

			$ret .= '
</script>';
		return $ret;
	}
}
