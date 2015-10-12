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
if(!defined('HIKAMARKET_COMPONENT'))
	return;
$doc = JFactory::getDocument();
$doc->addScript(HIKASERIAL_JS.'hikaserial.js');
?><dt class="hikamarket_product_plugin_hikaserial"><label><?php echo JText::_('HIKA_SERIALS'); ?></label></dt>
<dd class="hikamarket_product_plugin_hikaserial">
	<table class="adminlist table table-striped" style="cell-spacing:1px;width:100%;">
		<thead>
			<tr>
				<th class="title"><?php echo JText::_('HIKA_NAME');?></th>
				<th class="title"><?php echo JText::_('PRODUCT_QUANTITY');?></th>
				<th class="title"><?php echo JText::_('ID');?></th>
				<th align="center"><?php
					echo $this->popupHelper->display(
						'<img src="'. HIKAMARKET_IMAGES .'icon-16/plus.png" alt="'.JText::_('ADD').'">', JText::_('ADD'),
						hikaserial::completeLink('pack&task=select&single=true', true),
						'hikamarket_product_plugin_hikaserial'.$this->product_id,
						760, 480, 'onclick="return hikaserial_setPack('.$this->product_id.',this);"', '', 'link'
					);
				?></th>
			</tr>
		</thead>
		<tbody>
<?php
$k = 0;
if(empty($this->data)) {
	$this->data = array();
}
foreach($this->data as $data) {
?>
			<tr class="row<?php echo $k; ?>" id="serial_pack_<?php echo $this->product_id . '_' . $data->pack_id; ?>">
				<td><?php echo $data->pack_name;?></td>
				<td><input type="text" value="<?php echo $data->quantity;?>" name="data[hikaserial][<?php echo $this->product_id; ?>][pack_qty][]" size="5"/></td>
				<td align="center"><?php echo $data->pack_id;?><input type="hidden" value="<?php echo $data->pack_id;?>" name="data[hikaserial][<?php echo $this->product_id; ?>][pack_id][]"/></td>
				<td align="center"><a href="#" onclick="hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
			<tr class="row<?php echo $k; ?>" style="display:none" id="hikaserial_tpl_pack_line_<?php echo $this->product_id; ?>">
				<td>{pack_name}</td>
				<td><input type="text" value="1" name="{input_qty_name}" size="5"/></td>
				<td align="center">{id}<input type="hidden" value="{id}" name="{input_id_name}"/></td>
				<td align="center"><a href="#" onclick="hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
		</tbody>
	</table>
	<script type="text/javascript">
	function hikaserial_setPack(pid,el) {
		window.hikaserial.submitFct = function(data) {
			var htmlblocks = {input_qty_name: 'data[hikaserial]['+pid+'][pack_qty][]', input_id_name: 'data[hikaserial]['+pid+'][pack_id][]'};
			try {
				pack = data;
				if( typeof(pack) == "object" && pack.id > 0 ) {
					window.hikaserial.dupRow('hikaserial_tpl_pack_line_'+pid, htmlblocks, "serial_pack_" + pack.id, pack);
				}
			}catch(e){alert(e);}
		};
		window.hikaserial.openBox(el);
		return false;
	}
	</script>
	<input type="hidden" name="data[hikaserial][form]" value="1"/>
</dd>
