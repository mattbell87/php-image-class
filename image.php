<?php

/*****

Simple GD based Image Class
Matt Bell 2015

Usage Example:
$img = new Image("C:\image.png");
$img->resize(400, 400);
$img->save(); //or $img->save("C:\image-resized.png");

******/

class Image
{
	private $path;
	private $pathinfo;
	private $name;
	private $img;

	//Open the image
	function __construct($path)
	{
		$this->path = $path;
		$this->pathinfo = pathinfo($path);
		$this->name = isset($this->pathinfo['basename']) ? $this->pathinfo['basename'] : '';

		if(!file_exists($path))
			throw(new Exception("Image file not found: \"$this->name\""));

		$this->gdInit();
		$this->img = call_user_func($this->gdLoad, $path);

		if (!$this->img)
			throw(new Exception("\"$this->name\" does not appear to be a valid image."));
	}

	//Resize the image
	function resize($maxwidth, $maxheight = null)
	{
		if (!is_int($maxwidth) && !is_int($maxheight))
		{
			throw(new Exception("At least one dimension is needed for resizing: \"$this->name\""));
			return false;
		}

		$size = GetImageSize($this->path);
		$sizex = $size[0];
		$sizey = $size[1];

		//Find the largest dimension
		$sizexlargest = ($sizex > $sizey);

		//If null is specified in one dimension we want to scale until the other dimension is reached
		$sizexlargest = $maxheight == null ? true : $sizexlargest;
		$sizexlargest = $maxwidth == null ? false : $sizexlargest;

		$factor = 0;

		if ($sizexlargest)
			$factor = (float)$maxwidth/$sizex;
		else
			$factor = (float)$maxheight/$sizey;

		//Set new dimensions
		$width = $factor*$sizex;
		$height = $factor*$sizey;

		//Create a base image
		$newimg = ImageCreateTrueColor($width, $height);

		imagealphablending( $newimg, false );
		imagesavealpha( $newimg, true );

		//Copy and scale the data
		if(!imagecopyresampled($newimg, $this->img, 0, 0, 0, 0, $width, $height, $sizex, $sizey))
			throw(new Exception("Could not resize the image: \"$this->name\""));

		$this->img = $newimg;

	}

	//Save the image, if path isn't specified the original is overwritten
	function save($path = null)
	{
		if ($path != null)
			$this->path = $path;

		$this->img = call_user_func($this->gdSave, $this->img, $this->path);

		if (!$this->img)
			throw(new Exception("Could not save \"$this->name\" to the given path."));
	}

	//Set up the GD functions based on the file extension
	private function gdInit()
	{
		$ext = isset($this->pathinfo['extension']) ? $this->pathinfo['extension'] : '';

		switch ($ext)
		{
			case 'jpeg':
			$this->gdLoad = 'ImageCreateFromJPEG';
			$this->gdSave = 'ImageJPEG';
			break;

			case 'png':
			$this->gdLoad = 'ImageCreateFromPNG';
			$this->gdSave = 'ImagePNG';
			break;

			case 'bmp':
			$this->gdLoad = 'ImageCreateFromBMP';
			$this->gdSave = 'ImageBMP';
			break;

			case 'gif':
			$this->gdLoad = 'ImageCreateFromGIF';
			$this->gdSave = 'ImageGIF';
			break;

			case 'xbm':
			$this->gdLoad = 'ImageCreateFromXBM';
			$this->gdSave = 'ImageXBM';
			break;

			default:
			$this->gdLoad = 'ImageCreateFromJPEG';
			$this->gdSave = 'ImageJPEG';
		}
	}
}

?>
