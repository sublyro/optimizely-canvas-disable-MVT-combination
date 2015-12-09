<?php include 'canvas.php';?>

<html>
	<head>
  		<title><?php echo $APP_NAME ?></title>
  		<link rel="stylesheet" type="text/css" href="https://app.optimizely.com/dist/css/app.css">
  		<link rel="stylesheet" type="text/css" href="canvas.css">
 	</head>
 	<body>


		<div class="flex--1 flex" id="canvas-app">
			<div class="flex--1 flex flex--column" id="canvas-content">
				<?php include 'canvas-content.php';?>
			</div> 
			<div class="lego-data-sidebar flex" id="canvas-sidebar">
				<?php include 'canvas-sidebar.php';?>
			</div> 
		</div> 
 </body>
</html>