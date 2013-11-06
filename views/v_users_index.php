<h1>Hello, <?=$user->first_name?>!</h1>
<!-- Current user stats summary -->
<div id="users">
	<?php if(isset($last_login)): ?>
	Last logged in on 
	<time datetime="<?=Time::display($last_login,'Y-m-d H:i',$user->timezone)?>">
		<?=Time::display($last_login,'F j, Y g:ia',$user->timezone)?>
	</time>.
	<br/><br/>
	<?php endif; ?>
	You are currently stalking <?=$followed?> prey.<br/>
	You are being stalked by <?=$following?> predators.<br/>
	<br/>
	<?php if(isset($random_post)): ?>
	One of your prey meowed:<br/>
	<article>
		<!-- Print the name of the post's creator and date created -->
		<span class="user_info">
			<?=$random_post['first_name']?> <?=$random_post['last_name']?> meowed on 
			<time datetime="<?=Time::display($random_post['created'],'Y-m-d H:i',$user->timezone)?>">
				<?=Time::display($random_post['created'],'F j, Y g:ia',$user->timezone)?>
			</time>
		</span>		
		<!-- Display Unfollow Button if post is not from current user -->
		<a href="/posts/post_unfollow/<?=$random_post['post_user_id']?>"><img src="/images/unstalk_btn_2.png" alt="Unstalk!"/></a>	
		<!-- Display post content -->
		<p><?=$random_post['content']?></p>
	</article>
	<? else: ?>
		Everything is quiet.<br/>
	<?php endif; ?>
</div>