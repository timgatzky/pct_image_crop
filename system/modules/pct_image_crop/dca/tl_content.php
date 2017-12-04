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
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('PCT\ImageCrop\Backend\TableContent', 'modifyDca');

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['pct_image_crop_data'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['pct_image_crop_data'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'clr'),
	'input_field_callback'	  => array('PCT\ImageCrop\Backend\TableContent','renderImageCropCanvas'),
	'sql'					  => "tinytext NULL",
	'cropperjs'				  => array
	(
		'ratios' => array('free','16:9','4:3')
	),
);
