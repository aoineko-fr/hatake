<p class='menu_section' onclick='ToggleVisibilityByID("import_csv");' style='cursor:pointer;'>Import ⯆</p>
<div id='import_csv' style='background:#fff; padding:0 0.3em 0 0.3em; display:none;' data-display='block' >
	<form name='filter' action='index.php' method='post' enctype='multipart/form-data' style='display:flex; flex-flow:column;' >
		<input type='hidden' name='page' value='collection' />
		<input type='hidden' name='table' value='variety' />
		<input type='hidden' name='task' value='do_get_csv' />
		<input type='hidden' name='action' value='import' />
		<label for='csv_file'>File</label>
		<input class='edit' type='file' name='csv_file' id='csv_file' accept='text/csv' />
		<label for='csv_delimiter'>Delimiter</label>
		<input class='edit' type='text' name='csv_delimiter' id='csv_delimiter' value=',' />
		<label for='csv_enclosure'>Enclosure</label>
		<input class='edit' type='text' name='csv_enclosure' id='csv_enclosure' value='"' />
		<input type='submit' value='Importer' style='margin-top:16px;' />
	</form>
</div>

<p class='menu_section' onclick='ToggleVisibilityByID("export_csv");' style='cursor:pointer;' >Export ⯆</p>
<div id='export_csv' style='background:#fff; padding:0 0.3em 0 0.3em; display:none;' data-display='block' >
	<form name='filter' action='index.php' method='post' enctype='multipart/form-data' style='display:flex; flex-flow:column;' >
		<input type='hidden' name='page' value='collection' />
		<input type='hidden' name='table' value='variety' />
		<input type='hidden' name='task' value='do_export_csv' />
		<label for='csv_delimiter'>Delimiter</label>
		<input class='edit' type='text' name='csv_delimiter' id='csv_delimiter' value=',' />
		<label for='csv_enclosure'>Enclosure</label>
		<input class='edit' type='text' name='csv_enclosure' id='csv_enclosure' value='"' />
		<input type='submit' value='Exporter' style='margin-top:16px;' />
	</form>
</div>
