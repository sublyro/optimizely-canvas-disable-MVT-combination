<?php include 'canvas-config.php';?>

<?php

$config = new CanvasConfig();
$canvas = null;


if ($config->get("APP_TYPE") == "PROJECT_JS") {
	include 'canvas-project-js.php';
	$canvas = new CanvasProjectJS($config);
} else {
	include 'canvas.php';
	$canvas = new Canvas($config);
}

if (isset($_POST['action'])) {
	$optimizely = $canvas->get_optimizely();
	$project = $optimizely->get_project($canvas->get_project_id());
}

if ((isset($_POST['action'])) && ($_POST['action'] == "disable")) {
	$canvas->disable($project);
} else if ((isset($_POST['action'])) && ($_POST['action'] == "enable")) {
	$canvas->enable($project);
}

if ($config->get("MAIN") == null) {
	$main = 'canvas-content.php';
} else {
	$main = $config->get("MAIN");
}

if ($config->get("SIDEBAR") != null) {
	$sidebar = $config->get("SIDEBAR");
} else {
	$sidebar = null;
}

?>

<html>
	<head>
  		<title><?php echo $canvas->get_app_name() ?></title>
  		<link rel="stylesheet" type="text/css" href="https://d2uaiq63sgqwfs.cloudfront.net/7.1.0/oui-canvas.css">
  		<link rel="stylesheet" type="text/css" href="css/canvas.css">
  		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
  		<script type="text/javascript" src="js/canvas.js"></script> 
 	</head>
 	<body>

		<div class="flex--1 flex" id="canvas-app">
			<div class="flex--1 flex flex--column" id="canvas-content">
				<?php include $main;?>
			</div> 
			<?php if ($sidebar != null) { ?>
			<div class="data-sidebar flex" id="canvas-sidebar">
				<?php include $sidebar;?>
			</div> 
			<?php } ?>
		</div> 
 </body>
</html>