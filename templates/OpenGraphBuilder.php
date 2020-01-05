<?php

class OpenGraphBuilder {
	public $siteName = "ArtArchive";
	public $title = "Untitled";
	public $description = "";
	public $url = "";

	private $imageMedia = array();
	private $audioMedia = array();
	private $hasImage = false;
	private $hasAudio = false;

	public function __construct() {
		$this->url = URL::Root();
	}

	public function	AddMedia(string $path) : void {
		$type = GetMediaType($path);
		$path = URL::Absolute(URL::Media($path));

		if ($type === EMedia_image) 
		{
			$this->hasImage = true;
			$this->imageMedia[] = $path;
		}
		else if ($type === EMedia_audio) 
		{
			$this->hasAudio = true;
			$this->audioMedia[] = $path;
		}
	}

	public function Flush() : void {
		$this->FlushOG();
		$this->FlushTwitter();
	}

	private function FlushOG() : void {
		if ($this->hasAudio)
			$type = "music.song";
		else if ($this->hasImage)
			$type = "photo";
		else
			$type = "website";
			
		$this->PrintProperty("og:site_name",   $this->siteName    );
		$this->PrintProperty("og:title",       $this->title       );
		$this->PrintProperty("og:description", $this->description );
		$this->PrintProperty("og:url",         $this->url         );
		$this->PrintProperty("og:type",        $type              );
		foreach($this->audioMedia as $url)
			$this->PrintProperty("og:audio",    $url          );
		foreach($this->imageMedia as $url){
			$this->PrintProperty("og:image",    $url          );
			$this->PrintProperty("og:image:alt", $this->title );
		}
	}
	
	private function FlushTwitter() : void{
		$this->PrintName("twitter:site",        "@artarchive"      );
		$this->PrintName("twitter:title",       $this->title       );
		$this->PrintName("twitter:description", $this->description );
		$this->PrintName("twitter:url",         $this->url         );
		if ($this->hasImage)
			$this->PrintName("twitter:card",  "summary_large_image" );
		foreach($this->imageMedia as $url){
			$this->PrintName("twitter:image", $url                  );
		}

	}

	private function PrintProperty(string $property, ?string $content) {
		?>
		<meta property="<?=$property?>" content="<?=$content?>" />
		<?php
	}
	private function PrintName(string $name, ?string $content) {
		?>
		<meta name="<?=$name?>" content="<?=$content?>" />
		<?php
	}
}
?>
