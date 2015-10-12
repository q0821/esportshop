<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2015/08/24
 */

defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.plugins.socialprofile');

class plgSocialProfilesAlphauserpoints extends SocialProfilePlugin
{

    function __construct(&$subject, $params)
    {
        $this->_componentFolder = '';
        $this->_componentFile = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
        parent::__construct($subject, $params);

//        $this->defaultSettings->set('import_avatar', '1');
        $this->defaultSettings->set('import_always', '0');
        $this->defaultSettings->set('registration_show_fields', '0'); //0=None, 1=Required, 2=All
        $this->defaultSettings->set('imported_show_fields', '0'); //0=No, 1=Yes
        $this->defaultSettings->set('registration_show_fields', '0');
    }

    protected function createUser($profileData)
    {
        $this->importSocialProfile();
        return true;
    }

    protected function importSocialProfile()
    {
        require_once($this->_componentFile);
        parent::importSocialProfile();
    }

    protected function saveProfileField($fieldId, $value)
    {
        if ($fieldId == "gender")
            $value = $value == "male" ? 0 : 1;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__alpha_userpoints'))
            ->set($db->qn($fieldId) . '=' . $db->q($value))
            ->where($db->qn('userid') . '=' . $this->joomlaId);

        $db->setQuery($query);
        $db->execute();
    }

    protected function getProfileFields()
    {
        $fields = array();
        $fields[] = (object)array('id' => "gender", "name" => "Gender");
        $fields[] = (object)array('id' => "aboutme", "name" => "About Me");
        $fields[] = (object)array('id' => "website", "name" => "Website URL");
        $fields[] = (object)array('id' => "city", "name" => "City");
        $fields[] = (object)array('id' => "country", "name" => "Country");
        $fields[] = (object)array('id' => "zipcode", "name" => "Zip Code");
        $fields[] = (object)array('id' => "education", "name" => "Education");
        $fields[] = (object)array('id' => "graduationyear", "name" => "Graduation Year");
        $fields[] = (object)array('id' => "facebook", "name" => "Facebook Profile URL");
        $fields[] = (object)array('id' => "twitter", "name" => "Twitter Profile URL");

        return $fields;
    }

    public function awardPoints($userId, $name, $args)
    {
        require_once($this->_componentFile);

        $key = $args->get('key', '');

        $name = str_replace(".", "_", $name);
        $name = 'plgaup_jfbconnect_' . $name;
        $keyreference = AlphaUserPointsHelper::buildKeyreference($name, $key );

        // get the current user's Referrerid always, for now.
        $profile = AlphaUserPointsHelper::getUserInfo ( '', $userId ) ;
        $referrerId = $profile->referreid;
        $return = AlphaUserPointsHelper::newpoints($name, $referrerId, $keyreference);
    }

}