<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.9.1
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2015 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaserial::completeLink('pack'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
	<table class="admintable" style="width:100%">
		<tr>
			<td valign="top" width="50%">
<?php } else { ?>
	<div class="row-fluid">
		<div class="span6">
<?php } ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('MAIN_INFORMATION'); ?></legend>
					<table class="admintable table" style="width:100%">
						<tr>
							<td class="key">
								<label for="data[pack][pack_name]"><?php echo JText::_('HIKA_NAME'); ?></label>
							</td>
							<td>
								<input type="text" name="data[pack][pack_name]" value="<?php echo $this->escape(@$this->pack->pack_name); ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack][pack_data]"><?php echo JText::_('PACK_DATA'); ?></label>
							</td>
							<td><?php
								echo $this->packDataType->display('data[pack][pack_data]', @$this->pack->pack_data);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack][pack_generator]"><?php echo JText::_('PACK_GENERATOR'); ?></label>
							</td>
							<td><?php
								echo $this->packGeneratorType->display('data[pack][pack_generator]', @$this->pack->pack_generator);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack][pack_published]"><?php echo JText::_('HIKA_PUBLISHED'); ?></label>
							</td>
							<td><?php
								echo JHTML::_('hikaselect.booleanlist', 'data[pack][pack_published]', '', @$this->pack->pack_published);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack][pack_description]"><?php echo JText::_('PACK_DESCRIPTION'); ?></label>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2"><?php
								$this->editor->content = @$this->pack->pack_description;
								echo $this->editor->display();
							?></td>
						</tr>
					</table>
				</fieldset>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
			</td>
			<td valign="top" width="50%">
<?php } else { ?>
		</div>
		<div class="span6">
<?php } ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('ADDITIONAL_INFORMATION'); ?></legend>
					<table class="admintable table" style="width:100%">
						<tr>
							<td class="key">
								<label for="data[pack_params][status_for_refund]"><?php echo JText::_('SERIAL_STATUS_FOR_REFUND'); ?></label>
							</td>
							<td><?php
								echo $this->serialStatusType->display('data[pack_params][status_for_refund]', @$this->pack->pack_params->status_for_refund);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack_params][stock_level_notify]"><?php echo JText::_('SERIAL_STOCK_LEVEL_NOTIFY'); ?></label>
							</td>
							<td>
								<input type="text" name="data[pack_params][stock_level_notify]" value="<?php echo @$this->pack->pack_params->stock_level_notify;?>"/>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack_params][unlimited_quantity]"><?php echo JText::_('SERIAL_UNLIMITED_QUANTITY'); ?></label>
							</td>
							<td><?php
								echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][unlimited_quantity]', '',  @$this->pack->pack_params->unlimited_quantity);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack_params][consumer]"><?php echo JText::_('HIKA_CONSUMER_PACK'); ?></label>
							</td>
							<td><?php
								echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][consumer]', '',  @$this->pack->pack_params->consumer);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack_params][consume_user_assign]"><?php echo JText::_('HIKA_CONSUME_USER_ASSIGN'); ?></label>
							</td>
							<td><?php
								echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][consume_user_assign]', '',  @$this->pack->pack_params->consume_user_assign);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack_params][webservice]"><?php echo JText::_('HIKA_WEBSERVICE_ACCESS'); ?></label>
							</td>
							<td><?php
								echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][webservice]', '',  @$this->pack->pack_params->webservice);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label for="data[pack_params][no_user_assign]"><?php echo JText::_('SERIAL_NO_USER_ASSIGN'); ?></label>
							</td>
							<td><?php
								echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][no_user_assign]', '',  @$this->pack->pack_params->no_user_assign);
							?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('HIKA_STATISTICS'); ?></legend>
					<table class="admintable table" style="width:100%">
<?php
$statuses = $this->serialStatusType->getValues();
foreach($statuses as $key => $value) { ?>
						<tr>
							<td class="key">
								<?php
								if(isset($this->counters[$key])) {
									echo '<a href="' . hikaserial::completeLink('serial&filter_pack=' . @$this->pack->pack_id . '&filter_status=' . $key).'">'.$value.'</a>';
								} else {
									echo $value;
								}
								?>
							</td>
							<td><?php
								if(isset($this->counters[$key])) {
									echo $this->counters[$key];
								} else {
									echo '0';
								}
							?></td>
						</tr>
<?php } ?>
						<tr>
							<td class="key">
								<a href="<?php echo hikaserial::completeLink('serial&filter_pack=' . @$this->pack->pack_id . '&filter_status=');?>"><?php echo JText::_('TOTAL_SERIALS'); ?></a>
							</td>
							<td><?php
								if(isset($this->counters['total'])) {
									echo $this->counters['total'];
								} else {
									echo '0';
								}
							?></td>
						</tr>
<?php foreach($this->counters as $name => $value) {
	if($name == 'total' || isset($statuses[$name]))
		continue;
?>						<tr>
							<td class="key">
								<label><?php echo ucfirst($name); ?></label>
							</td>
							<td><?php echo $value;?></td>
						</tr>
<?php } ?>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('PRODUCTS'); ?></legend>
					<table class="adminlist table table-striped" style="width:100%">
						<thead>
							<tr>
								<th><?php echo JText::_('HIKA_NAME');?></th>
								<th><?php echo JText::_('PRODUCT_CODE');?></th>
								<th><?php echo JText::_('PRODUCT_QUANTITY');?></th>
								<th><?php echo JText::_('ID');?></th>
							</tr>
						</thead>
						<tbody>
<?php
if(!empty($this->products)){
	foreach($this->products as $key => $product) {
?>
							<tr>
								<td><a href="<?php echo hikaserial::completeLink('shop.product&task=edit&cid[]='.$product->product_id); ?>"><?php echo $product->product_name; ?></a></td>
								<td><?php echo $product->product_code; ?></td>
								<td align="center"><?php echo $product->quantity; ?></td>
								<td align="center"><?php echo $product->product_id; ?></td>
							<tr>
<?php
	}
} else {
?>
							<tr>
								<td colspan="4">
									<em><?php echo JText::_('PACK_NOT_LINKED_WITH_PRODUCT');?></em>
								</td>
							<tr>
<?php
}
?>
						</tbody>
					</table>
				</fieldset>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
		</div>
	</div>
<?php } ?>
	<div class="clr"></div>
	<input type="hidden" name="cid[]" value="<?php echo @$this->pack->pack_id; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
