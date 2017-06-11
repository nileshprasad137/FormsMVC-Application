<?php

// just a list of the controllers we have and their actions

global $controllers_list;

$controllers_list = array(
    "Pharmacy" => array(
        "display",
        "edit",
        "save"
    ),   
    "login" => array(
        "run",
        "index"
    ),
    "InsuranceCompanies" => array(
        "display",
        "edit",
        "save"
    ),
     "InsuranceNumbers" => array(
        "display",
        "edit",
        "save"
    ),
     "X12Partners" => array(
        "display",
        "edit",
        "save"
    ),
     "Documents" => array(
        "queue",
        "editCategory"        
    ), 
    "HL7Viewer" => array(
        "default"        
    )
    
);
