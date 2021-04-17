<?php
class RSSBuilder{
	public $xmlVersion = "1.0";
	public $rssVersion = "2.0";
	public $encodage = "utf-8";

	public $title = "RSS Feed";
	public $link = "";
	public $description = "";
	public $author = null;

	/** @var DOMDocument **/
	private $dom;
	/** @var DOMElement */
	private $channel;

	public function Init(){
		$this->dom = new DOMDocument($this->xmlVersion, $this->encodage);
		$dom = &$this->dom;
		$dom->formatOutput = true;
		$rss = $dom->createElement("rss");
		$dom->appendChild($rss);

		$attr = $dom->createAttribute("version");
		$attr->value = $this->rssVersion;
		$rss->appendChild($attr);

		$this->channel = $dom->createElement("channel");
		$channel = &$this->channel;
		$rss->appendChild($this->channel);

		$elt = $dom->createElement("title", $this->title);
		$channel->appendChild($elt);

		$elt = $dom->createElement("link", URL::Root().$this->link);
		$channel->appendChild($elt);

		$elt = $dom->createElement("description", $this->description);
		$channel->appendChild($elt);

		if ($this->author == null) {
			$authname = either(ArtArchive::$settings['AuthorName'], null);
			$authemail = either(ArtArchive::$settings['AuthorEmail'], null);
			if($authname && $authemail)
				$this->author = $authname." <".$authemail.">";
			else if ($authname)
				$this->author = $authname;
			else if ($authemail)
				$this->author = $authemail;
		}
	}
	public function Flush(){
		header("Content-Type: application/rss+xml");
		echo $this->dom->saveXML();
	}

	public function AddArtwork(ArtworkDTO $art) : DOMElement{
		$dom = &$this->dom;
		$channel = &$this->channel;

		$item = $dom->createElement("item");
		$channel->appendChild($item);

		$elt = $dom->createElement("title", $art->GetName());
		$item->appendChild($elt);

		$elt = $dom->createElement("link", URL::Root().URL::Artwork($art->slug));
		$item->appendChild($elt);

		$elt = $dom->createElement("guid", $art->slug);
		$item->appendChild($elt);

		$elt = $dom->createElement("pubDate", $art->date);
		$item->appendChild($elt);

		if ($this->author) {
			$elt = $dom->createElement("author", $this->author);
			$item->appendChild($elt);
		}

		if ($art->description) {
			$elt = $dom->createElement("description", $art->description);
			$item->appendChild($elt);
		}

		return $item;
	}
}
?>