<?php
/*

Example image upload and thumbnail generator
--------------------------------------------

Please make sure the the examples folder is writeable on your server

*/

//Include the image class
require_once ('../image.php');

//Enable errors
error_reporting(E_ALL);
ini_set("display_errors", 1);

//Utility function for uploading a file
function UploadFile($fieldname, $saveto, $allowedregex = ".*")
{
	$upload = $_FILES[$fieldname];
	$name = $upload['name'];
	$info = pathinfo($name);

	if (is_dir($saveto))
	{
		$saveto .= "/$name";
	}

	if (preg_match("/$allowedregex/", $name) == 0)
	{
		throw new Exception("File type not allowed!");
		return false;
	}

	if (!move_uploaded_file($upload['tmp_name'], $saveto))
	{
		throw new Exception("Could not upload the file \"$name\". Saving to: $saveto");
		return false;
	}

	return $saveto;
}

$message = "";
$imgpath = "";

//Process a form if there's one waiting
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	try
	{
		//Create the directories if they don't exist
		if (!is_dir('./images'))
			mkdir('./images');
		if (!is_dir('./thumbnails'))
			mkdir('./thumbnails');

		//Upload the file to ./images
		$imgpath = UploadFile('myfile', './images/', ".*\.(?:jpg|png)");

		//Open the image from the uploaded file
		$img = new Image($imgpath);

		//Resize the image
		$img->resize(130,130);

		//Get the thumbnail path (./thumbnails)
		$pathinfo = pathinfo($imgpath);
		$name = $pathinfo['basename'];
		$imgpath = './thumbnails/' . $name;

		//Save the resized image to the thumbnail path
		$img->save($imgpath);

		//Show a success message :)
		$message = "Image uploaded successfully!";
	}
	catch(Exception $e)
	{
		//Give an error
		$message = $e->getMessage();
		$imgpath = "";
	}

}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Upload example</title>
</head>
<body>
	<h1>PHP Image Class</h1>
	<h2>Image upload example</h2>
	<?php
		if ($message != "")
		{
			echo "<p>$message</p>";
		}

		if ($imgpath != "")
		{
			echo "<img src=\"$imgpath\" alt=\"Thumbnail\" />";
		}
	?>

	<form action="" method="post" enctype="multipart/form-data">

		<label for="myfile">File</label>
		<input type="file" name="myfile" id="myfile" />
		<input type="submit" value="Upload" />

	</form>
</body>
</html>
