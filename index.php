<?php
/**
 * Tableau Connector External Module
 * File index.php is the Instructions page
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
if (defined('PROJECT_ID')) {
    include APP_PATH_DOCROOT . 'ProjectGeneral/header.php';
} else {
    include APP_PATH_DOCROOT . 'ControlCenter/header.php';
}
$module = new MCRI\TableauConnector\TableauConnector();
$module->printInstructionsPageContent();

if (defined('PROJECT_ID')) {
    include APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
} else {
    include APP_PATH_DOCROOT . 'ControlCenter/footer.php';
}