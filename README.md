PHP class for generating thumbnails
===================================

This is a simple straight-forward PHP class for generating thumbnails. This class will always keep the aspect ratio of your image. 

This is based on GD which on most servers is built in to PHP so there is no need for installing any PHP extensions.

Usage Examples:
---------------
Include the image class
```php
require_once("image.php");
```

Resize an image to a maximum width and height
```php
$img = new Image("C:\image.png");
$img->resize(120, 120);
$img->save();
```

Resize an image to a maximum width
```php
$img = new Image("C:\image.png");
$img->resize(120);
$img->save();
```

Resize an image to a maximum height
```php
$img = new Image("C:\image.png");
$img->resize(null, 120);
$img->save();
```

Save a resized image to another location
```php
$img = new Image("C:\image.png");
$img->resize(120, 120);
$img->save("C:\image-resized.png");
```
