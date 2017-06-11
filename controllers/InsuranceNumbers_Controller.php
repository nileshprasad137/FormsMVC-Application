<?php

class InsuranceNumbers extends Controller {

    function __construct() {
        parent::__construct();
    }

    function router() {
        echo 'INSIDE INSURANCENUMBERS ROUTER ';        
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
