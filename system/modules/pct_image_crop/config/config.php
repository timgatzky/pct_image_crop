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
 * Config
 */
$GLOBALS['PCT_IMAGE_CROP']['defaultCanvasSize'] = array(740);
$GLOBALS['PCT_IMAGE_CROP']['supportedCeTypes'] = array('image');

// insert a free format selection
if(!is_array($GLOBALS['TL_CROP']['free']))
{	
	array_insert( $GLOBALS['TL_CROP'],0, array('free' => array('pct_image_crop')) );
}
else
{
	$GLOBALS['TL_CROP']['free'][] = 'pct_image_crop';
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getContentElement'][] = array('PCT\ImageCrop\Core','getContentElementCallback');