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
$GLOBALS['TL_DCA']['tl_pct_customelement_attribute']['config']['onsubmit_callback'][] = array('PCT\ImageCrop\Backend\TableCustomElementAttribute', 'onSubmitCallback');
