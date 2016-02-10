<?php

$all_sections = array();
$all_combinations = array();
$all_experiments = array();
$existing_disabled_combinations = array();
$new_disabled_combinations = array();
$error_message = "";
$optimizely = $canvas->get_optimizely();


$project = $optimizely->get_project($canvas->get_project_id());
$experiments = $optimizely->get_experiments($canvas->get_project_id());


if ($project == null || property_exists($project, 'status')) {
	$error_message = "An error occured while loading the app! Please check the config";
} else {
foreach ($experiments as $experiment) {
	if ($experiment->experiment_type == "multivariate") {   
		$all_experiments[trim($experiment->id)] = $experiment;

        $variations = $optimizely->get_variations($experiment->id);
		$sections = array();
		foreach ($variations as $variation) {
			if ($variation->is_paused != 1) {
				
				if (array_key_exists(trim($variation->section_id), $sections) != 1) {
					$sections[trim($variation->section_id)] = array();
				}
				array_push($sections[trim($variation->section_id)], $variation);
				$all_sections[trim($variation->id)] = $variation;
			}
		}

		$combinations = array();
		getCombinations($sections, $combinations, null);

		$all_combinations[trim($experiment->id)] = $combinations;

    }
}

// the form was saved for we need to update the projectJS
if ((isset($_POST['action'])) && ($_POST['action'] == "save")) {
//if (count($_POST) > 0 && ) {
	// process the POST parameters
	// the POST contains all the combination that are enabled
	// go through all the possible combinations and see if they were submited
	$tmp_json_str = array();
	$json_str = "window.optly_mvt.push([__MVT_COMBINATIONS__]);";

	foreach ($all_combinations as $experiment_id => $experiment) {
		$json_str_experiment = "{\"id\":__ID__, \"disabled_combinations\":[__MVT_COMBINATION__]}";
		$tmp = array();
		$has_exclusion = false;
		foreach ($experiment as $combination) {
			$combination = str_replace(" ", "_", $combination);
			if (!array_key_exists($combination, $_POST)) {
				array_push($tmp, "\"" .str_replace("_", " ", $combination) ."\"");
				$has_exclusion = true;
			}
		}
		$json_str_experiment = str_replace("__ID__", $experiment_id, $json_str_experiment);
		$json_str_experiment = str_replace("__MVT_COMBINATION__", implode(",", $tmp), $json_str_experiment);

		if ($has_exclusion) {
			array_push($tmp_json_str, $json_str_experiment);
		}
	}

	$new_code = readLocalFile("project_js_template.js");
	if (count($tmp_json_str) == 0) {
		$new_code = str_replace("__MVT_CONFIG__", "", $new_code);
	} else {
		$new_code = str_replace("__MVT_CONFIG__", implode(",", $tmp_json_str), $new_code);
	}
	$pjs = $project->project_javascript;

	$canvas->replace_app_code($project, $new_code);

	// get the project again with updated project JS
	$project = $optimizely->get_project($canvas->get_project_id());
}

// show which combinations are disabled on the UI
$existing_disabled_combinations = getExistingMVTConfig($canvas, $project);

//disableCanvasApp($canvas, $project);
}

// ********************** END OF MAIN ***********************************


function getCombinationName($sections, $code) {
	$name = "";
	$code = explode(" ", trim($code));
	foreach ($code as $id) {
		$name .= $sections[trim($id)]->description .' ';
	}
	return trim($name);
}

function getMVTConfig($canvas, $project) {
	$app_code = $canvas->get_app_code($project);
	if ($app_code == null) {
		// no existing config. disable the app
		$canvas->set_status(0);
	}
	if ($app_code == null) return null;
	preg_match("/optly_mvt.push\((.*)\);/i", $app_code, $res);
	if (count($res) == 0) return null;
	return json_decode($res[1]);
}


function getExistingMVTConfig($canvas, $project) {
	$combinations = array();

	$match = getMVTConfig($canvas, $project);
	if ($match != null) {
		foreach ($match as $experiment) {
			foreach ($experiment->disabled_combinations as $combination) {
				$combinations[trim($combination)] = $combination;
			}
		}
	}
	return $combinations;
}


function readLocalFile($file) {
	$res = "";
	$handle = fopen($file, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
	        $res .= $line;
	    }
	    fclose($handle);
	    return $res;
	} else {
	    // error opening the file.
	} 
}



function getCombinations($in, &$res, $current) {
	$out = $in;
	array_shift($out);

	// get the next section
	$section = array_values($in)[0];
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
	return implode($tmp, " ");
}

function compareCombinations($combination, $existing_disabled_combinations) {
	$c1 = explode(" ", trim($combination));
	asort($c1);
	$c1 = implode(" ", $c1);
	foreach($existing_disabled_combinations as $disabled) {
		$c2 = explode(" ", trim($disabled));
		asort($c2);
		$c2 = implode(" ", $c2);
		if ($c1 == $c2) return true;
	}
	return false;
}

?>

<?php
if ($error_message != "") {
?>
<div><?php echo $error_message ?></div>
<?php } else { ?>
<?php if (!$canvas->is_enabled()) { ?>

<br />
<div class="attention background--warning">
  This app is currently disabled. To enable it click on the "On" button in the sidebar
</div>

<?php } ?>


<form id="mvt-app-form" method="POST">
	<input type="hidden" name="action" id="action" value="save" />
  	<?php foreach($all_experiments as $experiment){  ?>
    <div class="mvt-experiment">
      <div class="experiment-id" id="<?php echo $experiment->id ?>">
        <h2><?php echo $experiment->description ?> (<?php echo $experiment->status ?>)</h2></div>
      <ul class="input-list">
        <?php foreach($all_combinations[trim($experiment->id)] as $combination) { ?>
          <div class="combination">
            <?php
			    		$combination = trim($combination);
			    		$checked = true;
			    		if (compareCombinations($combination, $existing_disabled_combinations)) {
			    			$checked = false;
			    		} 
			    		if (!$canvas->is_enabled()) {
			    			$checked = true;
			    		}
			    	?>
              <li>
                <input name="<?php echo $combination ?>" type="checkbox" <?php if ($checked==true) {echo('checked');} ?> <?php if (!$canvas->is_enabled()) {echo('disabled');} ?> />
                <label>
                  <?php echo getCombinationName($all_sections, $combination) ?>
                </label>
              </li>
          </div>
          <?php } ?>
      </ul>
      <button type="submit" class="button button--highlight <?php if (!$canvas->is_enabled()) {echo('button--disabled');} ?>" data-test-section="save-button" <?php if (!$canvas->is_enabled()) {echo('disabled=\"disabled\"');} ?>> Save </button>

    </div>
    <?php
		}
		?>
</form>

    <?php
		}
		?>
