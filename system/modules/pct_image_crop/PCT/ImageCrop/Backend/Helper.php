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
	
	
	/**
 	 * Modify the DCA
 	 * @param object
 	 * @return string||void
 	 */
 	public function CE_parseWidgetCallback($objWidget,$strField,$arrFieldDef,$objDC)
 	{
	 	if($GLOBALS['PCT_CUSTOMELEMENTS']['rekursive'][$strField] === true)
		{
			unset($GLOBALS['PCT_CUSTOMELEMENTS']['rekursive'][$strField]);
			return;
		}
		
		// check if size field is a cropper format
		$arrWizard = \PCT\CustomElements\Core\Vault::getWizardData($objDC->id,$objDC->table);
		$arrSize = $arrWizard['values'][$strField.'_size'];
		
		if(!in_array($arrSize[2], $GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
	 	{
		 	return;
	 	}
		
		$objAttribute = null;
		
		// CustomCatalog
		if( in_array('pct_customelements_plugin_customcatalog', \Config::getInstance()->getActiveModules()) )
		{
			if(\PCT\CustomElements\Plugins\CustomCatalog\Core\CustomCatalogFactory::validateByTableName($objDC->table))
			{
				$objDC->isCustomCatalog = true;
			}
		}
		
		if($objDC->isCustomCatalog)
		{
			$objAttribute = \PCT\CustomElements\Plugins\CustomCatalog\Core\AttributeFactory::findByCustomCatalog($strField,$objDC->table);
		}
		else
		{		
			$objAttribute = \PCT\CustomElements\Core\AttributeFactory::findByUuid($strField);
			// support duplicated attributes here
			if($objAttribute === null)
			{
				$arrWizard = \PCT\CustomElements\Core\Vault::getWizardData($objDC->id,$objDC->table);
				if(is_array($arrWizard['clones']))
				{
					foreach($arrWizard['clones'] as $attr_id => $arrUuids)
					{
						if(empty($arrUuids) || !is_array($arrUuids))
						{
							continue;
						}
						
						if(in_array($strField, $arrUuids))
						{
							$objAttribute = \PCT\CustomElements\Core\AttributeFactory::findById($attr_id);
							break;
						}
					}
				}
			}
		}
		
		if($objAttribute === null)
		{
			return; // bypass hook return
		}
		
		$objChildWidget = new \FormHidden();
		$objChildWidget->name = $strField.'_cropperdata';
		$objAttribute->addChildAttribute($objChildWidget);
		
		// avoid rekursive calls. Use a simple global to test if the attribute has been reparsed
		$GLOBALS['PCT_CUSTOMELEMENTS']['rekursive'][$strField] = true;
		
		// generate the widget again
		$strBuffer = $objAttribute->generateWidget($objDC);
		
		// create new cropper core instance
		// @var \PCT\ImageCrop\Core
		$objCropper = new \PCT\ImageCrop\Core;
		$_objDC = clone($objDC);
		
		if(!isset($arrFieldDef['eval']['cropper']))
		{
			$_objDC->cropper = array
			(
				'sizeField' 	=> $strField.'_size',
				'sourceField'	=> $strField,
				'selector'		=> 'pct_image_crop_canvas_'.$strField
			);
		}
		
		$_objDC->field = $strField.'_cropperdata';
		$_objDC->activeRecord->{$strField} = $objWidget->value;
		$_objDC->activeRecord->{$strField.'_size'} = $arrSize;
		$_objDC->activeRecord->{$strField.'_cropperdata'} = $objChildWidget->value;
		
		// render image crop canvas
		$strBuffer .= $objCropper->renderImageCropCanvas($_objDC);
		
		$strField = $_objDC->field;
		$strfield_base64 = $_objDC->field.'_base64';
	 	$strSourceField = $strField;
	 	
	 	// save value
	 	if(\Input::post($strField) != '' && \Input::post($strfield_base64) != '')
	 	{
		 	$arrData = json_decode(\Input::post($strField),true);
		 	$arrData_base64 = explode(',', \Input::post($strfield_base64));
		 		
		 	// round the data
		 	if(is_array($arrData))
		 	{
			 	$tmp = array();
			 	foreach($arrData as $k => $v)
			 	{
				 	if(is_numeric($v))
				 	{
				 		$v = round($v,2);
				 	}
				 	$tmp[$k] = $v;
			 	}
			 	$arrData = $tmp;
			 	unset($tmp);
			 }
		 	
		 	$objFile = \FilesModel::findByPk( $objWidget->value );
		 	if($objFile !== null)
		 	{
		 	 	// process image to get a cached string path
			 	$strImage = \Image::get($objFile->path,$arrData['width'],$arrData['height'],'crop');;
			 	// write the base64 data to the file
			 	if(file_exists(TL_ROOT.'/'.$strImage))
			 	{
			 	 	$objFile = new \File($strImage);
				 	$objFile->write( base64_decode($arrData_base64[1]) );
				 	$objFile->close();
				 	
				 	// move file to cropper directory
				 	$strUploadPath = \Config::get('uploadPath');
				 	$strNewPath = $strUploadPath.'/'.$GLOBALS['PCT_IMAGE_CROP']['filesPath'].'/'.$objFile->path;
					
				 	$arrAssetsPath = explode('/',dirname($objFile->path));
				 	$strAssetsPath = $arrAssetsPath[0];
				 	if(empty($strAssetsPath))
				 	{
					 	$strAssetsPath = 'assets';
				 	}
				 	\Config::set('uploadPath',$strAssetsPath);
				 	
				 	$objFile->copyTo( $strUploadPath.'/'.$GLOBALS['PCT_IMAGE_CROP']['filesPath'].'/'.$objFile->path);
				 	\Config::set('uploadPath',$strUploadPath);
				 	
				 	// add new image path to the set data
			 	 	$arrData['src'] = $strNewPath;
			 	}
			 	
			 	// add assets image path
			 	$arrData['tmp_src'] = $strImage;
		 	}
		 	
			\Input::setPost($strField,json_encode($arrData));
		}
		
	 	return $strBuffer;
	}
}