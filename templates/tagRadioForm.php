<?php
/**
 * @var PageBuilder $page
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var ?string[] $tagToStatus maps tag slugs to their initial status.
 */

$printCat = function(CategoryDTO $c){
	$name = $c->GetName();
	$style = $c->color ? "style=\"color: $c->color\"" : null;
	print("<h4 $style>$name</h4>");
};
$printTag = function(CategoryDTO $c, TagDTO $t) use ($tagToStatus) {
	$inputName = "status[$t->slug]";

	$checked = function (?string $checkStatus) use ($tagToStatus, $t) {
		return ($checkStatus == either($tagToStatus[$t->slug], 'ignore')) ? "checked" : "";
	};

	$radioInput = function($status, $simple=false) use ($inputName, $checked) {
		if ($simple) $simple='simple';
		?><input
			class='searchGroup <?=$status?> <?=$simple?>'
			type='radio'
			name="<?=$inputName?>"
			id="<?=$inputName?>.<?=$status?>"
			value="<?=$status?>"
			<?=$checked($status)?>
		/><?php
	};

	$iconLabel = function($status, $icon, $title=null) use ($inputName, $c) {
		if ($title) $title = "title='$title'";
		?><label
			for='<?=$inputName?>.<?=$status?>'
			class='iconLabel disabled advanced <?=$status?>'
			style="--radio-icon:url(/resources/icon/search/<?=$icon?>.png)"
			<?=$title?>
		></label><?php
		?><label
			for='<?=$inputName?>.ignore'
			class='iconLabel enabled advanced <?=$status?>'
			style="--radio-icon:url(/resources/icon/search/<?=$icon?>.png)"
			<?=$title?>
		></label><?php
	};


	$radioInput('ignore');  $iconLabel('ignore',  "exclude", "Ignored");
	$radioInput('exclude'); $iconLabel('exclude', "forbidden", "Blacklisted");
	$radioInput('require'); $iconLabel('require', "intersection", "Intersection");
	$radioInput('range');   $iconLabel('range', "weird", "Category-based");
	$radioInput('include'); $iconLabel('include', "combination", "Union");
	?>
	<label
		for='<?=$inputName?>.range'
		class='iconLabel disabled simple'
		style="--radio-icon:url(/resources/icon/search/exclude.png)"
		title="Ignored"
	></label><?php
	?><label
		for='<?=$inputName?>.ignore'
		class='iconLabel enabled simple'
		style="--radio-icon:url(/resources/icon/search/checked.png)"
		title="Included"
		></label>
	<span class='advanced'><?=$t->slug?></span>
	<label class='simple disabled' for="<?=$inputName?>.range"><?=$t->slug?></label>
	<label class='simple enabled'  for="<?=$inputName?>.ignore" ><?=$t->slug?></label>
	<?php
};

$page->LiquidTable(
	$tags, $cats,
	$printTag, $printCat,
	"Others",
	false
);
?>