<?php

// local

/*ini_set('display_errors', 1);
error_reporting(E_ALL); */

//session_set_cookie_params(0, '/', '.opticien-vitrine.dev');

// online

session_set_cookie_params(3600, '/', '.varigame.dev');

/*phpinfo();
die();*/
//ini_set('session.cookie_domain', substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], "."), 100));
ini_set('session.use_trans_sid', 0);
session_start();


/////////////////////////////////////////////////////////////////////////////////////////
// on se connecte
/*$memcache = new Memcache();
$memcache->connect('localhost', 11211) or die ('impossible de se connecter');
//on écrit avec une validité de 10 secondes
$test = 'du texte';
$memcache->set('test', $test, false, 10) or die ('echec sur ecriture');
//on lit
echo $memcache->get('test');*/
//////////////////////////////////////////////////////////////////////////////////////////

require_once dirname(__FILE__) . '/../ini_path.php';

require_once 'configuration/Config.CswConfiguration.php';
require_once 'preferences/pref.Csw.php';

require_once ('functions/returnJsonToJquery.php');
require_once ('functions/cookie.php');
require_once ('functions/tools.php');
require_once ('functions/getAllHeaders.php');

loadConstant();
loadCore();

$exchangeBdd = DatabaseConnectionManager::getDatabaseConnectionManager();

CswConst::c('URI', $_SERVER['REQUEST_URI']);

$urlFragments = CswPath::getUrlFragments();

extract($urlFragments);

$returnType  = 'default';

$auth = new CswAuth();
$alert = new CswAlert();
$CswSnippet = new CswSnippet();

//die(CswString::ashString('12345'));

if(empty($_SESSION['history']))
	$_SESSION['history'] = array();

$_SESSION['history'][] = CURRENTURL;

// request

$httpReferer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : URL;

if(!empty($_GET) || !empty($_POST))
{
	if
	(
		preg_match("/".HOSTNAME."/", $httpReferer) ||
		preg_match("/".APPHOSTNAME."/", $httpReferer)
	)
	{
		try
		{
			if(!empty($_GET['action']) || !empty($_POST['action']))
			{
				$action = CswVar::v('action');
				$class = CswVar::v('requested');

				if($action == ACTION_AJAX)
				{
					$returnType = ACTION_AJAX;
				}

				if($action == ACTION_AJAX_ACTION)
				{
					header("Access-Control-Allow-Origin:*", true);
					header("Access-Control-Allow-Methods: GET,OPTIONS", true);
					header("Access-Control-Allow-Headers: x-requested-with", true);

					$parts = explode('.', $class);
					$class = ucfirst($parts[1]);
					$method = $parts[2];

					$model = new $class();
					$datas = $model->$method();

					$callback = (empty($_GET['callback']) == false) ? $_GET['callback'] : false;
					$datas['datas'] = $datas;
					$json = json_encode($datas);

					returnJsonToJquery();

					exit();
				}
				
				if(preg_match("/form/", $action))
				{
					$parts = explode('.', $class);
					$class = ucfirst($parts[1]);
					$method = $parts[2];

					$model = new $class();
					$model->$method();

					$location = (CswVar::v('errorLocation') && $model->getReturnState()->type != ALERTSUCCES) ? CswVar::v('errorLocation') : CswVar::v('successLocation');

					header('location:'. $location);

					exit();
				}
			}
		}
		catch (Exception $e) 
		{
		    echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	else
	{
		die('Wrong way!');
	}
}

//

if (strtolower($class) == 'sitemap.xml')
{
	$sitemap = file_get_contents(strtolower($class));
	
	die($sitemap);
}

$class = (file_exists('views/' . strtolower($class) . '.php') == true) ? strtolower($class) : '404';

$titleSite = CswPref::pref($class . '_title');
$descriptionSite = CswPref::pref($class . '_description');
$keywordsSite = CswPref::pref($class . '_keywords');
$currentPage = '';

//die($class);

ob_start();

if(file_exists('../outline/controlers/Controler.' . ucfirst($class) . '.php'))
	require_once '../outline/controlers/Controler.' . ucfirst($class) . '.php';
require_once 'views/' . $class . '.php';

//extract($headers);

$content = ob_get_clean();

switch($returnType)
{
	case 'default':
		require_once ('layout/main_layout.php');
	break;

	case ACTION_AJAX:
		$callback = (empty($_GET['callback']) == false) ? $_GET['callback'] : false;
		$cont['datas'] = $content;
		$json = json_encode($cont);

		returnJsonToJquery();
	break;

	default:
		require_once ('layout/main_layout.php');
}

//

function loadConstant()
{
	foreach(glob('../outline/constants/*.php') as $file)
	{
		require_once $file;
	}
}

function loadCore()
{
	foreach(glob('../outline/core/*.php') as $file)
	{
		require_once $file;
	}
}

?>