<!-- Allow user to add new post -->
<div id="new_post">
	<h1>Add a Meow</h1>
	<?php if(isset($error)): ?>
		<div class="error">
			<?=$error?>
			<br/>
		</div>
	<?php endif; ?>
	<!-- Add new post form -->
	<form method="POST" action="/posts/p_add">
		<label for="content">New Meow:</label><br/>
		<textarea name="content" id="content" cols="75" rows="5" maxlength="300"></textarea>
		<br/><br/>
		<input type="image" src="/images/meow_off_btn.png" alt="Meow Off!" value="Meow Off"/>
	</form> 
	<div id="note">
	* 300 characters max per post.
	</div>
</div>
<!-- Display users posts -->
<div id="old_post">
	<h1>Your Recent Meows</h1>
	<?php foreach($posts as $post): ?>
		<article>
			<h2>You posted on 
				<time datetime="<?=Time::display($post['created'],'Y-m-d G:i')?>">
					<?=Time::display($post['created'])?>
				</time>
			</h2>
			<p><?=$post['content']?></p>
			<!-- Allow user to delete post -->
			<a href='/posts/delete/<?=$post['post_id']?>'><img src="/images/scratch_post.png" alt="Scratch Post!"/></a>
		</article>
	<?php endforeach; ?>
</div>