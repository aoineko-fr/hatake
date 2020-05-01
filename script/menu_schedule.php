<form name="filter" action="index.php" method="post" enctype="multipart/form-data" style="display:flex; flex-flow:column;" >
	<input type="hidden" name="page" value="<?php print($page); ?>" />
	<div><label for="displayCrop">Display crops</label><input type="checkbox" id="displayCrop" name="displayCrop" checked style="float:right;" /></div>
	<label for="variety">Variety</label>
	<?php
	
		$result = SqlQuery("SELECT `id`,`name` FROM `${g_SQLTablePrefix}variety`");
		$fetchResult = SqlFetchAllResult($result, MYSQLI_ASSOC);
		$html = "<select class='edit' name='variety' id='variety' style='width:100%;' >";
		$html .= "<option value='-1'>Toutes</option>";
		foreach($fetchResult as $opt)
		{
			$id = $opt['id'];
			$name = $opt['name'];
			$html .= "<option value='$id'>$name</option>";
		}
		$html .= "</select>";
		print $html;
		
	?>
	<br/>
	<div><label for="displayVariety">Display collection</label><input type="checkbox" id="displayVariety" name="displayVariety" checked style="float:right;" /></div>
	<label for="category">Category</label>
	<select class='edit' name='category' id='category' style='width:100%;' >
		<option value='-1'>Toutes</option>
	</select>
	<label for="category">Type</label>
	<select class='edit' name='type' id='type' style='width:100%;' >
		<option value='-1'>Toutes</option>
	</select>
	<br/>
	<input type="submit" value="Filtrer" />
</form>