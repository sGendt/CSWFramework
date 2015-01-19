<?php

abstract class CswPagination
{

	public static function getNbPages($nbRows, $nbElementsPerPage = null)
	{
		if($nbElementsPerPage != null && is_numeric($nbElementsPerPage))
			return ceil($nbRows / $nbElementsPerPage);
		else
			return ceil($nbRows / NBELEMENTSPERPAGE);
	}

	public static function getIntervalRowsDisplay($currentPage, $nbElementsPerPage = null)
	{
		if($nbElementsPerPage != null && is_numeric($nbElementsPerPage))
			return array
			(
				($currentPage * $nbElementsPerPage) - ($nbElementsPerPage - 1),
				(($currentPage * $nbElementsPerPage) + 1)
			);
		else
			return array
			(
				($currentPage * NBELEMENTSPERPAGE) - (NBELEMENTSPERPAGE - 1),
				(($currentPage * NBELEMENTSPERPAGE) + 1)
			);
	}

	public static function isDisplayed($i, $intervals = array())
	{
		if($i >= $intervals[0] && $i < $intervals[1])
			return true;
		else
			return false;
	}
}

?>
