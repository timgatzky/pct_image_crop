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

\System::loadLanguageFile('tl_pct_customelement_attribute',$GLOBALS['TL_LANGUAGE'],true);

/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_pct_customelement_attribute']['config']['onsubmit_callback'][] = array('PCT\ImageCrop\Backend\TableCustomElementAttribute', 'onSubmitCallback');

/**
 * Add Cropper option for image attributes
 */
$GLOBALS['TL_DCA']['tl_pct_customelement_attribute']['fields']['options']['image']['options'][] = 'cropperdata';
$GLOBALS['TL_DCA']['tl_pct_customelement_attribute']['fields']['options']['image']['reference']['cropperdata'] = $GLOBALS['TL_LANG']['tl_pct_customelement_attribute']['options']['image']['cropperdata'];