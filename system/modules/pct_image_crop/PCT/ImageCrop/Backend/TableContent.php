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
 * TableContent
 */ 
class TableContent extends \Backend
{ 
 	/**
 	 * Modify the DCA
 	 * @param object
 	 */
 	public function modifyDca($objDC)
 	{
	 	if($objDC->activeRecord === null)
	 	{
		 	$objDC->activeRecord = \ContentModel::findByPk($objDC->id);
	 	}
		
		// load the data container
		if(!$GLOBALS['loadDataContainer'][$objDC->table])
		{
			\Controller::loadDataContainer($objDC->table);
		}
		
		$strField = 'pct_image_crop_data';
	 	$arrFieldDef = $GLOBALS['TL_DCA'][$objDC->table]['fields'][$strField];
		$strfield_base64 = 'pct_image_crop_data_base64';
	 	$strSourceField = $arrFieldDef['eval']['cropper']['sourceField'];
	 	$strSizeField = $arrFieldDef['eval']['cropper']['sizeField'];
	 	
	 	$arrSize = deserialize($objDC->activeRecord->{$strSizeField});
		if(!is_array($arrSize))
	 	{
		 	return;
	 	}
	 	
	 	if(!in_array($arrSize[2], $GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
	 	{
		 	return;
	 	}
	 		 	
	 	unset($GLOBALS['TL_DCA'][$objDC->table]['fields'][$strSizeField]['eval']['rgxp']);
	 	
	 	// load the image crop palette
	 	$GLOBALS['TL_DCA'][$objDC->table]['palettes'][$objDC->activeRecord->type] = str_replace($strSourceField, 'singleSRC;{pct_image_crop_legend},'.$strField.';', $GLOBALS['TL_DCA'][$objDC->table]['palettes'][$objDC->activeRecord->type]);
	
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
		 	
		 	$objFile = \FilesModel::findByPk( $objDC->activeRecord->{$strSourceField} );
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
			
		 	\Database::getInstance()->prepare("UPDATE ".$objDC->table." %s WHERE id=?")->set( array($strField => json_encode( array_filter($arrData) )) )->execute($objDC->id);
		}
	}
}