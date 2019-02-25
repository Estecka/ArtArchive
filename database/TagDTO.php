<?php
class TagDTO{
	public $id;
	public $slug;
	public $name;
	public $description;

	static public function CreateFrom(array $object) : TagDTO {
		$tag = new TagDTO();
		foreach($object as $key => $value)
			if ($value !== 0 && empty($value))
				$object[$key] = null;
		
		$tag->id 	= value($object['id']);
		$tag->slug 	= value($object['slug']);
		$tag->name 	= value($object['name']);
		$tag->description = value($object['description']);
		return $tag;
	}

	public function GetName() : string {
		return $this->name ?? $this->slug;
	}

}
?>