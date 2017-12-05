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

if(!is_array($GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
{
	$GLOBALS['PCT_IMAGE_CROP']['cropFormats'] = array('free','16_9','4_3','1_1');
}

// insert a free format selection
if(!is_array($GLOBALS['TL_CROP']['pct_image_crop']))
{	
	array_insert( $GLOBALS['TL_CROP'],0, array('pct_image_crop' => $GLOBALS['PCT_IMAGE_CROP']['cropFormats']));
}
else
{
	$GLOBALS['TL_CROP']['pct_image_crop'][] = $GLOBALS['PCT_IMAGE_CROP']['cropFormats'];
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getContentElement'][] = array('PCT\ImageCrop\Core','getContentElementCallback');