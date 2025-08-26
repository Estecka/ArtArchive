<?php
const EMedia_undefined = "undefined";
const EMedia_image     = "image";
const EMedia_audio     = "audio";
const EMedia_video     = "video";
const EMedia_iframe    = "iframe";

function	GetMediaType(string $filename) : string {
	switch(pathinfo($filename, PATHINFO_EXTENSION)){
		default:
			return EMedia_undefined;

		case "bmp" :
		case "jpeg" :
		case "jpg" :
		case "png" :
		case "gif" :
		case "webp" :
			return EMedia_image;
		
		case "mp3":
		case "wav":
		case "ogg":
		case "m4a":
			return EMedia_audio;
		
		case "txt":
		case "pdf":
		case "html":
		case "htm":	
			return EMedia_iframe;
	}
}
?>
