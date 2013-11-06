<?php
class users_controller extends base_controller 
{
	public function __construct() 
	{
		parent::__construct();
	}

	public function index() 
	{
		# Need to check for token cookie
		if(!isset($this->user->token))
		{
			# User not logged in so send them to login page
			$this->login();		
		}
		else
		{
			# Display user's welcome page
			$this->template->title = "Welcome to ".APP_NAME;
			$this->template->content = View::instance('v_users_index');
			$client_files = Array("/css/users.css");
			$this->template->client_files_head = Utils::load_client_files($client_files);
			
			# Save previous login time before we update it
			if(isset($_SESSION['last_login']))
			{
				$this->template->content->last_login = $_SESSION['last_login'];
			}
		
			# Get number of users current user is following
			$q = "SELECT user_id_followed
				FROM users_users
				WHERE user_id = ".$this->user->user_id.
				" AND user_id_followed<>".$this->user->user_id;
			
			$followed = DB::instance(DB_NAME)->select_array($q, 'user_id_followed');
			
			# Get number of other users following current user
			$q = "SELECT count(*) as following
				FROM users_users
				WHERE user_id_followed = ".$this->user->user_id.
				" AND user_id<>".$this->user->user_id;
			
			$following = DB::instance(DB_NAME)->select_field($q);
			
			# Get most recent post from one of those being followed
			if(count($followed) > 0)
			{
				# Find only those followed that have posted
				$q = "SELECT user_id AS post_user_id
					FROM posts
					WHERE user_id IN (".implode(',',array_keys($followed)).
					") GROUP BY user_id";
			
				$random_users = DB::instance(DB_NAME)->select_array($q, 'post_user_id');
			
				# Pick a random user from the results
				$random_user = array_rand($random_users, 1);			
				$q = "SELECT content, 
					first_name, 
					last_name, 
					posts.created, 
					posts.user_id AS post_user_id
					FROM posts
					INNER JOIN users USING (user_id)
					WHERE user_id=".$random_user.
					" ORDER BY posts.created desc
					LIMIT 1";
				
				$random_post = DB::instance(DB_NAME)->select_row($q);
			}
		
			$this->template->content->following = $following;
			$this->template->content->followed = count($followed);
			if(isset($random_post) && count($random_post)>0)
				$this->template->content->random_post = $random_post;
			
			echo $this->template;	
		}
	}

	public function signup($error = NULL) 
	{
		# User is already logged in so no need to sign up
		if(isset($this->user->token))
		{
			Router::redirect('/users/index');
		}

		# Setup view
		$this->template->content = View::instance('v_users_signup');
		$this->template->title   = "Sign Up";
		$client_files_head = Array("/css/form.css",
									"https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js",
									"http://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js");
		$this->template->client_files_head = Utils::load_client_files($client_files_head);

		# Pass data to the view
		$this->template->content->error = $error;

		# In case user clicked on a new signup link
		# from a page with an error
		# Clear out any previous session variables
		if(!$error)
		{
			$_SESSION['error'] = null;
			$_SESSION['first_name'] = null;
			$_SESSION['last_name'] = null;
			$_SESSION['email'] = null;
			$_SESSION['password'] = null;
			$_SESSION['password_confirm'] = null;
		}

		# Render template
		echo $this->template;	
	}

	public function p_signup() 
	{
		# Validate POST fields before processing further
		$errors = Validate::validate_signup($_POST);

		# If errors, return to signup page
		# Else check for duplicate sign up and process
		if(isset($errors) && count($errors) > 0) 
		{
			# Send them back to the signup page
			$_SESSION['error'] = $errors;
			$_SESSION['first_name'] = $_POST['first_name'];
			$_SESSION['last_name'] = $_POST['last_name'];
			$_SESSION['email'] = $_POST['email'];
			$_SESSION['password'] = $_POST['password'];
			$_SESSION['password_confirm'] = $_POST['password_confirm'];

			Router::redirect("/users/signup/error");
		} 
		else 
		{
			# Clear out error session variables if needed in case user just fixed errors
			if(isset($_SESSION['error']))
			{
				$_SESSION['error'] = null;
				$_SESSION['first_name'] = null;
				$_SESSION['last_name'] = null;
				$_SESSION['email'] = null;
				$_SESSION['password'] = null;
				$_SESSION['password_confirm'] = null;
			}
			# clear out post vars that are not needed for new user insert
			unset($_POST['password_confirm']);
			# The x and y are from the image submit button
			unset($_POST['x']);	
			unset($_POST['y']);

			# More data we want stored with the user
			$_POST['created']  = Time::now();
			$_POST['modified'] = Time::now();

			# Clean up data
			$_POST = Validate::clean_data($_POST);

			# Query for checking duplicate signup
			$q = "SELECT email
				FROM users
				WHERE email='".$_POST['email']."'
				LIMIT 1";

			$exist_email = DB::instance(DB_NAME)->select_field($q);

			# Duplicate signup, ignore new sign up and alert user
			if($exist_email)
			{
				# Send them back to the signup page
				$_SESSION['error'] = array(0=>"Account already exists.");
				Router::redirect("/users/signup/error");
			}

			# Encrypt the password  
			$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);            

			# Create an encrypted token via their email address and a random string
			$_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());	

			# Insert this user into the database
			$user_id = DB::instance(DB_NAME)->insert('users', $_POST);

			# On successful signup, login user
			setcookie("token", $_POST['token'], strtotime('+2 day'), '/');

			# Default set up of having user "follow" self so they can view their posts
			# alongside the ones they actually follow
			Router::redirect("/posts/follow/".$user_id);
		}
	}

	public function login($error = NULL) 
	{
		# User is already logged in so no need to log in again
		if(isset($this->user->token))
		{
			Router::redirect('/users/index');
		}

		# Setup view
		$this->template->content = View::instance('v_users_login');
		$this->template->title   = "Login";
		$client_files = Array("/css/form.css");
		$this->template->client_files_head = Utils::load_client_files($client_files);

		# Pass data to the view
		$this->template->content->error = $error;

		# In case user clicked on a new login link
		# from a page with an error
		# Clear out any previous session variables
		if(!$error)
		{
			$_SESSION['error'] = null;
			$_SESSION['email'] = null;
			$_SESSION['password'] = null;
		}

		# Render template
		echo $this->template;
	}

	public function p_login() 
	{
		# Grab unsanitized email for user error purposes
		$user_email = strip_tags($_POST['email']);

		# Clean post fields
		$_POST = Validate::clean_data($_POST);

		# Hash submitted password so we can compare it against one in the db
		$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

		# Search the db for this email and password
		# Retrieve the token if it's available
		$q = "SELECT token 
			FROM users 
			WHERE email = '".$_POST['email']."' 
			AND password = '".$_POST['password']."'";

		$token = DB::instance(DB_NAME)->select_field($q);

		# IF NO TOKEN, CHECK IF EMAIL IS VALID 
		if(!isset($token)) 
		{
			$q = "SELECT email 
			FROM users
			WHERE email = '".$_POST['email']."'";

			$email = DB::instance(DB_NAME)->select_field($q);

			# If email not found show invalid email error
			# Else show invalid password error
			if(!$email) 
			{
				$_SESSION['error'] = "Login failed.  Invalid Email.";
			} 
			else 
			{
				$_SESSION['error'] = "Login failed.  Please re-enter your password.";
			}
			$_SESSION['email'] = $user_email;
		}

		# If we didn't find a matching token in the database, it means login failed
		if(!isset($token)) 
		{
			# Send them back to the login page
			Router::redirect("/users/login/error");

			# But if we did, login succeeded! 
		} 
		else 
		{
			# In case there were previous login issues, clear error messaging
			if(isset($_SESSION['error'])) 
			{
				$_SESSION['error'] = null;
				$_SESSION['email'] = null;
			}

			# Set cookie with token to "log in" user
			setcookie("token", $token, strtotime('+2 day'), '/');

			# Retrieve last login time if available, we want to save the old one before overwriting
			$q = "SELECT last_login
				FROM users
				WHERE email='".$_POST['email']."'";
				
			$last_login = DB::instance(DB_NAME)->select_field($q);
			
			# If available, save current last login time before we update
			if(isset($last_login))
			{
				$_SESSION['last_login'] = $last_login;
			}
			
			# Update last logged in time
			$data = Array("last_login" => Time::now());
			DB::instance(DB_NAME)->update("users", $data, "WHERE email = '".$_POST['email']."'");

			# Send them to the main page
			Router::redirect("/users/index/");
		}
	}

	public function logout() 
	{
		# Generate and save a new token for next login
		$new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

		# Create the data array we'll use with the update method
		# In this case, we're only updating one field, so our array only has one entry
		$data = Array("token" => $new_token);

		# Do the update
		DB::instance(DB_NAME)->update("users", $data, "WHERE token = '".$this->user->token."'");

		# Delete their token cookie by setting it to a date in the past - effectively logging them out
		setcookie("token", "", strtotime('-1 year'), '/');

		# Clear out last login session if set
		if(isset($_SESSION['last_login']))
		{
			unset($_SESSION['last_login']);
		}
		
		# Send them back to the main index.
		Router::redirect("/");
	}

	public function profile($user_name = NULL) 
	{
		# If user is blank, they're not logged in; redirect them to the login page
		if(!isset($this->user->token))
		{
			# User not logged in so send them to login page
			$this->login();		
		}
		else
		{
			# Setup view
			$this->template->content = View::instance('v_users_profile');
			$client_files = Array("/css/users.css");
			$this->template->client_files_head = Utils::load_client_files($client_files);

			# If user name passed in, find user in the database to display their profile
			# Else display profile of logged in user
			if(isset($user_name))
			{
				# Clean/sanitize param
				$clean_data = Validate::clean_data(array('user_name'=>$user_name));
				$profile_name = $clean_data['user_name'];
			}
			else
			{
				$this->template->title = "My Profile";
				$this->template->content->header = "My Profile";
				$profile_name = $this->user->email;
			}
			
			# Check for user name in the database to verify they are an actual user
			$q = "SELECT first_name, 
					last_name, 
					email, 
					users.created, 
					users.modified, 
					users.timezone,
					content as last_post 
					FROM users
					LEFT JOIN posts USING(user_id)
					WHERE email = '".$profile_name."'
					ORDER BY posts.created desc 
					LIMIT 1";

			$profile_user = DB::instance(DB_NAME)->select_row($q);	

			# If user exists, display profile data
			# Otherwise, user does not exist
			if($profile_user)
			{
				# Viewing someone else's profile nets a different title and header
				if(isset($user_name))
				{
					$this->template->title = "Profile of {$profile_user['first_name']} {$profile_user['last_name']}";
					$this->template->content->header = "Profile of {$profile_user['first_name']} {$profile_user['last_name']}";
				}
				$location = explode('/',$profile_user['timezone']);			
				
				$this->template->content->first_name = $profile_user['first_name'];
				$this->template->content->last_name = $profile_user['last_name'];
				$this->template->content->email = $profile_user['email'];
				# Retrieve the second portion of the timezone to use as location
				if(isset($location[1]))
					$this->template->content->location = str_replace('_',' ',$location[1]);
				else
					$this->template->content->location = "Unknown";
				$this->template->content->created = $profile_user['created'];
				$this->template->content->last_modified = $profile_user['modified'];
				$this->template->content->last_post = $profile_user['last_post'];
				$this->template->content->user = $this->user;
			}
			else
			{
				$this->template->title = "Profile of {$user_name}";
				$this->template->content->header = "User profile cannot be displayed.";
				$this->template->content->error = "error";
			}

			# Render template
			echo $this->template;
		}
	}
} # end of the class
?>