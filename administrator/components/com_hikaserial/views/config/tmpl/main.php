<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.9.1
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2015 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
<div id="page-main">
	<table style="width:100%">
		<tr>
			<td valign="top" width="50%">
<?php } else { ?>
<div id="page-main" class="row-fluid">
	<div class="span6">
<?php } ?>
				<fieldset class="adminform">
<table class="admintable table" cellspacing="1">
<tr>
	<td class="key"><?php echo JText::_('ASSIGNABLE_ORDER_STATUSES'); ?></td>
	<td><?php
		echo $this->orderstatusType->displayMultiple("config[assignable_order_statuses]", $this->config->get('assignable_order_statuses', 'confirmed,shipped'));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('ASSIGNED_SERIAL_STATUS'); ?></td>
	<td><?php
		echo $this->serial_status->display("config[assigned_serial_status]", $this->config->get('assigned_serial_status', 'assigned'));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('USED_SERIAL_STATUS'); ?></td>
	<td><?php
		echo $this->serial_status->display("config[used_serial_status]", $this->config->get('used_serial_status', 'used'));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('UNASSIGNED_SERIAL_STATUS'); ?></td>
	<td><?php
		echo $this->serial_status->display("config[unassigned_serial_status]", $this->config->get('unassigned_serial_status', 'unassigned'), true);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('REMOVE_DATA_ON_UNASSIGNED'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[unassigned_remove_data]", '', $this->config->get('unassigned_remove_data', false));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('DISPLAY_SERIAL_STATUSES'); ?></td>
	<td><?php
		echo $this->serial_status->displayMultiple("config[display_serial_statuses][]", explode(',', $this->config->get('display_serial_statuses', 'assigned,used')), 'namebox');
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('USEABLE_SERIAL_STATUSES'); ?></td>
	<td><?php
		echo $this->serial_status->displayMultiple("config[useable_serial_statuses][]", explode(',', $this->config->get('useable_serial_statuses', 'free,reserved')), 'namebox');
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
<table class="admintable table" cellspacing="1">
<tr>
	<td class="key"><?php echo JText::_('LINK_PRODUCT_QUANTITY'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[link_product_quantity]", '', $this->config->get('link_product_quantity', false));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('FORBIDDEN_CONSUME_GUEST'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[forbidden_consume_guest]", '', $this->config->get('forbidden_consume_guest', true));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('CONSUME_DISPLAY_DETAILS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[consume_display_details]", '', $this->config->get('consume_display_details', false));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('SERIAL_TRUNCATED_SIZE_IN_BACKEND'); ?></td>
	<td><input type="text" name="config[serial_display_size]" value="<?php echo $this->config->get('serial_display_size', 30);?>"/></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('USE_FAST_RANDOM'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[use_fast_random]", '', $this->config->get('use_fast_random', false));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('SAVE_HISTORY'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[save_history]", '', $this->config->get('save_history', false));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('USE_DELETED_SERIAL_STATUS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[use_deleted_serial_status]", '', $this->config->get('use_deleted_serial_status', false));
	?></td>
</tr>
</table>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
</div>
<?php } else { ?>
	</div>
</div>
<?php } ?>
