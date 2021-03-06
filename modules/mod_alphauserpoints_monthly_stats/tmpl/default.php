<?php
/**
* @version		$Id: mod_alphauserpoints_monthly_stats.php 2015-08-03 v2.0.2 $
* @package		AlphaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2008-2015. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$document	= JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_alphauserpoints/assets/css/bar.css');

$com_params = JComponentHelper::getParams( 'com_alphauserpoints' );
$user 		= JFactory::getUser();

$db			      = JFactory::getDBO();
?>
<form name="frmModAUPMS" id="frmModAUPMS" method="post" action="">
<?php 
echo $currentmonthlist . "<br /><br />"; 
if ( $list ) {
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?php if ( $params->get('showheader') ) { ?>
  <tr>
    <?php if ( $params->get('showdate') ) { ?>
	<td class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>" width="20%"><div align="center"><?php echo JText::_('MODAUP_MS_DATE'); ?></div></th>
	<?php } ?>
    <td class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>"><div align="left"><?php echo JText::_('MODAUP_MS_NAME'); ?></div></th>
    <td class="sectiontableheader<?php echo $params->get( 'pageclass_sfx' ); ?>" width="25%"><div align="left"><?php echo JText::_('MODAUP_MS_POINTS'); ?></div></th>
  </tr>
<?php } ?>
	<?php
	$k = 0;
	for ($i=0, $n=count( $list ); $i < $n; $i++) {
	
		$item 	= $list[$i];
		
		$usrname = htmlspecialchars($item->usrname, ENT_QUOTES, 'UTF-8');
		
		$profil = getProfileLink( $com_params->get('linkToProfile', ''), $item );		
	
		if ($i==0) {
			$maxpoints = $item->sumpoints;
			$barwidth = 100;
		}
		else {
			$barwidth = round(($item->sumpoints * 100) / $maxpoints);
		}	
		 ?>
		<tr>
		<?php if ( $params->get('showdate') ) { ?>
		<td class="sectiontableentry<?php echo $k; ?>" width="20%"><div align="center"><?php echo $item->insert_date; ?></div></td>
		<?php } ?>
		<td class="sectiontableentry<?php echo $k; ?>"><div align="left">
		<?php 
		if ( $user->id || !$user->id && $com_params->get( 'allowGuestUserViewProfil', 1) ){
			echo '<a href="'.$profil.'">'. $usrname . '</a>';
		} else echo $usrname;				 
		?>
		</div></td>
		<td class="sectiontableentry<?php echo $k; ?>" width="25%"><div align="left">
		<?php 
		echo getFormattedPoints( $item->sumpoints ) ;
		?>
		</div></td>
		</tr>
		<?php if ( $params->get('showbar') ) { ?>
			<tr>		
			<td colspan="3" height="8">
				<div class="progress progress-striped active">
					<div class="bar" style="width: <?php echo $barwidth;?>%;"><?php echo $barwidth;?>%</div>
				</div>
			<!--<img style="margin-bottom:1px" src="modules/mod_alphauserpoints_monthly_stats/images/bar.gif" alt="" height="8" width="<?php echo $barwidth;?>%" />-->
			</td>
			</tr>		
		<?php
			}
		$k = 1 - $k;
 	 }
?>
</table>
<?php
} else echo JText::_('MODAUP_MS_NOT_AVAILABLE');
?>
</form>