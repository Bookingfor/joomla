<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';

/**
 * BookingForConnectorModelOrders Model
 */
class BookingForConnectorModelPortal extends JModelList
{
		
	private $urlGetPrivacy = null;
	private $urlGetAdditionalPurpose = null;
	private $urlGetProductCategoryForSearch = null;
	private $urlGetCartmultimerchantenabled = null;
	
	private $helper = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlGetPrivacy = '/GetPrivacy';
		$this->urlGetAdditionalPurpose = '/GetAdditionalPurpose';
		$this->urlGetProductCategoryForSearch = '/GetProductCategoryForSearch';
		$this->urlGetCartmultimerchantenabled = '/HasMultiMerchantCart';
	}

	public function getProductCategoryForSearchFromService($language='', $typeId = 1) {
		$options = array(
				'path' => $this->urlGetProductCategoryForSearch,
				'data' => array(
					'typeId' => $typeId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);
					
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results;
			}elseif(!empty($res->d)){

				$return = $res->d;
			}
		}
		return $return;
	}

	public function getProductCategoryForSearch($language='', $typeId = 1) {
//		$session = JFactory::getSession();
		$results = BFCHelper::getSession('getProductCategoryForSearch'.$language.$typeId, null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($results==null) {
			$results = $this->getProductCategoryForSearchFromService($language, $typeId);
			BFCHelper::setSession('getProductCategoryForSearch'.$language.$typeId, $results, 'com_bookingforconnector');
		}
		return $results;
	}

	public function getAdditionalPurpose($language='') {
		$results = BFCHelper::getSession('getAdditionalPurpose'.$language, null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($results==null) {
			$results = $this->getAdditionalPurposeFromService($language);
			BFCHelper::setSession('getAdditionalPurpose'.$language, $results, 'com_bookingforconnector');
		}
		return $results;
//		$additionalPurpose = $this->getAdditionalPurposeFromService($language);
//		return $additionalPurpose;
	}

	public function getAdditionalPurposeFromService($language='') {		
		$options = array(
				'path' => $this->urlGetAdditionalPurpose,
				'data' => array(
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results->GetAdditionalPurpose;
			}elseif(!empty($res->d)){

				$return = $res->d->GetAdditionalPurpose;
			}
		}
		return $return;
	}

	public function getPrivacy($language='') {
		$privacy = BFCHelper::getSession('getPrivacy'.$language, null , 'com_bookingforconnector');
		if ($privacy==null) {
			$privacy = $this->getPrivacyFromService($language);
			BFCHelper::setSession('getPrivacy'.$language, $privacy, 'com_bookingforconnector');
		}
		return $privacy;
	}

	public function getPrivacyFromService($language='') {		
		$options = array(
				'path' => $this->urlGetPrivacy,
				'data' => array(
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results->GetPrivacy;
			}elseif(!empty($res->d)){

				$return = $res->d->GetPrivacy;
			}
		}
		return $return;
	}
	
	public function getCartMultimerchantEnabled() {
//		return true; //TODO: da fare
		$cartmultimerchantenabled = BFCHelper::getSession('cartmultimerchantenabled', null , 'com_bookingforconnector');
		if ($cartmultimerchantenabled==null) {
			$cartmultimerchantenabled = $this->getCartmultimerchantenabledFromService();
			BFCHelper::setSession('cartmultimerchantenabled', $cartmultimerchantenabled, 'com_bookingforconnector');
		}
		return $cartmultimerchantenabled;
	}

	public function getCartmultimerchantenabledFromService($language='') {		
		$options = array(
				'path' => $this->urlGetCartmultimerchantenabled,
				'data' => array(
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results->HasMultiMerchantCart;
			}elseif(!empty($res->d)){

				$return = $res->d->HasMultiMerchantCart;
			}
		}
		return $return;
	}
	
	protected function populateState($ordering = NULL, $direction = NULL) {
		
		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		return $this->cache[$store];
	}
}