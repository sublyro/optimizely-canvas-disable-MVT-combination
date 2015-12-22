<?php include 'canvas.php';?>

<?php

Class CanvasProjectJS extends Canvas {

function get_start_tag($escape = false) {
	$tag = "/**_START_" .$this->get_app_id() ."_" .$this->get_app_version() ."**/";
	if ($escape) {
		$tag = str_replace("/**", "\/\*\*", $tag);
		$tag = str_replace("**/", "\*\*\/", $tag);
	}
	return $tag;
}

function get_end_tag($escape = false) {
	$tag = "/**_END_" .$this->get_app_id() ."_" .$this->get_app_version() ."**/";
	if ($escape) {
		$tag = str_replace("/**", "\/\*\*", $tag);
		$tag = str_replace("**/", "\*\*\/", $tag);
	}
	return $tag;
}

function get_app_code($project) {
	preg_match("/" .$this->get_start_tag(true) ."(.*)" .$this->get_end_tag(true) ."/s", $project->project_javascript, $res);
	if (count($res) == 0) {
		return null;
	}
	return $res[0];
}

function replace_app_code($project, $new_code) {
	$pjs = $project->project_javascript;
	$optimizely = $this->get_optimizely();

	preg_match("/" .$this->get_start_tag(true) ."(.*)" .$this->get_end_tag(true) ."/s", $pjs, $res);
	if (count($res) == 0) {
		// add the config at the bottom of project_js
		$pjs .= "\n" .$new_code;
	} else {
		$pjs = preg_replace("/" .$this->get_start_tag(true) .".*" .$this->get_end_tag(true) ."/s", $new_code, $pjs);
	}
	$optimizely->update_project($project->id, array("project_javascript" => $pjs));
}

function disable($project) {
	$pjs = $project->project_javascript;
	$optimizely = $this->get_optimizely();

	preg_match("/" .$this->get_start_tag(true) ."(.*)" .$this->get_end_tag(true) ."/s", $pjs, $res);
	if (count($res) == 0) {
		//do nothing
	} else {
		$pjs = preg_replace("/" .$this->get_start_tag(true) ."(.*)" .$this->get_end_tag(true) ."/s", "", $pjs);
	}
	$x = $optimizely->update_project($project->id, array("project_javascript" => trim($pjs)));
}

function enable($project) {
	$pjs = $project->project_javascript;
	$optimizely = $this->get_optimizely();

	if ($this->get_app_code($project) == null) {
		$new_code = $this->get_start_tag() ."\n" .$this->get_end_tag();

		$pjs .= "\n" .$new_code;
		$optimizely->update_project($project->id, array("project_javascript" => $pjs));
	}
}


}