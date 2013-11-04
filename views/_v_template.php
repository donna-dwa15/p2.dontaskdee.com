<!DOCTYPE html>
<html>
<head>
	<title><?php if(isset($title)) echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	<link rel="stylesheet" type="text/css" href="/css/main.css">				
	<!-- Controller Specific JS/CSS -->
	<?php if(isset($client_files_head)) echo $client_files_head; ?>	
</head>
<body>
	<div id="menu">
		<!-- Home link logo -->
		<a href="/"><img src="/images/meower_logo.png"/></a>
		<span class="text_link">
			<!-- Top Menu for users who are logged in -->
			<?php if($user): ?>
				<a href="/users/profile"><img src="/images/profile.png" alt="View Profile"/></a> |
				<a href="/users/logout"><img src="/images/logout2.png" alt="Log out!"/></a>				
			<!-- Menu options for users who are not logged in -->
			<?php else: ?>
				<a href="/users/signup"><img src="/images/signup.png" alt="Sign up!"/></a> |
				<a href="/users/login"><img src="/images/login2.png" alt="Log In!"/></a>
			<?php endif; ?>
		</span>
	</div>
	<div id="main_content">
    <br/>
	<!-- Navigateion menu for users who are logged in, do not display when not logged in -->
	<?php if($user): ?>
		<div id="main_navigation">
			<a href="/users/index"><img src="/images/home.png" alt="Go home"/></a> |
			<a href="/users/profile"><img src="/images/profile.png" alt="View profile"/></a> |
			<a href="/posts/add"><img src="/images/meow.png" alt="Add post"/></a> |
			<a href="/posts/index"><img src="/images/stalk.png" alt="View posts"/></a> |
			<a href="/posts/users"><img src="/images/seek_prey.png" alt="Search users"/></a>
		</div>
	<?php endif; ?>	
	<!-- Main page content -->
	<?php if(isset($content)) echo $content; ?>
	</div>
	<?php if(isset($client_files_body)) echo $client_files_body; ?>
</body>
</html>