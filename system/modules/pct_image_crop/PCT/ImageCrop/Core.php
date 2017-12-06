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
	 	
	 	// load the data container
		if(!$GLOBALS['loadDataContainer']['tl_content'])
		{
			\Controller::loadDataContainer('tl_content');
		}
	 	
	 	if($objElement->type == 'alias' && $objElement->cteAlias)
	 	{
		 	$intId = $objElement->cteAlias;
	 	}
	 	
	 	$strField = 'pct_image_crop_data';
	 	$arrFieldDef = $GLOBALS['TL_DCA']['tl_content']['fields'][$strField];
		$strSourceField = $arrFieldDef['eval']['cropper']['sourceField'];
	 	$strSizeField = $arrFieldDef['eval']['cropper']['sizeField'];
	 	
	 	$arrSize = deserialize($objRow->{$strSizeField});
	 	$objData = json_decode($objRow->{$strField});
	 
	 	if(!in_array($arrSize[2], $GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
	 	{
		 	return;
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
	 	#\Config::set('uploadPath','assets');
	 	// add the file to file database
	 	$objFile = \Dbafs::addResource($objData->src);
	 	// update the element source to the file
	 	$objElement->{$strSourceField} = $objFile->uuid;
	 	// regenerate the element with the new data
	 	$strBuffer = $objElement->generate();
	 	
	 	// reset the upload path to its original path
	 	#\Config::set('uploadPath',$strUploadPath);
	 	//--
	 		 	
	 	return $strBuffer;
 	}
 	
 	
	/**
	 * Render the image crop field
	 * @param object
	 *
	 * called input_field_callback
	 */	
	public function renderImageCropCanvas($objDC)
	{
		if(!$objDC->activeRecord->singleSRC)
		{
			return 'No image selected';
		}
		
		// load the data container
		if(!$GLOBALS['loadDataContainer'][$objDC->table])
		{
			\Controller::loadDataContainer($objDC->table);
		}
		
		$strField = $objDC->field;
		$arrFieldDef = $GLOBALS['TL_DCA'][$objDC->table]['fields'][$strField];
		$strSourceField = $arrFieldDef['eval']['cropper']['sourceField'];
	 	$strSizeField = $arrFieldDef['eval']['cropper']['sizeField'];

		$arrSize = deserialize($objDC->activeRecord->{$strSizeField});
		if(!is_array($arrSize))
		{
			$arrSize = array('','','pct_free');
		}
		
		$strRatio = str_replace('pct_','',$arrSize[2]);
		$numRatio = 0;
		if($strRatio != 'free')
		{
			$_ratio = explode('_', $strRatio);
			$numRatio = $_ratio[0] / $_ratio[1];
		}
		
		$objFileModel = \FilesModel::findByPk($objDC->activeRecord->{$strSourceField});
		$objFile = new \File($objFileModel->path);
		$strImageSrc = \Image::get($objFileModel->path,null,null);
		$strImage = \Image::getHtml($strImageSrc);
		
		$objTemplate = new \BackendTemplate('be_pct_image_crop_canvas');
		$objTemplate->setData($objDC->activeRecord->row());
		$objTemplate->objDataContainer = $objDC;
		$objTemplate->activeRecord = $objDC->activeRecord;
		$objTemplate->file = $objFile;
		$objTemplate->name = $strField;
		$objTemplate->name_base64 = $strField.'_base64';
		$objTemplate->image = $strImage;
		$objTemplate->mime = $objFile->__get('mime');
		$objTemplate->rounded_data = json_encode($objData);
		$objTemplate->data = $objDC->activeRecord->{$strField} ?: '';
		$objTemplate->fieldDef = $arrFieldDef;
		$objTemplate->lang = $GLOBALS['TL_LANG']['PCT_IMAGE_CROP'];
		$objTemplate->ratio = $numRatio;
		
		$offset = 10;
		$arrStyles = array
		(
			 'max-height:'.($objFile->viewHeight + $offset) . 'px;',
			 'max-width:'.($objFile->viewWidth + $offset) . 'px;'
		);
		
		$objTemplate->styles = $arrStyles;
		$objTemplate->canvas_styles = implode(' ', $arrStyles);
		
		return  \Controller::replaceInsertTags($objTemplate->parse());
	}
}