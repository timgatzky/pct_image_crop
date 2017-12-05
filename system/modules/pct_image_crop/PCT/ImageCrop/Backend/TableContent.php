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
		
	 	// add submit on change
	 	$GLOBALS['TL_DCA'][$objDC->table]['fields']['size']['eval']['submitOnChange'] = true;
	 	
	 	$arrSize = deserialize($objDC->activeRecord->size);
	 	
	 	if(!is_array($arrSize))
	 	{
		 	return;
	 	}
	 	
	 	if(!in_array($arrSize[2], $GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
	 	{
		 	return;
	 	}
	 	
	 	$strField = 'pct_image_crop_data';
	 	$strfield_base64 = 'pct_image_crop_data_base64';
	 	$strTarget = 'singleSRC';
	 	
	 	unset($GLOBALS['TL_DCA'][$objDC->table]['fields']['size']['eval']['rgxp']);
	 	
	 	// load the image crop palette
	 	$GLOBALS['TL_DCA'][$objDC->table]['palettes'][$objDC->activeRecord->type] = str_replace('singleSRC', 'singleSRC;{pct_image_crop_legend},'.$strField.';', $GLOBALS['TL_DCA'][$objDC->table]['palettes'][$objDC->activeRecord->type]);
	
	 	// save value
	 	if(\Input::post($strField) != '' && \Input::post($strfield_base64) != '')
	 	{
		 	$arrData = json_decode(\Input::post($strField),true);
		 	$arrData_base64 = explode(',', \Input::post($strfield_base64));
						 	
		 	$objFile = \FilesModel::findByPk( $objDC->activeRecord->{$strTarget} );
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
			 	}
			 	// add new image path to the set data
			 	$arrData['src'] = $strImage;
		 	}
			
		 	\Database::getInstance()->prepare("UPDATE ".$objDC->table." %s WHERE id=?")->set( array($strField => json_encode( array_filter($arrData) )) )->execute($objDC->id);
		}
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
		$strTarget = 'singleSRC';
		$arrFieldDef = $GLOBALS['TL_DCA'][$objDC->table]['fields'][$strField];
		
		$arrSize = deserialize($objDC->activeRecord->{$arrFieldDef['eval']['sizeField']});
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
		
		$objFileModel = \FilesModel::findByPk($objDC->activeRecord->{$strTarget});
		$objFile = new \File($objFileModel->path);
		$strImageSrc = \Image::get($objFileModel->path,$arrSize[0],$arrSize[1],$arrSize[2]);
		$strImage = \Image::getHtml($strImageSrc);
		
		$objTemplate = new \BackendTemplate('be_pct_image_crop_canvas');
		$objTemplate->setData($objDC->activeRecord->row());
		$objTemplate->objDataContainer = $objDC;
		$objTemplate->activeRecord = $objDC->activeRecord;
		$objTemplate->name = $strField;
		$objTemplate->name_base64 = $strField.'_base64';
		$objTemplate->image = $strImage;
		$objTemplate->mime = $objFile->__get('mime');
		$objTemplate->rounded_data = json_encode($objData);
		$objTemplate->data = $objDC->activeRecord->{$strField} ?: '';
		$objTemplate->fieldDef = $arrFieldDef;
		$objTemplate->lang = $GLOBALS['TL_LANG']['PCT_IMAGE_CROP'];
		$objTemplate->ratio = $numRatio;
		
		return  \Controller::replaceInsertTags($objTemplate->parse());
	}
}