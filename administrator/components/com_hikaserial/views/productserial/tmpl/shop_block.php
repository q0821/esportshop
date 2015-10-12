<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.9.1
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2015 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset class="adminform">
<legend><?php echo JText::_('HIKA_SERIALS'); ?></legend>
	<div style="float: right;">
		<a onclick="return hikaserial_setPack(this);" href="<?php echo hikaserial::completeLink('pack&task=select&single=true', true); ?>" rel="{handler: 'iframe', size: {x: 760, y: 480}}" class="modal">
			<button class="btn" onclick="return false;" type="button"><img src="<?php echo HIKASHOP_IMAGES;?>add.png"/><?php echo JText::_('ADD');?></button>
		</a>
	</div>
	<table class="adminlist table table-striped" style="cell-spacing:1px">
		<thead>
			<tr>
				<th class="title"><?php echo JText::_('HIKA_NAME');?></th>
				<th class="title"><?php echo JText::_('PRODUCT_QUANTITY');?></th>
				<th class="title"><?php echo JText::_('ID');?></th>
				<th class="title"><?php echo JText::_('HIKA_DELETE');?></th>
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
			<tr class="row<?php echo $k; ?>" id="serial_pack_<?php echo $data->pack_id; ?>">
				<td><?php echo $data->pack_name;?></td>
				<td><input type="text" value="<?php echo $data->quantity;?>" name="data[hikaserial][pack_qty][]" size="5"/></td>
				<td align="center"><?php echo $data->pack_id;?><input type="hidden" value="<?php echo $data->pack_id;?>" name="data[hikaserial][pack_id][]"/></td>
				<td align="center"><a href="#" onclick="hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKASHOP_IMAGES;?>delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
			<tr class="row<?php echo $k; ?>" style="display:none" id="hikaserial_tpl_pack_line">
				<td>{pack_name}</td>
				<td><input type="text" value="1" name="{input_qty_name}" size="5"/></td>
				<td align="center">{id}<input type="hidden" value="{id}" name="{input_id_name}"/></td>
				<td align="center"><a href="#" onclick="hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKASHOP_IMAGES;?>delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
		</tbody>
	</table>
</fieldset>
<script type="text/javascript">
function hikaserial_setPack(el) {
	window.hikaserial.submitFct = function(data) {
		var htmlblocks = {input_qty_name: 'data[hikaserial][pack_qty][]', input_id_name: 'data[hikaserial][pack_id][]'};
		try {
			{
				pack = data;
				if( typeof(pack) == "object" && pack.id > 0 ) {
					hikaserial.dupRow('hikaserial_tpl_pack_line', htmlblocks, "serial_pack_" + pack.id, pack);
				}
			}
		}catch(e){}
	};
	hikaserial.openBox(el);
	return false;
}
</script>
<input type="hidden" name="data[hikaserial][form]" value="1"/>
