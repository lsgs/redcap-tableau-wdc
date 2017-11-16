<?php
/**
 * Tableau Connector External Module
 * File wdc.php is for the Web Data Connector URL
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
use HtmlPage;
$page = new HtmlPage();
$page->PrintHeaderExt();

$module = new MCRI\TableauConnector\TableauConnector();
$module->printConnectorPageContent();

print '</body></html>';