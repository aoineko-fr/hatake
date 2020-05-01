<div id='log_reg' style='display:flex; flex-flow:row; justify-content:center;' >
	<div class='login_panel'>
		<div style='display:flex; flex-flow:row;'>
			<img src='pub/img/interface/man-farmer_1f468-200d-1f33e.png' style='height:32px;' />
			<div class='login_title'><?php print Loc("user.do_login"); ?></div>
		</div>
		<form name='login' action='index.php' method='post' enctype='multipart/form-data'>
			<input type='hidden' name='table' value='user' />
			<input type='hidden' name='task' value='do_login' />
			<table class='sql' border='0' cellpadding='0' cellspacing='0' >
				<tr class='even'>
					<td><?php print Loc("user.login"); ?></td>
					<td><input class='edit' type='text' name='login' id='login' value='' style='width:100%;' /></td>
				</tr><tr class='odd'>
					<td><?php print Loc("user.password"); ?></td>
					<td><input class='edit' type='password' name='password' id='password' value='' style='width:100%;' /></td>
				</tr>			
			</table>
			<input type='image' src='pub/img/interface/white-heavy-check-mark_2705.png' alt='<?php print Loc("user.do_login"); ?>' id='do_login' style='height:24px; margin-top:4px;' >
		</form>
	</div>
	<?php
	
	if($selfRegister)
		print "
	<div class='login_panel'>
		<div style='display:flex; flex-flow:row;'>
			<img src='pub/img/interface/bust-in-silhouette_1f464.png' style='height:32px;' />
			<div class='login_title'>". Loc("user.do_register") ."</div>
		</div>
		<form name='register' action='index.php' method='post' enctype='multipart/form-data'>
			<input type='hidden' name='table' value='user' />
			<input type='hidden' name='task' value='do_register' />
			<table class='sql' border='0' cellpadding='0' cellspacing='0' >
				<tr class='even'>
					<td>". Loc("user.login") ."</td>
					<td><input class='edit' type='text' name='login' id='login' value='' style='width:100%;' /></td>
				</tr><tr class='odd'>
					<td>". Loc("user.password") ."</td>
					<td><input class='edit' type='password' name='password' id='password' value='' style='width:100%;' /></td>
				</tr><tr class='even'>
					<td>". Loc("user.familyname") ."</td>
					<td><input class='edit' type='text' name='familyname' id='familyname' value='' style='width:100%;' /></td>
				</tr><tr class='odd'>
					<td>". Loc("user.firstname") ."</td>
					<td><input class='edit' type='text' name='firstname' id='firstname' value='' style='width:100%;' /></td>
				</tr><tr class='even'>
					<td>". Loc("user.birthday") ."</td>
					<td><input class='edit' type='date' name='birthday' id='birthday' value='' style='width:100%;' /></td>
				</tr><tr class='odd'>
					<td>". Loc("user.avatar") ."</td>
					<td><input class='edit' type='file' name='avatar' id='avatar' value='' style='width:100%;' accept='image/*' /></td>
				</tr>			
			</table>
			<input type='image' src='pub/img/interface/white-heavy-check-mark_2705.png' alt='". Loc("user.do_register") ."' id='do_register' style='height:24px; margin-top:4px;' >
		</form>
	</div>
";
	
	?>
</div>