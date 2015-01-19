<?php

class CswSnippet
{

	protected $css;
	protected $js;
	protected $views;
	protected $controlers;

	/**
	 *
	 */

	public function __construct()
	{
		$this->css = array();
		$this->js = array();
	}


	public function add($snippet, $params = array())
	{
		extract($params);

		$this->css[$snippet] = '../outline/snippets/' . $snippet . '/css/' . $snippet . '.css';
		$this->js[$snippet] = '../outline/snippets/' . $snippet . '/js/' . $snippet . '.js';

		if(file_exists('../outline/snippets/' . $snippet . '/controlers/Controler.' . ucfirst($snippet) . '.php'))
			require '../outline/snippets/' . $snippet . '/controlers/Controler.' . ucfirst($snippet) . '.php';
		require '../outline/snippets/' . $snippet . '/views/' . $snippet . '.php';
	}

	public function addCss()
	{
		CswDirectory::deleteFolder('../cdn/css/snippets/');
		CswDirectory::folder('../cdn/css/snippets/');

		$css = '';

		foreach($this->css as $uri)
		{
			if(file_exists($uri))
			{
				CswDirectory::copyFolder
				(
					$uri, 
					'../cdn/css/snippets/'
				);

				$css .= '<link media="screen" type="text/css" href="' . CswPref::pref('pathCdn') . '/css/snippets/' . basename($uri) . '" rel="stylesheet">';
			}
		}

		echo $css;
	}

	public function addJs()
	{
		CswDirectory::deleteFolder('../cdn/js/snippets/');
		CswDirectory::folder('../cdn/js/snippets/');

		$js = '';

		foreach($this->js as $uri)
		{
			if(file_exists($uri))
			{
				CswDirectory::copyFolder
				(
					$uri, 
					'../cdn/js/snippets/'
				);

				$js .= '<script type="text/javascript" src="' . CswPref::pref('pathCdn') . '/js/snippets/' . basename($uri) . '"></script>';
			}
		}

		echo $js;
	}

	

}


?>