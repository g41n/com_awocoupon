<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
if (!function_exists('printr')) { function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; } }
if (!function_exists('printrx')) { function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; } }
//ini_set("display_errors", 1); error_reporting(E_ALL);


// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

global $def_lists;

require JPATH_COMPONENT_ADMINISTRATOR.'/awocoupon.config.php';

if (!class_exists( 'VmConfig' )) require JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php';
VmConfig::loadConfig();

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT.'/tables');

$jlang =& JFactory::getLanguage();
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, null, true);
$jlang->load('com_awocoupon', JPATH_COMPONENT_ADMINISTRATOR, null, true);

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

//Create the controller
$controller = new AwoCouponController( );

// Perform the Request task
$controller->execute( JRequest::getWord('task', ''));
$controller->redirect();

		
?>
<br><div align="right" style="font-size:9px;">&copy; <a href="http://awodev.com" target="_blank">AwoCoupon</a> by Seyi Awofadeju <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL License</a></div>
