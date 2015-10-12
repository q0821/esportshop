<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.3.0
 * @build-date      2015/03/19
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