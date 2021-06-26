<?php

class ArtworkDTO {
	/** @var int */
	public $id;
	/** @var string */
	public $slug;
	/** @var string */
	public $title;
	/** @var string */
	public $date;
	/** @var string */
	public $description;
	/** @var string */
	public $links;

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
		$art->links = value($object['links']);
		return $art;
	}

	public function IsValidSubmition() : bool {
		return isDate($this->date)
			&& $this->slug;
	}

	public function GetName() : string{
		return $this->title ?? $this->slug;
	}

}

?>