<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package pct_image_crop
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'PCT\ImageCrop\Core' 					=> 'system/modules/pct_image_crop/PCT/ImageCrop/Core.php',
	'PCT\ImageCrop\Backend\TableContent' 	=> 'system/modules/pct_image_crop/PCT/ImageCrop/Backend/TableContent.php',
	'PCT\ImageCrop\Backend\Helper' 			=> 'system/modules/pct_image_crop/PCT/ImageCrop/Backend/Helper.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_pct_image_crop_canvas' 		=> 'system/modules/pct_image_crop/templates',
	'be_js_pct_image_crop_backend' 	=> 'system/modules/pct_image_crop/templates',
));
