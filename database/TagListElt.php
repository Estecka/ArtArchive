<?php
class TagListElt {
	public $slug;
	public $tagged;

	static public function CreateFrom($object) : TagListElt {
		$tag = new TagListElt();
		$tag->slug 		= value($object['slug']);
		$tag->enabled 	= (bool)value($object['enabled']);
		return $tag;
	}
}
?>