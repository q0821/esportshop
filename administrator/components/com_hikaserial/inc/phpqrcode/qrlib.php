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

$QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR;

include $QR_BASEDIR.'qrconst.php';
include $QR_BASEDIR.'qrconfig.php';
include $QR_BASEDIR.'qrtools.php';
include $QR_BASEDIR.'qrspec.php';
include $QR_BASEDIR.'qrimage.php';
include $QR_BASEDIR.'qrinput.php';
include $QR_BASEDIR.'qrbitstream.php';
include $QR_BASEDIR.'qrsplit.php';
include $QR_BASEDIR.'qrrscode.php';
include $QR_BASEDIR.'qrmask.php';
include $QR_BASEDIR.'qrencode.php';
