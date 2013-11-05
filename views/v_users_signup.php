<!-- Sign up content -->
<div id="form_content">
	<h1>Welcome, new user!</h1>
	<p>
		Before we get started, we need to know the following:
	</p>
	<!-- User signup form -->
	<form method="POST" action="/users/p_signup">
		<?php if(isset($error)): ?>
			<div class="error">
				<?php foreach($_SESSION['error'] as $error_msg)
						echo($error_msg."<br/>");
				?>
				<br/>
			</div>
		<?php endif; ?>
		<!-- User info fields -->
		<label for="first_name">First Name</label><br/>
		<input type="text" id="first_name" name="first_name" size="30" maxlength="150" value="<?=$_SESSION['first_name']?>"/>
		<br/>
		<label for="last_name">Last Name</label><br/>
		<input type="text" id="last_name" name="last_name" size="30" maxlength="150" value="<?=$_SESSION['last_name']?>"/>
		<br/>
		<label for="email">Email</label><br/>
		<input type="text" id="email" name="email" size="30" maxlength="150" value="<?=$_SESSION['email']?>"/>
		<br/>
		<label for="password">Password</label><br/>
		<input type="password" id="password" name="password" size="30" maxlength="50" value="<?=$_SESSION['password']?>"/>
		<br/>
		<label for="password_confirm">Confirm Password</label><br/>
		<input type="password" id="password_confirm" name="password_confirm" size="30" maxlength="50" value="<?=$_SESSION['password_confirm']?>"/>
		<br/>	
		<input type="image" src="/images/signup_btn.png" alt="sign up!"/>
	</form>
</div>
