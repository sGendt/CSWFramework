<?php


$command = new Command();
$command->load(CswVar::v('formationId'));

$studient = new Studient();

$nbPages = getNbPages($text);

// book information

$information = new stdClass();
$information->id = null;

$bookInformation = new Book_information();
$informationState = new StdClass;
$informationState->error = 1;
$informationState->pages = 1;
$informationState->bookInfosError = 1;
$informationState->studientPagesError = 1;
$informationState->studientInfosError = 1;


$rows = $bookInformation->getByUser($userId, CswVar::v('formationId'));

if(!empty($rows))
{
	$information = $rows[0];

	/* state publish */
	
	$informations =  $bookInformation->isCompleted($information->id);
	$informationState = (object) array_merge((array) $informationState, (array) $informations);

	if($nbPages >= MINPAGES)
	{
		$informationState->pages = 0;
		$informationState->error = 0;
	}
	else
	{
		$informationState->error = 1;
	}

	$studientInfos = $studient->getValidateByStudient(CswVar::v('formationId'));
	$informationState = (object) array_merge((array) $informationState, (array) $studientInfos);

	if($informationState->studientPagesError == 1 || $informationState->studientInfosError == 1)
		$informationState->error = 1;

}

$pathIcoState = CswPref::pref('pathCdn') . 'images/';

?>
