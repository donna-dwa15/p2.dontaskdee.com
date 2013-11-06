<div id="form_content">
	<header>
		<h1>Login to start meowing!</h1>
	</header>
	<!-- User Login form -->
	<form method="POST" action="/users/p_login">
		<?php if(isset($error)): ?>
			<div class="error">
				<?=$_SESSION['error']?>
			</div>
			<br/>
		<?php endif; ?>
		<!-- Required form fields -->
		<label for="email">Email</label><br/>
		<input type="text" size="30" maxlength="150" id="email" name="email" value="<?=$_SESSION['email']?>"/><br/>
		<label for="password">Password</label><br/> 
		<input type="password" size="30" maxlength="50" id="password" name="password"/><br/>
		<input type="image" src="/images/login_btn.png" alt="login!"/>	
	</form>
	<span id="new_user">
		New user? Sign up!
	</span>
	<br/>
	<!-- Link to signup area for new users -->
	<a href="/users/signup"><img src="/images/signup_btn.png" alt="sign up!"/></a>
</div>