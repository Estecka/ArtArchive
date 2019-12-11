<?php
class RSSBuilder{
	public $xmlVersion = "1.0";
	public $rssVersion = "2.0";
	public $encodage = "iso-8859-1";

	public $title = "RSS Feed";
	public $link = "";
	public $description = "";

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

		$elt = $dom->createElement("link", "http://".URL::Root().$this->link);
		$channel->appendChild($elt);

		$elt = $dom->createElement("description", $this->description);
		$channel->appendChild($elt);
	}
	public function Flush(){
		header("Content-Type: text/rss+xml");
		echo $this->dom->saveXML();
	}

}
?>