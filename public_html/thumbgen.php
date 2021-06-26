<?php
if (!array_key_exists('path', $_GET)) {
	http_response_code(400);
	die;
}

define("__TARGET_PX_SIZE__", 500);
define("__TARGET_QUALITY__", 60);

$uri = $_GET['path'];

function	GetPath(string $uri) : string {
	// urldecode replaces special url symbols with their literals (e.g: %20 to spaces)
	// This may however replace '+' with spaces. Which I'd like to preserve eventually.
	$uri = urldecode($uri);
	if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
		$uri = str_replace("/", "\\", $uri);
	return __DIR__.$uri;
}

function	Fail() {
	http_response_code(500);
	echo "Thumbnail generation failed";
	die;
}

$src = GetPath("/storage/$uri");
$dst = GetPath("/thumbs/$uri");

if (!file_exists($src)) {
	http_response_code(404);
	echo "Image not found";
	die;
}
switch(pathinfo($src, PATHINFO_EXTENSION)){
	default: 
		Fail();
		break;
	case "jpg":
	case "jpeg":
		$image = imagecreatefromjpeg($src);
		break;
	case "png":
		$image = imagecreatefrompng($src);
		break;
	case "gif":
		$image = imagecreatefromgif($src);
		break;
	case "bmp":
		$image = imagecreatefrombmp($src);
		break;
}
if (!$image)
	Fail();

// Compute the dimensions of the thumbnail.
$src_size = getimagesize($src) ?: Fail();
$scale_factor = min(
	__TARGET_PX_SIZE__ / $src_size[0],
	__TARGET_PX_SIZE__ / $src_size[1],
	1,
);
$dst_size = array(
	0 => $src_size[0] * $scale_factor,
	1 => $src_size[1] * $scale_factor,
);

// Perform downscaling
$r = imagecopyresampled($image, $image, 0,0, 0,0, $dst_size[0], $dst_size[1], $src_size[0], $src_size[1]) ?: Fail();
$image = imagecrop($image, array('x'=>0, 'y'=>0, 'width'=>$dst_size[0], 'height'=>$dst_size[1])) ?: Fail();

// Serve the image
header("Content-type: image/jpg");
imagejpeg($image, NULL, __TARGET_QUALITY__);
ob_end_flush();
flush();

// Cache the image
$dir = pathinfo($dst, PATHINFO_DIRNAME);
if (!file_exists($dir))
	mkdir($dir, 0777, true);
imagejpeg($image, $dst, __TARGET_QUALITY__);

?>
