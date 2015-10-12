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
if(empty($this->element))
	$this->element = new stdClass();
if(empty($this->element->plugin_params))
	$this->element->plugin_params = new stdClass();
if(empty($this->element->plugin_params->quantity))
	$this->element->plugin_params->quantity = 1;
?>
<table class="table">
	<tbody>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][pack_id]"><?php echo JText::_('SERIAL_PACK');?></label></td>
			<td><?php
				$packType = hikaserial::get('type.pack');
				echo $packType->displaySingle('data[plugin][plugin_params][pack_id]', @$this->element->plugin_params->pack_id);
			?></td>
		</tr>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][quantity]"><?php echo JText::_('QUANTITY');?></label></td>
			<td>
				<input type="text" name="data[plugin][plugin_params][quantity]" value="<?php echo (int)$this->element->plugin_params->quantity; ?>" />
			</td>
		</tr>
	</tbody>
</table>
