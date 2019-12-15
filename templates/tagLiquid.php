<?php
/**
 * @var CategoryDTO $cat
 * @var TagDTO[] $tags
 * @var int $rowMax
 * @var function $printCat function(CategoryDTO) 
 * @var function $printTag function(TagDTO)
 */

$empty = empty($tags);

for($col=0; $col==0 || current($tags); $col++){
	$hidden = $col ? "style=\"visibility: hidden\"" : null;
	?>
	<table class="inlineCategory">
		<tr <?=$hidden?>>
			<th><?=$printCat($cat)?></th>
		</tr>
		<?php
		if (!$col && !current($tags)) {
			print("<tr><td>This category is empty.<td><tr>");
			break;
		}
		for($row=0; $row<$rowMax && $tag = current($tags); $row++, next($tags)){
			?>
			<tr>
				<td><?=$printTag($cat, $tag)?></td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
}
