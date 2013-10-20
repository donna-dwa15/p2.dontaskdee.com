<form method='POST' action='/users/p_login'>
	<?php if(isset($error)): ?>
        <div class='error'>
            Login failed. Please double check your email and password.
        </div>
        <br>
    <?php endif; ?>
	<span id="login">Login!</span><br/>
	Email<br/>
	<input type="text" size="40" maxlength="150" name="email"/><br/>
	Password<br/> 
	<input type="password" size="40" maxlength="50" name="password"/><br/>
	<input type="image" src="/images/login_btn.png" alt="login!"/>	
</form>
<span id="new_user">
	New user? Signup!
</span>
<br/>
<a href="/users/signup"><img src="/images/signup_btn.png" alt="signup!"/></a>