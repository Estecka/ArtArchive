<?php
class TagDTO{
	public $id;
	public $slug;
	public $name;
	public $description;
	public $categoryId;

	static public function CreateFrom(array $object) : TagDTO {
		$tag = new TagDTO();
		foreach($object as $key => $value)
			if ($value !== 0 && empty($value))
				$object[$key] = null;
		
		$tag->id 	= value($object['id']);
		$tag->slug 	= value($object['slug']);
		$tag->name 	= value($object['name']);
		$tag->description = value($object['description']);
		$tag->categoryId = value($object['categoryId']);
		return $tag;
	}

	public function GetName() : string {
		return $this->name ?? $this->slug;
	}

}
?>