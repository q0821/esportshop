<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form enctype="multipart/form-data" action="index.php" method="post" name="filename">
	<div class="form">
		<fieldset class="adminform">
			<legend><?php echo JText::_('AUP_INSTALL_NEW_PLUGIN_RULE');?></legend>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('AUP_UPLOADXMLFILE');?>
				</div>
			</div>
			<div class="controls">
				<div><?php echo JText::_('AUP_FILENAME');?>:
				<input class="text_area" name="userfile" type="file" size="70"/>
				<input class="btn btn-primary" type="submit" value="<?php echo JText::_('AUP_UPLOADANDINSTALL');?>" />
				</div>
			</div>
			<input type="hidden" name="task" value="uploadfile"/>
			<input type="hidden" name="option" value="com_alphauserpoints"/>
		</fieldset>
	</div>
</form>



