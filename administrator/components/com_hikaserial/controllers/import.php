<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.9.1
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2015 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class importController extends hikaserialController {
	protected $type = 'import';
	public $helper = null;
	protected $rights = array(
		'display' => array('display','show','listing','cancel'),
		'add' => array(),
		'edit' => array(),
		'modify' => array('import'),
		'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('show');
	}

	public function display($tpl = null, $params = null) {
		JRequest::setVar('layout', 'show');
		return parent::display($tpl, $params);
	}

	public function import() {
		JRequest::checkToken('request') || die('Invalid Token');
		$importFrom = JRequest::getCmd('importfrom');

		$this->helper = hikaserial::get('helper.import');
		switch($importFrom) {
			case 'csv':
				$this->importCsvFile();
				break;
			case 'textarea':
				$this->importTextarea();
				break;
		}

		JRequest::setVar('layout', 'show');
		return parent::display();
	}

	private function importCsvFile() {
		$importFile = JRequest::getVar('csvimport_file', array(), 'files','array');
		$productClass = hikaserial::get('class.product');

		$this->helper->charset = JRequest::getVar('csvimport_charsetconvert', '');
		$this->helper->pack_id = JRequest::getInt('csvimport_pack');
		$ret = $this->helper->importFromFile($importFile);
		$productClass->refreshQuantities();
		return $ret;
	}

	private function importTextarea() {
		$content = JRequest::getVar('textareaimport_content', '', '', 'string', JREQUEST_ALLOWRAW);
		$this->helper->pack_id = JRequest::getInt('textareaimport_pack');
		$productClass = hikaserial::get('class.product');

		$importAsCsv = JRequest::getInt('textareaimport_as_csv', 0);
		if($importAsCsv == 0) {
			if($this->helper->pack_id == 0) {
				return false;
			}
			$ret = $this->helper->handleTextContent($content);
			$productClass->refreshQuantities();
			return $ret;
		}
		$ret = $this->helper->handleCsvContent($content);
		$productClass->refreshQuantities();
		return $ret;
	}
}
