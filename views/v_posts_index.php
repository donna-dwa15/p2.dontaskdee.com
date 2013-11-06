<!-- Display all posts of users being followed by logged in user -->
<div id="posts">
	<h1>Meow Mix</h1>
	<?php foreach($posts as $post): ?>
		<article>
			<!-- Print the name of the post's creator and date created -->
			<span class="user_info">
				<?php if($post['post_user_id'] != $user->user_id): ?>
					<a href="/users/profile/<?=$post['email']?>"><?=$post['first_name']?> <?=$post['last_name']?></a> 
				<?php else: echo "You" ?>
				<?php endif; ?> 
				meowed on 
				<time datetime="<?=Time::display($post['created'],'Y-m-d G:i',$user->timezone)?>">
					<?=Time::display($post['created'],'F j, Y g:ia',$user->timezone)?>
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
	<?php if(count($posts)==0): ?>
		<article>
			<p>There are no meows.  You should find someone to <a href="/posts/users/">stalk</a>.</p>
		</article>
	<?php endif; ?>
</div>