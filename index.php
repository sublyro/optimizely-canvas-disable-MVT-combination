<?php include 'optimizely.php';?>
<html>
 <head>
  <title>Optimizely MVT Combination App</title>
 </head>
 <body>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');


// setup the object
$optimizely = new Optimizely('6e3a173e9e74caba0dbb34b3e1db612f:6a13b0a4');
//print_r($optimizely);
// get projects

$project_id = 4048040051;

//debug($project);

$all_sections = array();
$all_combinations = array();
$all_experiments = array();
$existing_disabled_combinations = array();

$experiments = $optimizely->get_experiments($project_id);
$project = $optimizely->get_project($project_id);

/*
$variations = array();
$variations['a'] = array('1', '2', '3');
$variations['b'] = array('a', 'b');
$variations['c'] = array('x', 'y', 'z', 'w');
$variations['d'] = array('10', '11');

$res = array();
getCombinations($variations, $res, null);*/

foreach ($experiments as $experiment) {
	if ($experiment->experiment_type == "multivariate") {   
		//debug("experiment " .$experiment->id);
		//debug($experiment);
		$all_experiments[$experiment->id] = $experiment;

        $variations = $optimizely->get_variations($experiment->id);
        //debug($variations);
		$sections = array();
		foreach ($variations as $variation) {

			if ($variation->is_paused != 1) {
				if (array_key_exists($variation->section_id, $sections) != 1) {
					$sections[$variation->section_id] = array();
				}
				array_push($sections[$variation->section_id], $variation);
				$all_sections[$variation->id] = $variation;
			}
		}
		//debug($sections);

		$combinations = array();
		//asort($sections);
		getCombinations($sections, $combinations, null);

		$all_combinations[$experiment->id] = $combinations;

    }
    //debug($sections);
}

$existing_disabled_combinations = getExistingConfig($project);

//debug($all_sections);
//debug($all_combinations);
//debug($existing_disabled_combinations);

//var_dump(json_decode('[{"id":4053480045, "disabled_combinations":["4055370049 4051590088","4042040132 4047950063"]},{"id":8765432, "disabled_combinations":["11111 22222","3333 44444","55555 66666"]}]', true));

//readLocalFile('project_js_template.js');

// ********************** END OF MAIN ***********************************


function getCombinationName($sections, $code) {
	$name = "";
	$code = explode(" ", trim($code));
	foreach ($code as $id) {
		$name .= $sections[$id]->description .' ';
	}
	return trim($name);
}


function getExistingConfig($project) {
	$combinations = array();

	$pjs = $project->project_javascript;

	preg_match("/optly_mvt.push\((.*)\);/i", $pjs, $res);
	$match = json_decode($res[1]);

	foreach ($match as $experiment) {
		foreach ($experiment->disabled_combinations as $combination) {
			$combinations[$combination] = $combination;
		}
	}
	return $combinations;
}


function readLocalFile($file) {
	$handle = fopen($file, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
	        //debug($line);
	    }

	    fclose($handle);
	} else {
	    // error opening the file.
	} 
}



function getCombinations($in, &$res, $current) {

	$out = $in;
	array_shift($out);

	// get the next section
	$section = array_values($in)[0];
	//debug($section);

	foreach ($section as $variation) {
		$tmp = $current .' ' .$variation->id;
		if (count($in) == 1) {
			// this is the last section
			array_push($res, reorderCombinations($tmp));
		} else {
			getCombinations($out, $res, $tmp);
		}
	}
}

function reorderCombinations($in) {
	$tmp = explode(" ", trim($in));
	//asort($tmp);
	return implode($tmp, " ");
}

function debug($msg) {
	echo "<pre>";
	print_r($msg);
	echo "</pre>";
}

function compareCombinations($combination, $existing_disabled_combinations) {
	//asort($combination);
	$c1 = explode(" ", trim($combination));
	asort($c1);
	$c1 = implode(" ", $c1);
	//debug($c1);
	foreach($existing_disabled_combinations as $disabled) {
		//debug($disabled);
		$c2 = explode(" ", trim($disabled));
		asort($c2);
		$c2 = implode(" ", $c2);
		if ($c1 == $c2) return true;
	}
	return false;
	//debug($existing_disabled_combinations);

}

?>

<div class="mvp-experiments">
<div class="project-id"><?php echo "Project Id: " .$project_id?></div>
<?php foreach($all_experiments as $experiment){ ?>
    <div class="experiment-id" id="<?php echo $experiment->id ?>"><?php echo "Experiment Name " .$experiment->description ?></div>
    <?php foreach($all_combinations[$experiment->id] as $combination) { ?>
	    <div class="combination">
	    	<?php
	    		$combination = trim($combination);
	    		$checked = true;
	    		//debug("c |" .$combination ."|");
	    		//compareCombinations($combination, $existing_disabled_combinations);
	    		//if (array_key_exists($combination, $existing_disabled_combinations) == 1) {
	    		if (compareCombinations($combination, $existing_disabled_combinations)) {
	    			//debug("ici");
	    			$checked = false;
	    		} 
	    	?>
			<input type="checkbox" id="c1" <?php if ($checked == true) {echo('checked');} ?> /><?php echo getCombinationName($all_sections, $combination) ?>
		</div>
	<?php } ?>
<?php
}
?>
</div>

</div>


 </body>
</html>