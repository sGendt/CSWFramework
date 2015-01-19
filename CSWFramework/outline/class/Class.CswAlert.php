<?php

class CswAlert
{
	public function display()
	{
		if(!empty($_SESSION[ALERTSESSIONNAME]))
		{
			$template = file_get_contents(CswPref::pref('alertTemplatePath'));
			$template = str_replace(ALERTCSSPATTERN, $_SESSION[ALERTSESSIONNAME]->type, $template);
			$template = str_replace(ALERTMESSAGEPATTERN, $_SESSION[ALERTSESSIONNAME]->message, $template);

			CswString::p($template, true);
			$_SESSION[ALERTSESSIONNAME] = null;
		}
	}
}

?>
