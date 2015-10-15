<?php
/**
 * @author	hughes
 */
    defined('_JEXEC') or die('Restricted access');
?>
<div id="hikashop_mail">
    <span id="hikashop_pay2go_end_message" class="hikashop_pay2go_end_message">
		<?php echo JText::sprintf('PLEASE_WAIT_BEFORE_REDIRECTION_TO_X', $this->payment_html['order_payment_method']).'<br/><span id="hikashop_pay2go_button_message">'. JText::_('CLICK_ON_BUTTON_IF_NOT_REDIRECTED').'</span>';?>
	</span>
</div>
<form method="post" id="hikashop_pay2go_form" action="<?php echo $this->htmlAction ?>">
    <?php
    foreach ($this->params as $name => $value) { ?>
    <input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">
    <?php } ?>
    <br style="clear:both">
    <input type="submit" class="btn button hikashop_cart_input_button" id="hikashop_pay2go_button" name="sub" value="付款去"/>
</form>
<script type="text/javascript">
    <!--
    document.getElementById('hikashop_pay2go_form').submit();
    //-->
</script>

