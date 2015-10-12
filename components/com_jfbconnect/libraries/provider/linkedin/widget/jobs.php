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

class JFBConnectProviderLinkedinWidgetJobs extends JFBConnectWidget
{
    var $name = "Jobs";
    var $systemName = "jobs";
    var $className = "jlinkedJobs";
    var $tagName = "jlinkedjobs";
    var $examples = array (
        '{JLinkedJobs}',
        '{JLinkedJobs companyid=365848}'
    );

    protected function getTagHtml()
    {
        $tag = '<script type="IN/JYMBII"';
        $tag .= $this->getField('companyid', null, null, '', 'data-companyid');
        $tag .= ' data-format="inline"';
        $tag .= '></script>';
        return $tag;
    }
}
