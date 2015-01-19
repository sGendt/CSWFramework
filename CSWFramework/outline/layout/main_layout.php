<?php 
header('Content-Type: text/html; charset=utf-8');  
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="author" content="xxxxxxxxxxxx">
		<meta name="category" content="xxxxxxxxxxxx">
		<meta name="copyright" content="xxxxxxxxxxxx">
		<meta name="language" content="fr">
		<meta name="distribution" content="global">
		<meta name="identifier-url" content="xxxxxxxxxxxx">
		<meta name="publisher" content="xxxxxxxxxxxx">
		<meta name="revisit-after" content="15 days">
		<meta name="title" content="xxxxxxxxxxxx">
		<meta name="description" content="xxxxxxxxxxxx">
		<meta name="keywords" content="xxxxxxxxxxxx">
		<link rel="icon" type="image/x-icon" href="<?php echo cswPref::pref('pathCdn'); ?>images/favicon.ico" />
		<script type="text/javascript" src="<?php echo cswPref::pref('pathCdn'); ?>js/jquery-1.8.3.min.js"></script>
		<script type="text/javascript" src="<?php echo cswPref::pref('pathCdn'); ?>js/CswAlert.js"></script>
		<script type="text/javascript" src="<?php echo cswPref::pref('pathCdn'); ?>js/tools.js"></script>
		<script type="text/javascript" src="<?php echo cswPref::pref('pathCdn'); ?>js/CswPopup.js"></script>
		<script type="text/javascript" src="<?php echo cswPref::pref('pathCdn'); ?>js/CswImageAlign.js"></script>
		<script type="text/javascript" src="<?php echo cswPref::pref('pathCdn'); ?>js/validateForm.js"></script>
		<?php $CswSnippet->addJs(); ?>
		<link type="text/css" href="<?php echo cswPref::pref('pathCdn'); ?>css/cswFramework.css" rel="stylesheet">
		<link media="screen" type="text/css" href="<?php echo cswPref::pref('pathCdn'); ?>css/style.css" rel="stylesheet">
		<?php $CswSnippet->addCss(); ?>
		
		<script type="text/javascript"></script>

		<title>xxxxxxxxxxxx</title>
	</head>
<body>
	<div class="container_12 main_content clear">
		<?php echo $content; ?>
	</div>
	<div id="mask">
		<div class="bkg"></div>
		<div class="msg"></div>
		<div id="loader">
			<div class="f_circleG" id="frotateG_01">
			</div>
			<div class="f_circleG" id="frotateG_02">
			</div>
			<div class="f_circleG" id="frotateG_03">
			</div>
			<div class="f_circleG" id="frotateG_04">
			</div>
			<div class="f_circleG" id="frotateG_05">
			</div>
			<div class="f_circleG" id="frotateG_06">
			</div>
			<div class="f_circleG" id="frotateG_07">
			</div>
			<div class="f_circleG" id="frotateG_08">
			</div>
		</div>
	</div>
	<div class="mask-popup"></div>
	<div class="popup">
		<div class="title-popup"><div></div> <span class="close-popup"></span></div>
		<div class="content-popup" id="content-popup"></div>
	</div>
	<?php $alert->display(); ?>
</body>
</html>