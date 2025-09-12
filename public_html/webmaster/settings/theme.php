<?php
require_once ".settings.php";

$page = new PageBuilder("Site settings");
$page->StartPage();
	?>

<form method="POST">
		<h2>Site layout</h2>

		<label for=stylesheets>Stylesheet:</label>
		<input id="stylesheets" type=text name="settings[stylesheet]" 
			placeholder="<?=htmlspecialchars("/css/stylesheet.css")?>"
			value="<?=htmlspecialchars($settings['stylesheet'])?>"
		/>

		<br/>

		<label for=socialFavicons>External Link Icons</label>
		<textarea id="socialFavicons" name="configs[socialFavicons]"
			placeholder="<?=htmlspecialchars(SocialIcon::GetFormPlaceholder())?>"
		><?=value($configs['socialFavicons'])?></textarea>

		<br/>

		<h2>Artwork lists :</h2>
		<label for="rpp">Results per page</label>
		<input id="rpp" type=number name="settings[ResultsPerPage]" placeholder=20 value="<?=(int)$settings["ResultsPerPage"]?>" />

		<br/>

		<h2><label>Tag Lists :</label></h2>
		<?php
			$isLiquid = $isRigidMasonry = $isFluidMasonry = null;
			switch ($settings['tagMasonry']){
				default:
				case 0: $isLiquid       = "checked"; break;
				case 1: $isRigidMasonry = "checked"; break;
				case 2: $isFluidMasonry = "checked"; break;
			}
		?>
		<div class="row">
			<div class=column>
				<input type=radio name="settings[tagMasonry]" value=1 id="Masonry" <?=$isRigidMasonry?>>
				<label for="Masonry">
					Masonry
					<br/>
					<img src="/resources/masonry.png" width=150>
				</label>
			</div>
			<div class=column>
				<input type=radio name="settings[tagMasonry]" value=2 id="FluidMasonry" <?=$isFluidMasonry?>>
				<label for="FluidMasonry">
					Liquid Masonry
					<br/>
					<img src="/resources/liquid-masonry.png" width=150>
				</label>
			</div>
			<div class=column>
				<input type=radio name="settings[tagMasonry]" value=0 id="Liquid" <?=$isLiquid?>>
				<label for="Liquid">
					Liquid Rows
					<br/>
					<img src="/resources/liquid.png" width=150>
				</label>
				<br/><label for="Liquidity">Tags per liquid row:</label>
				<br/><input id="Liquidity" type=number name="settings[tagLiquidity]" placeholder=16 value="<?=(int)$settings["tagLiquidity"]?>" />
			</div>
		</div>

		<input type="submit"/>
	</form>

	<?php
$page->EndPage();
?>
