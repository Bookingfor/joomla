<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Bookingforconnector Component
 */
class BookingForConnectorViewResource extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;
	protected $params = null;

	// Overwriting JView display method
	function display($tpl = null)
	{
		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$params		= $this->params;		

		if(!isset($item)){
			header ("Location: ". JURI::root()); 
			$app->close();
		}

		$this->state = $state;
		$this->params = $params;
		$this->item = $item;
		$this->config = $config;
		$this->sitename = $sitename;
		$this->language = $language;
		
		$resource = $this->item;
		$merchant = $resource->Merchant;

		$items=null;
		$pagination=null;
		if(isset($_REQUEST['newsearch'])){
			bfi_setSessionFromSubmittedData();
		}
		if(isset($_REQUEST['state'])){
			$currParam = BFCHelper::getSearchParamsSession();
			$currParam['state'] = $_REQUEST['state'];
			BFCHelper::setSearchParamsSession($currParam);
		}


		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/bf_cart.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimePeriod.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimeSlot.js');

		$sendAnalytics = true;
		if ($this->getLayout() == 'rating') {
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.rating.pack.js');
			$sendAnalytics =false;
		}

//		if ($this->getLayout() == 'ratings') {
//			$items = $this->get('ItemsRating');
//			$pagination	= $this->get('PaginationRatings');
//			$sendAnalytics =false;
//		}

		$this->items = $items;
		$this->pagination = $pagination;

		$merchants = array();
		$merchants[] = $item->MerchantId;
		$analyticsEnabled = $this->checkAnalytics();
		$criteoConfig = null;
		if(BFCHelper::getString('layout', "default") == "default") {
			
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
			if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
				$document->addScript('//static.criteo.net/js/ld/ld.js');
				$document->addScriptDeclaration('
				window.criteo_q = window.criteo_q || [];');
				if($item->IsCatalog) {
					$document->addScriptDeclaration('
					window.criteo_q.push( 
						{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "viewItem", item: "'. $criteoConfig->merchants[0] .'" }
					);');
				}
			}
		}
			if($sendAnalytics &&  $analyticsEnabled && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
				$obj = new stdClass;
				$obj->id = "" . $item->ResourceId . " - Resource";
				$obj->name = $item->Name;
				$obj->category = $item->MerchantCategoryName;
				$obj->brand = $item->MerchantName;
				$obj->variant = $item->IsCatalog ? 'CATALOG' : 'NS';
				$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
			}
		}
		

		$this->analyticsEnabled = $analyticsEnabled;
		$this->criteoConfig = $criteoConfig;
		$this->setBreadcrumb($item, 'resources', $language);
				
		parent::display($tpl);
	}

	function setBreadcrumb($resource, $layout = '', $language) {
		if (!empty($resource)){
				$mainframe = JFactory::getApplication();
				$pathway   = $mainframe->getPathway();
				$items   = $pathway->getPathWay();
				$count = count($items);
				$newPathway = array();
				if($count>1){
					$newPathway = array_pop($items);
				}
				$pathway->setPathway($newPathway);

				$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

				$pathway->addItem(
					$resource->MerchantName,
					JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName))
				);
				$pathway->addItem(
					$resourceName,
					JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName))
				);
		}
	}

}
