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
			login();		
		}
		else
		{
			# Display user's welcome page
			$this->template->title = "Welcome to ".APP_NAME;
			$this->template->content = View::instance('v_users_index');
			
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
		$client_files = Array("/css/form.css");
	    $this->template->client_files_head = Utils::load_client_files($client_files);
		
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
		
		# If no error, check database to see if user already exists
		# If so, return error message
		
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
			# Clear out error session variables if needed
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

			# On successful signup, login user and redirect to profile page
			setcookie("token", $_POST['token'], strtotime('+2 day'), '/');
			
			Router::redirect("/users/profile");
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
		
			setcookie("token", $token, strtotime('+2 day'), '/');

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

		# Send them back to the main index.
		Router::redirect("/");
	}

	public function profile($user_name = NULL) 
	{
		# If user is blank, they're not logged in; redirect them to the login page
		if(!isset($this->user)) 
		{
			Router::redirect('/users/login');
		}

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
			$user_name = $clean_data['user_name'];
			$this->template->title = "Profile of {$profile_user['first_name']} {$profile_user['last_name']}";
			$this->template->content->header = "Profile of {$profile_user['first_name']} {$profile_user['last_name']}";
			
			$q = "SELECT first_name, last_name, email, content as last_post 
				FROM users
				LEFT JOIN posts USING(user_id)
				WHERE email = '".$user_name."'
				ORDER BY posts.created desc 
				LIMIT 1";
				
			$profile_user = DB::instance(DB_NAME)->select_row($q);
		
			# If user exists, display profile data
			# Otherwise, user does not exist
			if($profile_user)
			{
				
				$this->template->content->first_name = $profile_user['first_name'];
				$this->template->content->last_name = $profile_user['last_name'];
				$this->template->content->email = $profile_user['email'];
				$this->template->content->last_post = $profile_user['last_post'];
			}
			else
			{
				$this->template->title = "Profile of {$user_name}";
				$this->template->content->header = "User profile cannot be displayed.";
				$this->template->content->error = "error";
			}
		}
		else
		{
			$this->template->title = "My Profile";
			$this->template->content->header = "My Profile";
			$this->template->content->first_name = $this->user->first_name;
			$this->template->content->last_name = $this->user->last_name;
			$this->template->content->email = $this->user->email;
		}
		
		# Render template
		echo $this->template;
	}

} # end of the class
?>