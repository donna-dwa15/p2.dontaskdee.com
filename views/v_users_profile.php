<div id="profile">
	<!-- Profile header depending on who is viewing -->
	<h1><?=$header?></h1>
	<!-- Profile content -->
	<div id="profile_info">
		<?php if(!isset($error)): ?>
			Name: <?=$first_name?> <?=$last_name?><br/>
			Email: <?=$email?><br/>
		<?php endif; ?>
		<?php if(isset($last_post)): ?>
			Last Meow: <?=$last_post?><br/>
		<?php endif; ?>
	</div>
</div>