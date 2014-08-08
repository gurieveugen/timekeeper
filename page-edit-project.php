<?php
/**
 * Template name: Edit project
 */
?>
<?php
$project = new Project($_GET['id'], $GLOBALS['gc_session']['t']);
$project->save();
echo $project->editForm();