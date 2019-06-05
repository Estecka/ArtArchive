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

print "<div class=\"pageList\">";
if ($currentPage > 0){
	$url = sprintf($urlFormat, $currentPage-1);
	?>
	<a href="<?=$url?>">Previous <<</a> | 
	<?php
}

if ($pageMin > 0){
	echo " ••• ";
}

for ($i=$pageMin; $i<=$pageMax; $i++){
	if ($i == $currentPage){
		echo "<b>$i</b>,";
	}
	else {
		$url = sprintf($urlFormat, $i);
		?>
		<a href="<?=$url?>"><?=$i?></a>, 
		<?php
	}
}

if ($currentPage < ($pageAmount-1)){
	if ($pageMax < ($pageAmount-1)){
		echo " ••• ";
	}
	$url = sprintf($urlFormat, $currentPage+1);
	?>
	| <a href="<?=$url?>">>> Next</a>
	<?php
}
print "</div>";
?>