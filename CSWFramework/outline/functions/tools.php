<?php

function getNbPages($text)
{
	$text = trim($text);
	$textLength = strlen($text);
	return ($textLength > 0) ? ceil($textLength / NBCHARSPERPAGE) : 0;
}

?>