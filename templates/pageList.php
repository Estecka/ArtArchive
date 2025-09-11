<?php
/**
 * @var string $urlFormat Url where %d represents the page number. E.g: "http://url?page=%d"
 * @var int $currentPage Zero-based index of the current page
 * @var int $pageAmount
 * @var int $maxRange How many links to the nearby pages should be displayed.
 */

$pageMin = $currentPage - floor($maxRange*0.5);
$pageMin = max(0, $pageMin);

$pageMax = $pageMin + $maxRange;
$pageMax = min($pageMax, $pageAmount-1);

$pageMin = $pageMax - $maxRange;
$pageMin = max(0, $pageMin);

function ellipsis(){
	?>
	<span class='pageItem ellipsis' >
		•••
	</span>
	<?php
}

?><nav class='pageList'><?php
	if ($currentPage > 0){
		$url = sprintf($urlFormat, $currentPage-1);
		?>
		<span class='pageItem pageNav pagePrevious'><a href="<?=$url?>">Previous <<</a></span>
		<?php
	}

	if ($pageMin > 0){
		ellipsis();
	}

	for ($i=$pageMin; $i<=$pageMax; $i++){
		if ($i == $currentPage){
			?>
			<span class='pageItem pageNumber currentPage'><b><?=$i?></b></span>
			<?php
		}
		else {
			$url = sprintf($urlFormat, $i);
			?>
			<span class='pageItem pageNumber otherPage'><a href="<?=$url?>"><?=$i?></a></span>
			<?php
		}
	}

	if ($currentPage < ($pageAmount-1)){
		if ($pageMax < ($pageAmount-1)){
			ellipsis();
		}
		$url = sprintf($urlFormat, $currentPage+1);
		?>
		<span class='pageItem pageNav pageNext'><a href="<?=$url?>">>> Next</a></span>
		<?php
	}
?></nav><?php
