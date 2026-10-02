<?php
require("../ArtArchive.php");

function NewRadioArray(){
	return array(
		'require' => array(),
		'include' => array(),
		'range'=> array(),
		'exclude' => array(),
	);
}

// Redirect posts to a get-formatted URL
if ($_POST){
	if (!isset($_POST["status"])) {
		PageBuilder::ErrorDocument(400, "Bad Request");
		die;
	}

	$statusToSlugs = NewRadioArray();
	foreach($_POST["status"] as $tag=>$radio){
		if (isset($statusToSlugs[$radio]))
			$statusToSlugs[$radio][] = $tag;
	}

	header("Location:".URL::Search($statusToSlugs), false, 303);
	exit;
}

// Redirect queries that use the old format.
if (isset($_GET['tags'])
&& ! isset($_GET['include'])
&& ! isset($_GET['exclude'])
&& ! isset($_GET['range'])
&& ! isset($_GET['require'])
){
	header("Location:".URL::SearchLegacy(implode('+', $_GET['tags'])), false, 301);
	exit;
}


$bdd = &ArtArchive::$database;
$rpp = ArtArchive::$settings["ResultsPerPage"];
$pageNo = (int)either($_GET["page"], 0);
$allTags = $bdd->GetAllTags();
$allCats = $bdd->GetAllCategories();

$slugToTag = array();
foreach ($allTags as $tag)
	$slugToTag[$tag->slug] = $tag;


// Compile query string into easy to access data
$missingSlugs = array();
$slugToStatus = array();
$statusToTags = NewRadioArray();
$statusToIds  = NewRadioArray();
foreach (array('include', 'exclude', 'range', 'require') as $status)
if (isset($_GET[$status]))
foreach (array_unique(explode(' ', $_GET[$status])) as $slug)
{
	if (isset($slugToTag[$slug])){
		$t = $slugToTag[$slug];
		$slugToStatus[$t->slug] = $status;
		$statusToTags[$status][] = $t;
		$statusToIds [$status][] = $t->id;
	}
	else
		$missingSlugs[] = $slug;
}

$includeIdGroups = array();
$includeIdGroups['global'] = $statusToIds['include'];
$rangeCatToTags  = array();
foreach ($statusToTags['range'] as $tag) {
	$cat = value($allCats[$tag->categoryId]);
	$grpName = "categorynull";
	$catId = null;
	if ($cat != null){
		$grpName = "category$cat->id";
		$catId = $cat->id;
	}

	if (!isset($includeIdGroups[$grpName]))
		$includeIdGroups[$grpName] = array();
	$includeIdGroups[$grpName][] = $tag->id;

	if (!isset($rangeCatToTags[$catId]))
		$rangeCatToTags[$catId] = array();
	$rangeCatToTags[$catId][] = $tag;
}

$isQuery = !empty($statusToIds['require'])
        || !empty($statusToIds['include'])
        || !empty($statusToIds['range'])
        || !empty($statusToIds['exclude'])
        ;


// Search for artworks
if ($isQuery){
	$arts = $bdd->SearchArtworks(
		$statusToIds['require'],
		$statusToIds['exclude'],
		$includeIdGroups,
		$rpp, $pageNo, $total
	);

	if ($arts)
		$arts = $bdd->GetThumbnails($arts);
	$pageAmount = (int)ceil($total /$rpp);
}

$page = new PageBuilder("Search");

/**
 * @param TagDTO[] $tags
 */
function SummarizeTagList(string $title, array $tags) {
	global $page, $allCats;
	if (empty($tags))
		return;
	?>
	<p>
		<b><?=$title?></b>
		<?php
		foreach ($tags as $t){
			$color = null;
			if (isset($allCats[$t->categoryId]))
				$color = $allCats[$t->categoryId]->color;

			$page->TagLink($t, $color);
			print ", ";
		}
		?>
	</p>
	<?php
}

$page->StartPage();
	if (isset($arts))
	{
		?>
		<section class='searchSummary'>
			<h3>Search summary</h3>
			<?php
			SummarizeTagList("Excluded: ", $statusToTags['exclude']);
			SummarizeTagList("Required: ",  $statusToTags['require']);
			SummarizeTagList("Allowed: ",  $statusToTags['include']);

			foreach ($rangeCatToTags as $catId => $tags){
				$cat = either($allCats[$catId], CategoryDTO::Empty());

				SummarizeTagList("Allowed ".$cat->GetName().":", $tags);
			}

			if (!empty($missingSlugs)){
				?><p>
					<b>The following tags do not exist and were ignored :</b>
					<?=implode(", ", $missingSlugs)?>
				</p><?php
			}
			?>
			
		</section>
		<?php
	}
	?>
	<section class='searchResults'>
		<?php
		if (isset($arts)){

			?><h3>Results : </h3><?php

			if (sizeof($arts) <= 0){
				?><p><i class='emptyNotice'>This search did not yield any result. :(</i></p><?php
			}
			else
				$page->ArtCardList($arts);

			$query = $_GET;
			unset($query['page']);
			$page->PageList(
				URL::Search($_GET, "%d"),
				$pageNo,
				$pageAmount,
				10
			);
		}
		?>
	</section>
	<section class=searchForm>
		<h3>New Search</h3>
		<input id=FormType type=checkbox /> <label for=FormType>Show advanced options</label>
		<p class=advanced>
			<?php $page->MaskedImage("/resources/icon/search/forbidden.png")?>
			Results will have <strong>none</strong> of those tags.
			<br/>
			<?php $page->MaskedImage("/resources/icon/search/intersection.png")?>
			Results will have <strong>all</strong> of those tags.
			<br/>
			<?php $page->MaskedImage("/resources/icon/search/combination.png")?>
			Results will have <strong>at least one</strong> of those tags overall.
			<br/>
			<?php $page->MaskedImage("/resources/icon/search/weird.png")?>
			Results will have <strong>at least one</strong> of those tags in <strong>every</strong> applicable category.
		</p>
		<form class='searchForm' method="POST">
			<?php
			$page->SearchForm($allTags, $allCats, $slugToStatus);
			?>
			<input type="reset"/>
			<input type="submit"/>
		</form>
	</section>
	<?php
$page->EndPage();
?>