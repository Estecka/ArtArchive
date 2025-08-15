<?php
class CategoryDTO {
	public $id;
	public $order;
	public $slug;
	public $name;
	public $description;
	public $color;

	static public function Empty() : CategoryDTO {
		$cat = new CategoryDTO();
		$cat->id = -1;
		$cat->name = "Tags";
		return $cat;
	}

	static public function CreateFrom(array $object) : CategoryDTO {
		$cat = new CategoryDTO();
		foreach($object as $key => $value)
			if ($value !== 0 && empty($value))
				$object[$key] = null;
		
		$cat->id 	= value($object['id']);
		$cat->order	= value($object['order']);
		$cat->slug 	= value($object['slug']);
		$cat->name 	= value($object['name']);
		$cat->description = value($object['description']);
		$cat->color = value($object['color']);
		return $cat;
	}

	public function GetName() : string {
		return $this->name ?? $this->slug;
	}
}
?>