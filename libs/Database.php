<?php

class Database extends PDO
{
	
	public function __construct()
	{
                                       //   Constants defined in config/database.php
		parent::__construct(DB_TYPE.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
	}
	
	
}