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
	if(empty($this->data) && !$this->show_refresh)
		return;

	if(empty($this->ajax)) {
?>
<fieldset class="adminform" id="hikashop_order_field_serial">
<?php
	}
?>
	<legend><?php echo JText::_('HIKA_SERIALS')?></legend>
<?php if($this->show_refresh) { ?>
	<div style="float:right">
		<a href="<?php echo hikaserial::completeLink('orderserial&task=refresh&order_id='.$this->order_id, true); ?>">
			<button class="btn" type="button"><img src="<?php echo HIKASHOP_IMAGES;?>refresh.png" style="vertical-align:middle;"/><?php echo JText::_('REFRESH_ASSOCIATIONS');?></button>
		</a>
	</div>
<?php } ?>
	<table style="width:100%;cell-spacing:1px;" class="adminlist table table-striped">
		<thead>
			<tr>
				<th><?php echo JText::_('PACK_NAME');?></th>
				<th><?php echo JText::_('SERIAL_DATA');?></th>
				<th><?php echo JText::_('ASSIGN_DATE');?></th>
				<th><?php echo JText::_('ATTACHED_TO_PRODUCT');?></th>
			</tr>
		</thead>
		<tbody>
<?php
if(!empty($this->data)) {
	foreach($this->data as $data) {
?>
			<tr>
				<td><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$data->pack_id);?>"><?php echo $data->pack_name;?></a></td>
				<td><a href="<?php echo hikaserial::completeLink('serial&task=edit&cid[]='.$data->serial_id);?>"><?php echo $data->serial_data; ?></a></td>
				<td><?php echo hikaserial::getDate($data->serial_assign_date);?></td>
				<td><?php echo $data->order_product_name; ?></td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
<?php
	if(empty($this->ajax)) {
?>
</fieldset>
<script type="text/javascript">
window.Oby.registerAjax('hikashop.order_update', function(params){
	if(params.el === undefined) return;
	window.Oby.xRequest("<?php echo hikaserial::completeLink('orderserial&task=show&cid='.$this->order_id, true, false, true); ?>", {update: 'hikashop_order_field_serial'});
});
</script>
<?php }
