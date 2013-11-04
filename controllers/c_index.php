<?php

class index_controller extends base_controller 
{
	
	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function __construct() 
	{
		parent::__construct();
	} 
		
	/*-------------------------------------------------------------------------------------------------
	Accessed via http://localhost/index/index/
	-------------------------------------------------------------------------------------------------*/
	public function index() 
	{
		
		# Check if user is logged in
		# If not logged in, view main landing page with signup/login
		# Else redirect to users index page
		if(!isset($this->user->token))
		{
			# Set template data
			$this->template->content = View::instance('v_index_index');
			$this->template->title = "Welcome to ".APP_NAME;
	
			# CSS/JS includes
			/*
				$client_files_head = Array("");
				$this->template->client_files_head = Utils::load_client_files($client_files);
				
				$client_files_body = Array("");
				$this->template->client_files_body = Utils::load_client_files($client_files_body);   
	    	*/
		}
		else
		{
			# Redirect user to their index page
			Router::redirect("/users/index/");
		}
		# Render the view
		echo $this->template;

	} # End of method
	
	
} # End of class
