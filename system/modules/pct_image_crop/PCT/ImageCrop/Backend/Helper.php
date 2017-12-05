<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2017
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_image_crop
 * @link		http://contao.org
 */
 
/**
 * Namespace
 */ 
namespace PCT\ImageCrop\Backend;

/**
 * Class file
 * Helper
 */ 
class Helper extends \Controller
{ 
 	/**
 	 * Modify the DCA
 	 * @param object
 	 */
 	public function parseWidgetCallback($strBuffer,$objWidget)
 	{
		if($objWidget->type != 'imageSize')
		{
			return $strBuffer;
		}
		
		$objHelperJs = new \BackendTemplate('be_js_pct_image_crop_backend');
		$objHelperJs->widget = $objWidget;
		$objHelperJs->table = \Input::get('table');
		
		$arrSize = deserialize($objWidget->value);
		if(in_array($arrSize[2], $GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
	 	{
		 	$objHelperJs->isCropper = true;
	 	}
	 	
	 	return $strBuffer . $objHelperJs->parse();
	}
}