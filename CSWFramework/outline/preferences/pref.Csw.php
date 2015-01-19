<?php

CswPref::pref('defaultPage', 'accueil/');
CswPref::pref('forbiddenPage', 'forbidden/');


// local


CswPref::pref('mysqlHost', 'localhost');
CswPref::pref('mysqlUser', 'root');
CswPref::pref('mysqlPassword', '');
CswPref::pref('mysqlBdd', 'xxxxxxxxxxxx');
CswPref::pref('pathCdn', 'http://cdn.local.dev/');


// online

/*******/


CswPref::pref('folderCdn', '../cdn/');
CswPref::pref('alertTemplatePath', '../outline/layout/alert.php');


CswPref::pref('imageExtension', array('png', 'jpg', 'gif'));
CswPref::pref
(
	'imageSizes', 
	array
	(
		'250x250' => array(250, 250),
		'350x350' => array(350, 350)
	)
);

CswPref::pref
(
	'authorizedImages', 
	array
	(
		'jpg',
		'jpeg',
		'png',
		'gif'
	)
);

CswPref::pref
(
	'brandLogoMinSizes', 
	array
	(
		300,
		300,
	)
);

CswPref::pref
(
	'brandLogoMaxOctets', 
	8589934592
);

CswPref::pref
(
	'paimentTypes', 
	array
	(
		'transfert',
		'check',
		'card'
	)
);

CswPref::pref
(
	'pubTypes', 
	array
	(
		'home',
		'one',
		'vip'
	)
);

?>
