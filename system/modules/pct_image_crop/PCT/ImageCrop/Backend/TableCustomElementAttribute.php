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
 * TableCustomElementAttribute
 */ 
class TableCustomElementAttribute extends \Backend
{ 
 	/**
 	 * Modify the DCA
 	 * @param object
 	 */
 	public function onSubmitCallback($objDC)
 	{
	 	if(!$objDC->activeRecord)
	 	{
		 	$objDC->activeRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);
		}
	 	
	 	if($objDC->activeRecord->type == 'image')
	 	{
		 	$arrOptions = deserialize($objDC->activeRecord->options);
		 	if(!in_array('cropperdata', $arrOptions) && in_array('size', $arrOptions))
		 	{
			 	$arrOptions[] = 'cropperdata';
			 	\Database::getInstance()->prepare("UPDATE ".$objDC->table." %s WHERE id=?")->set( array('options' => $arrOptions) )->execute($objDC->id);
		 	}
	 	}
	 	
	}
}