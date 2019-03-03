<?php
class TagListElt {
	/** @var string */
	public $slug;
	/** @var bool */
	public $enabled;

	static public function CreateFrom($object) : TagListElt {
		$tag = new TagListElt();
		$tag->slug 		= value($object['slug']);
		$tag->enabled 	= (bool)value($object['enabled']);
		return $tag;
	}
}
?>