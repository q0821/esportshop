<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.4.2
 * @build-date      2015/08/24
 */

defined('_JEXEC') or die('Restricted access');

class TableConfig extends JTable
{
	var $id = null;

	var $setting = null;
	var $value = null;

	var $created_at = null;
	var $updated_at = null;

	function TableConfig(&$db)
	{
		parent::__construct('#__jfbconnect_config', 'id', $db);
	}
}