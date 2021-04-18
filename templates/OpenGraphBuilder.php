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
			
		$this->PrintMeta("og:site_name",   $this->siteName    );
		$this->PrintMeta("og:title",       $this->title       );
		$this->PrintMeta("og:description", $this->description );
		$this->PrintMeta("og:url",         $this->url         );
		$this->PrintMeta("og:type",        $type              );
		foreach($this->audioMedia as $url)
			$this->PrintMeta("og:audio",    $url          );
		foreach($this->imageMedia as $url){
			$this->PrintMeta("og:image",    $url          );
			$this->PrintMeta("og:image:alt", $this->title );
		}
	}
	
	private function FlushTwitter() : void{
		$this->PrintMeta("twitter:site",        "@artarchive"      );
		$this->PrintMeta("twitter:title",       $this->title       );
		$this->PrintMeta("twitter:description", $this->description );
		$this->PrintMeta("twitter:url",         $this->url         );
		if ($this->hasImage)
			$this->PrintMeta("twitter:card",  "summary_large_image" );
		foreach($this->imageMedia as $url){
			$this->PrintMeta("twitter:image", $url                  );
		}

	}

	private function PrintMeta(string $property, ?string $content) {
		?>
		<meta property="<?=$property?>" content="<?=$content?>" />
		<?php
	}
}
?>
