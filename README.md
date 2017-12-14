pct_image_crop
================

About
-----
Crop an image directly in the content element using the copperjs script (https://fengyuanchen.github.io/cropper/)

Installation
------------
- Clear the internal cache
- Copy the module folder to /system/modules, update database 
- A public folder will be created in the files folder (/files/pct_image_crop)

Installation Contao 4
------------
- Clear symphony cache: delete all folders in /var/cache
- Clear symlinks in /web folder: delete all folders within the /web folder
- Copy the module folder to /system/modules
- Open install tool: Update database also rebuilds symlinks to modules in /web folder
- A public folder will be created in the files older (/files/pct_image_crop)
- System maintenance: Rebuild symlinks (rebuilds symlinks to /files folder)

Usage
-----
- Choose "PCT Image Crop" as image size option in regular image or text contao content elements
- PCT CustomElements: Check the "Image crop" option and "Size" option for the image attribute
- The cropped images will be in the /files/pct_image_crop folder