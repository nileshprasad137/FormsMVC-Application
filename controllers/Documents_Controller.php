<?php

class Documents extends Controller {

    function __construct() {
        parent::__construct();
    }

    function router() {
        //echo 'INSIDE PHARMACY ROUTER ';        
        //$this->edit();
        echo "you are inside router of documents";
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
