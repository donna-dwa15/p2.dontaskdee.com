<?php

// library for re-usable validation functions
// All methods should be static, accessed like: Validate::method(...);
class Validate {

	/*-------------------------------------------------------------------------------------------------
	Validates sign up form fields.
	-------------------------------------------------------------------------------------------------*/
	public static function validate_signup($fields)
	{
		$errors = array();
		foreach($fields as $key=>$value)
		{
			$value = trim($value);
			if($key == 'first_name' || $key == 'last_name')
			{
				$name_type = str_replace('_',' ',$key);
				if(!self::is_alpha($value))
					$errors[] = "Valid {$name_type} is required.";
			}
			elseif($key == 'email')
			{
				if($value == FALSE || !filter_var($value, FILTER_VALIDATE_EMAIL))
					$errors[] = "Valid email address is required.";
			}
			elseif($key == 'password')
			{
				if(empty($value))
					$errors[] = "A password is required.";
			}
			elseif($key == 'password_confirm')
			{
				if(empty($value) && trim($fields['password']) !== FALSE)
					$errors[] = "Please confirm your password.";
				elseif($value != $fields['password'] && trim($fields['password']) !== FALSE)
					$errors[] = "Your password does not match.";
			}
		}
		return $errors;
	}

	/*-------------------------------------------------------------------------------------------------
	Checks if a string is empty or contains alphabetic characters only
	-------------------------------------------------------------------------------------------------*/
	public static function is_alpha($str)
	{
		if(trim($str) == FALSE)
			return false;
		elseif (preg_match("/^[a-zA-Z][a-zA-Z ]*$/", $str)) 
			return true;
		
		return false;
	}
	
	/*-------------------------------------------------------------------------------------------------
	Cleans up an array of string data.
	-------------------------------------------------------------------------------------------------*/
	public static function clean_data($fields)
	{
		$clean_fields = array();
		
		foreach($fields as $key=>$value)
		{
			$clean_fields[$key] = strip_tags($value);
		}
		
		$clean_fields = DB::instance(DB_NAME)->sanitize($clean_fields);
		
		return $clean_fields;
	}
}
?>