<?php include 'optimizely.php';?>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');


// setup the object
$optimizely = new Optimizely('6e3a173e9e74caba0dbb34b3e1db612f:6a13b0a4');
//print_r($optimizely);
// get projects
//$projects = $optimizely->get_projects();

$experiments = $optimizely->get_experiments('4048040051');


?>