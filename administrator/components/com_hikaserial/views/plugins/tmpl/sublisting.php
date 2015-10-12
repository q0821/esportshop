<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.9.1
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2015 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>	<table class="adminlist table table-striped" cellpadding="1">
		<thead>
			<tr>
				<th class="title titlenum"><?php echo JText::_('HIKA_NUM');?></th>
			<th class="title titlebox">
				<input type="checkbox" name="toggle" value="" onclick="hikashop.checkAll(this);" />
			</th>
			<th class="title"><?php echo JText::_('HIKA_NAME');?></th>
<?php
if(!empty($this->data['listing_header'])) {
	foreach($this->data['listing_header'] as $k => $v) {
		if(is_string($v)) {
			$name = JText::_($v);
		} else if(is_array($v) && isset($v['title'])) {
			$name = $v['title'];
		} else {
			$name = JText::_($k);
		}
?>
			<th class="title"><?php echo $name;?></th>
<?php
	}
}
?>
			<th class="title titleorder" style="width:10%;"><?php
				if(@$this->data['order']->ordering)
					echo JHTML::_('grid.order',  $this->data['plugins'] );
				echo JText::_( 'HIKA_ORDER' );
			?></th>
			<th class="title" style="width:2%;"><?php echo JText::_('HIKA_DELETE');?></th>
			<th class="title" style="width:2%;"><?php echo JText::_('HIKA_PUBLISHED');?></th>
		</tr>
	</thead>
	<tbody>
<?php
$p_id = $this->plugin_type.'_id';
$p_name = $this->plugin_type.'_name';
$p_order = $this->plugin_type.'_ordering';
$p_published = $this->plugin_type.'_published';

$k = 0;
$i = 0;
$a = count($this->elements);
$plugins = array();
if(!empty($this->elements))
	$plugins = array_values($this->elements);
foreach($plugins as $plugin){
	$published_id = $this->plugin_type.'_published-' . $plugin->$p_id;
	$id = $this->plugin_type.'_' . $plugin->$p_id;
?>
		<tr class="row<?php echo $k;?>" id="<?php echo $id;?>">
			<td align="center"><?php
				echo $i+1;
			?></td>
			<td align="center"><?php
				echo JHTML::_('grid.id', $i, $plugin->$p_id );
			?></td>
			<td>
				<a href="<?php echo hikaserial::completeLink('plugins&plugin_type='.$this->plugin_type.'&task=edit&name='.$this->data['pluginName'].'&subtask='.$this->plugin_type.'_edit&'.$p_id.'='.$plugin->$p_id);?>"><?php if(!empty($plugin->$p_name)) { echo $plugin->$p_name; } else { echo '<em>'.JText::_('HIKASERIAL_NO_NAME').'</em>'; }?></a>
			</td>
<?php
if(!empty($this->data['listing_header'])) {
	foreach($this->data['listing_header'] as $key => $v) {
		$style = '';
		if(is_array($v) && isset($v['cell']))
			$style = ' '.$v['cell'];
?>
			<td<?php echo $style;?>><?php echo $this->plugin->configurationLine($key, $plugin); ?></td>
<?php
	}
}
?>
			<td class="order">
				<span><?php
					echo $this->data['pagination']->orderUpIcon(
							$i,
							$this->data['order']->reverse XOR ($plugin->$p_order >= @$plugins[$i-1]->$p_order),
							$this->data['order']->orderUp,
							'Move Up',
							$this->data['order']->ordering
						);
				?></span>
				<span><?php
					echo $this->data['pagination']->orderDownIcon(
							$i,
							$a,
							$this->data['order']->reverse XOR ($plugin->$p_order <= @$plugins[$i+1]->$p_order),
							$this->data['order']->orderDown,
							'Move Down',
							$this->data['order']->ordering
						);
					?></span>
				<input type="text" name="order[]" size="5" <?php if(!$this->data['order']->ordering) echo 'disabled="disabled"'; ?> value="<?php echo $plugin->$p_order; ?>" class="text_area" style="text-align: center" />
			</td>
			<td align="center">
				<span class="spanloading"><?php
					echo $this->data['toggleClass']->delete($id, $this->name.'-'.$plugin->$p_id, $this->plugin_type, true);
				?></span>
			</td>
			<td align="center">
				<span id="<?php echo $published_id;?>" class="spanloading"><?php echo $this->data['toggleClass']->toggle($published_id, (int)$plugin->$p_published, $this->plugin_type);?></span>
			</td>
		</tr>
<?php
	$k = 1 - $k;
	$i++;
}
?>
	</tbody>
</table>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="subtask" value=""/>
<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT;?>" />
<input type="hidden" name="task" value="edit"/>
