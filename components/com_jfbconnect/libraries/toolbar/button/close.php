<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.4.2
 * @build-date      2015/08/24
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectToolbarButtonClose extends JFBConnectToolbarButton
{
    var $order = '1000';
    var $displayName = "X";
    var $systemName = "close";

    protected function generateJavascript()
    {
        return "display: function ()
                    {
                        jfbcJQuery('#social-toolbar').hide();
                    }";
    }

}