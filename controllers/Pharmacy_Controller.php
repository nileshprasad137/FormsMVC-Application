<?php

class Pharmacy extends Controller {

    function __construct() {
        parent::__construct();
    }

    function router() {
        //echo 'INSIDE PHARMACY ROUTER ';        
        $this->edit();
        //$this->view();
    }

    function edit() {
        //echo 'INSIDE PHARMACY EDIT ';
        $this->view->render('Pharmacy/edit');
    }

    function display() {
        //echo "INSIDE PHARMACY DISPLAY";
        $this->view->render('Pharmacy/display');
    }
    
    function save(){
        $this->model->save();                
    }

}
