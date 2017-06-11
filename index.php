<?php

//  We can use an autoloader!
require 'libs/Bootstrap.php';
require 'libs/Controller.php';
require 'libs/Model.php';
require 'libs/View.php';


// Library
require 'libs/Database.php';

require 'config/paths.php';
require 'config/database.php';
require 'config/ControllerActionList.php';

//print_r($controllers_list);

$app = new Bootstrap();
