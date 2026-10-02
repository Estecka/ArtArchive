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
		case "gif" :
		case "jpeg" :
		case "jpg" :
		case "png" :
		case "webp" :
			return EMedia_image;
		
		case "mp3":
		case "m4a":
		case "ogg":
		case "wav":
			return EMedia_audio;
		
		case "avi":
		case "mp4":
		case "mkv":
		case "ogv":
		case "webm":
		case "wmv":
			return EMedia_video;
		
		case "htm":
		case "html":
		case "pdf":
		case "txt":
			return EMedia_iframe;
	}
}
?>
