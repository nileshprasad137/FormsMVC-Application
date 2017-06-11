<?php

class X12Partners extends Controller {

    function __construct() {
        parent::__construct();
    }

    function router() {
        echo 'INSIDE X12PARTNERS ROUTER ';        
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
