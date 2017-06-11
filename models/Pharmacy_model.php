<?php

class Pharmacy_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function save()
	{
                                       // Save into database
		echo "Saved into database";
                                       header('location: ../Pharmacy/display');
	}
	
}