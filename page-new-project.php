<?php
/**
 * Template name: New project
 */
?>
<?php
$project = new Project(null, $GLOBALS['gc_session']['t']);
$project->save();
echo $project->editForm();
