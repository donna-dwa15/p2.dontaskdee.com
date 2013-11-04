<!-- Display all posts of users being followed by logged in user -->
<div id="posts">
	<h1>Meow Mix</h1>
	<?php foreach($posts as $post): ?>
		<article>
			<!-- Print the name of the post's creator and date created -->
			<span class="user_info">
				<?=$post['first_name']?> <?=$post['last_name']?> posted on 
				<time datetime="<?=Time::display($post['created'],'Y-m-d G:i')?>">
					<?=Time::display($post['created'])?>
				</time>
			</span>		
			<!-- Display Unfollow Button if post is not from current user -->
			<?php if($post['post_user_id'] != $user->user_id): ?>
				<a href="/posts/post_unfollow/<?=$post['post_user_id']?>"><img src="/images/unstalk_btn_2.png" alt="Unstalk!"/></a>
			<?php endif; ?>		
			<!-- Display post content -->
			<p><?=$post['content']?></p>
		</article>
	<?php endforeach; ?>
</div>