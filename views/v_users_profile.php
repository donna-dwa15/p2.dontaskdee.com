<div id="profile">
	<!-- Profile header depending on who is viewing -->
	<h1><?=$header?></h1>
	<!-- Profile content -->
	<div id="profile_info">
		<?php if(!isset($error)): ?>
			Name: <?=$first_name?> <?=$last_name?><br/>
			Email: <?=$email?><br/>
			Approximate Location: <?=$location?><br/> 
			Meower Since: 
			<time datetime="<?=Time::display($created,'Y-m-d',$user->timezone)?>">
				<?=Time::display($created,'F j, Y',$user->timezone)?>
			</time><br/>
			Profile Last Updated: 
			<time datetime="<?=Time::display($last_modified,'Y-m-d G:i',$user->timezone)?>">
				<?=Time::display($last_modified,'F j, Y g:ia',$user->timezone)?>
			</time><br/>			
		<?php endif; ?>
		<?php if(isset($last_post)): ?>
			Last Meowed: <?=$last_post?><br/>
		<?php endif; ?>
	</div>
</div>