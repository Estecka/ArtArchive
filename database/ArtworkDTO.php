<?php

class ArtworkDTO {
	public $id;
	public $slug;
	public $title;
	public $date;
	public $description;

	static public function CreateFrom($object) : ArtworkDTO {
		foreach($object as $key => $value)
			if ($value !== 0 && empty($value))
				$object[$key] = null;

		$art = new ArtworkDTO();
		$art->id	= value($object['id']);
		$art->title	= value($object['title']);
		$art->date 	= value($object['date']);
		$art->slug 	= value($object['slug']);
		$art->description = value($object['description']);
		return $art;
	}

	public function IsValidSubmition() : bool {
		return isDate($this->date)
			&& $this->slug;
	}

}

?>