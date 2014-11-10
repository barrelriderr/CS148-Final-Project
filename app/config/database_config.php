<?php

/* ------------------------------------------------
 * Enter your database configuration settings here.
 * The first section is for developers using a VPS.
 * DELETE FIRST SECTION WHEN SITE GOES LIVE!
 * The second section is for the settings on the 
 * production server.
 * - - -
 */


// Developer Server Settings (remove when live)
 if($_SERVER["REMOTE_ADDR"] == '127.0.0.1'){
	$database_host = '127.0.0.1';
	$database_name = 'buildr';
	$database_username = 'root';
	$database_password = 'Gguest1998';
}

// Production Server Settings
else {
	$database_host = 'webdb.uvm.edu';
	$database_name = 'BTNEWTON_buildr';
	$database_username = 'btnewton_admin';
	$database_password = 'Yid0dcG5czVNvSRE';
}