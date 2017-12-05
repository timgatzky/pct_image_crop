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
$GLOBALS['PCT_IMAGE_CROP']['supportedCeTypes'] = array('image');

if(!is_array($GLOBALS['PCT_IMAGE_CROP']['cropFormats']))
{
	$GLOBALS['PCT_IMAGE_CROP']['cropFormats'] = array('pct_free','pct_16_9','pct_4_3','pct_1_1');
}

// insert a imace crop selections
array_insert( $GLOBALS['TL_CROP'],0, array('pct_image_crop' => $GLOBALS['PCT_IMAGE_CROP']['cropFormats']));

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getContentElement'][] 		= array('PCT\ImageCrop\Core','getContentElementCallback');
$GLOBALS['TL_HOOKS']['parseWidget'][] 		= array('PCT\ImageCrop\Backend\Helper','parseWidgetCallback');