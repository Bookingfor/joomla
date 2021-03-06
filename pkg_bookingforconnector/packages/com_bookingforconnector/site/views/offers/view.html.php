<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewOffers extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{

		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();		

		// Initialise variables
		$state		= $this->get('State');
		$params = $state->params;
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
//		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
//		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		
		$listNameAnalytics =6;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";
		$analyticsEnabled = count($items) > 0 && $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		if ($analyticsEnabled) {
						$listName = "Offers List";
						$type = "Offer";
						$itemType = 1;
						$allobjects = array();

						foreach ($items as $key => $value) {
							$obj = new stdClass;
							$obj->id = "" . $value->VariationPlanId . " - " . $type;
							$obj->name = $value->Name;
							$obj->brand = $value->MrcName;
							$obj->position = $key;
							$allobjects[] = $obj;
						}
						$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');					
		    
		}


		$this->state = $state;
		$this->language = $language;
		$this->params = $params;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->listNameAnalytics = $listNameAnalytics;
		
		// Display the view
		parent::display($tpl);
	}
}
