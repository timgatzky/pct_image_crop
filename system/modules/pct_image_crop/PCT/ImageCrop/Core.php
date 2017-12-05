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
namespace PCT\ImageCrop;

/**
 * Class file
 * PCT\ImageCrop\Core
 */ 
class Core extends \Controller
{ 
 	/**
 	 * Update the image source by the cropped image and regenerate the element
 	 * @param object
 	 * @param string
 	 * @param object
 	 *
 	 * called from getContentElement Hook
 	 */
 	public function getContentElementCallback($objRow,$strBuffer,$objElement)
 	{
	 	if( !in_array($objRow->type,$GLOBALS['PCT_IMAGE_CROP']['supportedCeTypes']) )
	 	{
		 	return $strBuffer;
	 	}
	 	
	 	if($objElement->type == 'alias' && $objElement->cteAlias)
	 	{
		 	$intId = $objElement->cteAlias;
	 	}
	 	
	 	$arrSize = deserialize($objRow->size);
	 	$objData = json_decode($objRow->pct_image_crop_data);
	 
	 	if($arrSize[2] != 'pct_image_crop' || strlen($objData->src) < 1)
	 	{
		 	return $strBuffer;
	 	}
	 	
	 	if(!file_exists(TL_ROOT.'/'.$objData->src))
	 	{
		 	return $strBuffer;
	 	}
	 	
	 	// remember original upload path
	 	$strUploadPath = \Config::get('uploadPath');
	 	$arrAssetsPath = explode('/',dirname($objData->src));
	 	$strAssetsPath = $arrAssetsPath[0];
	 	if(empty($strAssetsPath))
	 	{
		 	$strAssetsPath = 'assets';
	 	}
	 	
	 	//-- trick contao and set the upload path to the cached assets folder
	 	\Config::set('uploadPath','assets');
	 	// add the file to file database
	 	$objFile = \Dbafs::addResource($objData->src);
	 	// update the element source to the file
	 	$objElement->singleSRC = $objFile->uuid;
	 	// regenerate the element with the new data
	 	$strBuffer = $objElement->generate();
	 	
	 	// reset the upload path to its original path
	 	\Config::set('uploadPath',$strUploadPath);
	 	//--
	 		 	
	 	return $strBuffer;
 	}
}