<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
/**
 * 
 */
class JFormFieldMasterTypologies extends JFormFieldList
{

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$masterTypologies = BFCHelper::getMasterTypologies();
		$options = array();
		if ($masterTypologies)
		{
		foreach($masterTypologies as $masterTypology)
			{
				$options[] = JHtml::_('select.option', $masterTypology->MasterTypologyId, BFCHelper::getLanguage($masterTypology->Name, $language));
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
