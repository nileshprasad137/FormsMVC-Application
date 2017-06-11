<?php

class HL7Viewer extends Controller {

    function __construct() {
        parent::__construct();
    }

    function router() {
        echo 'INSIDE HL7Viewer ROUTER ';        
        //$this->edit();
    }

    function edit() {
        //echo 'INSIDE PHARMACY EDIT ';
        //$this->view->render('Pharmacy/edit');
    }

    function display() {
        //echo "INSIDE PHARMACY DISPLAY";
        //$this->view->render('Pharmacy/display');
    }
    
    function save(){
        //$this->model->save();                
    }

}
