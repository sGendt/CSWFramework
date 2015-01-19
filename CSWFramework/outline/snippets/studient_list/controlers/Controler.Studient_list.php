<?php

$user = new User();
$studient = new Studient();

if(CswVar::v('delete'))
{
	$studient->load(CswVar::v('studientId'));
	
	$user->delete($studient->getUserId());
	$studient->delete(CswVar::v('studientId'));
}

$story 	= new Story();
$user = new User();
$rows = $user->getAllStudients($userId, CswVar::v('formationId'));

foreach($rows as &$stud)
{
	$bookmarks = $story->getBookmarkByAppIdAndUserId(CswVar::v('formationId'), $stud->id);
	$studText = '';

	foreach($bookmarks as $bookmark)
		$studText .= ' ' . trim($bookmark->value);

	$stud->nbPages = getNbPages($studText);
}

?>