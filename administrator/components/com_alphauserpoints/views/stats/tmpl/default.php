<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
$rows = $this->result;
$rowsSpent = $this->result2;
$listrules = $this->listrules;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('stats-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
			Joomla.submitform(task, document.getElementById('backForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<table class="table table-striped table-hover">
<thead>
 <tr>
 <th colspan="2"><?php echo JText::_( 'AUP_STATISTICS' )  ; ?></th>
 </tr>
 </thead>
 <tbody>
 <tr>
	<td width="220"><?php echo JText::_( 'AUP_TOTAL_COMMUNITY_POINTS' )  ; ?></td>
	<td><span class="badge badge-success"><?php echo getFormattedPointsAdm($this->communitypoints) . " " . strtolower(JText::_( 'AUP_POINTS' )) ; ?></span></td>
 </tr>
 <tr>
	<td width="220"><?php echo JText::_( 'AUP_TOTAL_MEMBERS' )  ; ?></td>
	<td><?php echo $this->numusers ; ?></td>
 </tr>
 <tr>
	<td><?php echo JText::_( 'AUP_AVERAGE_AGE_COMMUNITY_MEMBERS' ); ?></td>
	<td>
	<?php 
	 if ($this->average_age) {	
		echo $this->average_age . " " . JText::_( 'AUP_YEARS' ) ; 
	 } else echo JText::_( 'AUP_UNKNOW' );
	?></td>
 </tr>
 <tr>
	<td><?php echo JText::_( 'AUP_AVERAGE_POINTS_EARNED_BY_DAY_MEMBER' )  ; ?></td>
	<td><?php echo $this->average_points_earned_by_day ; ?></td>
 </tr>
 <tr>
	<td><?php echo JText::_( 'AUP_AVERAGE_POINTS_SPENT_BY_DAY_MEMBER' )  ; ?></td>
	<td><?php echo $this->average_points_spent_by_day ; ?></td>
 </tr>
 <?php if ($this->ratiomembers) { 
 	$numM = '0';
	$numF = '0';
	$numU = '0';
	foreach( $this->ratiomembers as $ratio ) {
		$sexe = $ratio->gender;
		$num = $ratio->nb;
		switch ( $sexe ) {
			case '0':
				$numU = $num;
				break;			
			case '1':
				$numM = $num ;
				break;
			case '2':
				$numF = $num ;	
				break;
		}	
    }
 	$male = JText::_( 'AUP_MALES' ) . ': ' . $numM ; 
	$female = JText::_( 'AUP_FEMALES' ) . ': ' . $numF ;	
 	$unknow = ( $numU ) ? JText::_( 'AUP_UNKNOW' ) . ': ' . $numU : '';
	
	
	$ratiogender = '<br />' . $male . '<br />' . $female  . '<br />' . $unknow ;
 ?> 
 <tr>
	<td><div id="chart_div_gender"></div></td>
	<td> </td>
 </tr> 
<?php } ?> 
<?php if ( $this->inactiveusers && $this->num_days_inactiveusers_rule ) { 
	$tense_days = ( $this->num_days_inactiveusers_rule > 1 ) ? JText::_( 'AUP_DAYS' ) : JText::_( 'AUP_DAY' );
?> 
 <tr>
	<td><?php echo JText::_( 'AUP_INACTIVE_USERS' ) . ' > ' . JText::_( 'AUP_CURRENT_RULE' ) .' ' . $this->num_days_inactiveusers_rule . ' ' . $tense_days ; ?></td>
	<td><?php echo $this->inactiveusers ; ?></td>
 </tr>
<?php } ?>
 <tr>
	<td><?php echo JText::_( 'AUP_MOST_COUNTRIES_REPRESENTED' )  ; ?></td>
	<td>	
	<?php
	$unknowcountry = "";
	 foreach($this->topcountryusers as $countryusers){
	 	$percentusers = intval($countryusers->numusers)/intval($this->numusers)*100;
		if ( $percentusers >= 1 ) {
				$showpercentusers = number_format($percentusers,1,'.','')."%";
		} elseif ( $percentusers >= 0 && $percentusers < 1 ) {
				$showpercentusers = number_format($percentusers,3,'.','')."%";
		}
		$tense = ( $countryusers->numusers > 1 ) ? strtolower(JText::_( 'AUP_MEMBERS')) : strtolower(JText::_( 'AUP_MEMBER')) ;
		if ( $countryusers->country != '' ) {
	 		echo $countryusers->country . ": " . $countryusers->numusers . " " . $tense . "&nbsp;&nbsp;<font color=\"#CCCCCC\">(" . $showpercentusers . ")</font><br />"; 
		} else {
			$unknowcountry =  "<font color=\"#999999\">" . JText::_( 'AUP_UNKNOW' ) . ": " . $countryusers->numusers . " " . $tense . "</font>&nbsp;&nbsp;<font color=\"#CCCCCC\">(" . $showpercentusers . ")</font>"; 
		}
	 }
	echo $unknowcountry;
	 ?>	
	</td>
 </tr>
 </tbody>
</table>
<div id="chart_div"></div>
<br /><br />
<div class="navbar">
	<div class="navbar-inner" style="padding: 12px 20px 0 20px;">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
			<div class="form-horizontal">
				<?php echo JText::_( 'AUP_RULES' ) . " " . $listrules . " <NOBR>" . JText::_( 'AUP_START_DATE' ) . "&nbsp;" . JHTML::_('calendar', $this->date_start, 'date_start', 'date_start', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'style'=>'margin-top:6px;', 'size'=>'20',  'maxlength'=>'19')).'</NOBR>'; ?>
				<?php echo " <NOBR>" . JText::_( 'AUP_END_DATE' ) .  "&nbsp;" . JHTML::_('calendar', $this->date_end, 'date_end', 'date_end', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'style'=>'margin-top:6px;', 'size'=>'20',  'maxlength'=>'19')).'</NOBR>'; ?>
				&nbsp;<input class="btn btn-primary" type="submit" name="Submit" value="<?php echo JText::_( 'JSHOW' ); ?>">
				<input type="hidden" name="option" value="com_alphauserpoints" />
				<input type="hidden" name="task" value="stats" />
			</div>
		</form>
	</div>
</div>
<h2><?php echo JText::_( 'AUP_WINNING_POINTS' ); ?></h2>
<table class="table table-striped table-hover">
  <thead>
	<tr>
	  <th width="140"><?php echo JText::_( 'AUP_NAME' ); ?></th>
	  <th width="120" ><?php echo JText::_( 'AUP_USERNAME' ); ?></th>
	  <th width="180"><?php echo JText::_( 'AUP_REFERREID' ); ?></th>
	  <th width="400">&nbsp;</th>
	  <th><?php echo JText::_( 'AUP_POINTS' ); ?></th>
	</tr>
  </thead>
  <tbody>
	<?php
		for ($i=0, $n=count( $rows ); $i < $n; $i++)
		{
			$row 	= $rows[$i];
			if ($i==0) {
				$maxpoints = $row->sumpoints;
				$barwidth = 100;
			}
			else {
				$barwidth = round(($row->sumpoints * 100) / $maxpoints);
			}
			
			$percent = intval($row->sumpoints)/intval($this->communitypoints)*100;		
			if ( $percent >= 1 ) {
					$showpercent = number_format($percent,1,'.','')."%";
			} elseif ( $percent >= 0 && $percent < 1 ) {
					$showpercent = number_format($percent,3,'.','')."%";
			}
	?>
			<tr height="20" valign="middle">
			  <td height="20"><?php echo $row->name;?> </td>
			  <td><?php echo $row->username;?> </td>
			  <td><?php echo $row->referreid;?> </td>
			  <td>
				<div class="progress progress-striped active">
					<div class="bar" style="width: <?php echo $barwidth;?>%;"><?php echo $barwidth;?>%</div>
				</div>
			  </td>
			  <td><span class="badge badge-success"><?php echo getFormattedPointsAdm($row->sumpoints); ?></span>&nbsp;<span class="badge"><?php echo $showpercent;?></span></td>
			</tr>
			<?php
	}
	?>
  </tbody>
</table>
<h2><?php echo JText::_( 'AUP_POINTS_SPENT' ); ?></h2>
<table class="table table-striped">
  <thead>
	<tr>
	  <th width="140"><?php echo JText::_( 'AUP_NAME' ); ?></th>
	  <th width="120" ><?php echo JText::_( 'AUP_USERNAME' ); ?></th>
	  <th width="180"><?php echo JText::_( 'AUP_REFERREID' ); ?></th>
	  <th width="400">&nbsp;</th>
	  <th><?php echo JText::_( 'AUP_POINTS' ); ?></th>
	</tr>
  </thead>
  <tbody>
	<?php
		for ($i=0, $n=count( $rowsSpent ); $i < $n; $i++)
		{
			$row 	= $rowsSpent[$i];
			if ($i==0) {
				$maxpoints = abs($row->sumpoints);
				$barwidth = 100;
			}
			else {
				$barwidth = round((abs($row->sumpoints) * 100) / $maxpoints);
			}
	?>
			<tr height="20" valign="middle">
			  <td height="20"><?php echo $row->name;?> </td>
			  <td><?php echo $row->username;?> </td>
			  <td><?php echo $row->referreid;?> </td>
			  <td>
				<div class="progress progress-striped active">
					<div class="bar-warning" style="width: <?php echo $barwidth;?>%;"><?php echo $barwidth;?>%</div>
				</div>
			  </td>
			  <td><?php echo getFormattedPointsAdm($row->sumpoints);?></td>
			</tr>
			<?php
	}
	?>
  </tbody>
</table>
</div>
<form action="index.php" method="post" name="backForm" id="backForm" class="form-validate">			
<input type="hidden" name="option" value="com_alphauserpoints" />
<input type="hidden" name="task" value="cpanel" />
</form>