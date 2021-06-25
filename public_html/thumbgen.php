<?php
if (!array_key_exists('path', $_GET)) {
	http_response_code(400);
	die;
}

define("__ROOT__", __DIR__."/../");
require_once __ROOT__."MediaType.php";
require_once __ROOT__."URL.php";

// echo "ThumbGen.exe";
// var_dump($_GET);

$uri = $_GET['path'];

$type = GetMediaType($uri);
if ($type != EMedia_image) {
	http_response_code();
	die;
}

function	GetPath(string $uri) : string {
	// urldecode replaces special url symbols with their literals (e.g: %20 to spaces)
	// This may however replace '+' with spaces. Which I'd like to preserve eventually.
	$uri = urldecode($uri);
	if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
		$uri = str_replace("/", "\\", $uri);
	return __DIR__.$uri;
}


$src = GetPath("/storage/$uri");
$dst = $src;

// echo $dst;
// exit;

header("Content-type: image/jpg");
readfile($dst);
?>
