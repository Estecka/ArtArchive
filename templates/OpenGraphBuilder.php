<?php

class OpenGraphBuilder {
	public $siteName = "ArtArchive";
	public $title = "Untitled";
	public $description = "";
	public $url;

	public $media = array();

	private $hasImage;
	private $hasAudio;

	public function __construct() {
		$this->url = URL::Root();
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
	}
	
	private function FlushTwitter() : void{
		$this->PrintMeta("twitter:site",        "@artarchive"      );
		$this->PrintMeta("twitter:title",       $this->title       );
		$this->PrintMeta("twitter:description", $this->description );
		$this->PrintMeta("twitter:url",         $this->url         );
		if ($this->hasImage)
			$this->PrintMeta("twitter:card", "summary_large_image");

	}

	private function PrintMeta(string $property, string $content) {
		?>
		<meta property="<?=$property?>" content="<?=$content?>" />
		<?php
	}
}
?>
