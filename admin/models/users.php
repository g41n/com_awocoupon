<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class AwoCouponModelUsers extends JModelLegacy {
	var $_pagination = null;
	var $_id = null;

	/**
	 * Constructor
	 **/
	function __construct() {
		parent::__construct();

		$id		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );
		$this->setId($id);

		$limit		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to set the identifier
	 **/
	function setId($id) {
		// Set id and wipe data
		$this->_id	 = $id;
		$this->_data = null;
	}

	/**
	 * Method to get data
	 **/
	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			
			$controller = new AwoCouponController( );
			$model_coupon = $controller->getModel('coupon');
			$model_coupon->setId($this->_id);
			$rows      	= & $model_coupon->getEntry();
			$rows->users = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$rows->totalcount = $this->getTotalCount();
			$this->_data = $rows;

		}
			
		return $this->_data;
	}

	/**
	 * Method to get the total
	 **/
	function getTotal() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object
	 **/
	function getPagination() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function getTotalCount() {
		$sql = 'SELECT COUNT(*) FROM #__'.AWOCOUPON.'_user WHERE coupon_id='.$this->_id; 
		$this->_db->setQuery( $sql );
		$rtn = $this->_db->loadResult();
		return empty($rtn) ? 0 : $rtn;
	}
	/**
	 * Method to build the query
	 **/
	function _buildQuery() {

		// Get the WHERE, and ORDER BY clauses for the query
		$orderby	= $this->_buildContentOrderBy();

		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,u.user_id,
					 if(uv.virtuemart_user_id is NULL,us.name,uv.first_name) as first_name,uv.last_name
				 FROM #__'.AWOCOUPON.' c
				 JOIN #__'.AWOCOUPON.'_user u ON u.coupon_id=c.id
				 JOIN #__users us ON us.id=u.user_id
				 LEFT JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=u.user_id
				WHERE c.id='.$this->_id.'
				GROUP BY u.user_id
				'.$orderby
			; 
		return $sql;
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.filter_order', 	'filter_order', 	'', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= !empty($filter_order) ? ' ORDER BY '.$filter_order.' '.$filter_order_Dir : '';

		return $orderby;
	}

	
	function deleteusers($cids) {	
		$total = count($cids);
		$cids = implode( '\',\'', $cids );

		$sql = 'DELETE FROM #__'.AWOCOUPON.'_user WHERE coupon_id='.(int)$this->_id.' AND user_id IN (\''. $cids .'\')';
		
		$this->_db->setQuery( $sql );
		if(!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $total.' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED');
	}
	

}
