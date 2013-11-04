<h1>Welcome to Meower!</h1>
<!-- Left hand content -->
<div id="new_features">
	<h2>New features!</h2>
	<img src="/images/meower_head.png"/>Feeling lonely?  Search for new people to STALK!<br/>
	<img src="/images/meower_head.png"/>Regret a meow?  SCRATCH those unwanted posts!<br/>
	<h2>Find your prey today!</h2>
</div>
<!-- Content divider -->
<div id="vert_line">
	<img src="/images/v_line.png"/>
</div>
<!-- Right hand content -->
<div id="form_content">
	<!-- Login form -->
	<form method="POST" action="/users/p_login">
		<header id="login">
			<h2>Ready to meow? Login!</h2>
		</header>
		<label for="email">Email</label><br/>
		<input type="text" size="30" maxlength="150" name="email"/>
		<br/>
		<label for="password">Password</label>
		<br/>
		<input type="password" size="30" maxlength="50" name="password"/>
		<br/>
		<input type="image" src="/images/login_btn.png" alt="login!"/>
	</form>
	<br/>
	<!-- For new users -->
	<header id="new_user">
		<h2>New user? Sign up!</h2>
	</header>
	<a href="/users/signup"><img src="/images/signup_btn.png" alt="sign up!"/></a>
	<br/>
</div>