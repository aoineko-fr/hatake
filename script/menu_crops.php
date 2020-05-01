<form name="add_crop" action="index.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="page" value="<?php print($page); ?>" />
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
	<div><input type="submit" value="Filtre" /></div>
</form>