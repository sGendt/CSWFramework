<?php
class date
{
	private $date;
	private $feedback;
	private $french_day = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	private $french_month = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	
	public function __CONSTRUCT($date = null)
	{
		$this->date = ($date != null) ? $date : date('Y-m-d H:i:s') ;
	}
	
	public function getYear()
	{
		$this->feedback = explode('-', $this->date) ;
		return $this->feedback[0] ;
	}
	
	public function getMonthInt()
	{
		$this->feedback = explode('-', $this->date) ;
		return $this->feedback[1] ;
	}
	
	public function getMonthFText()
	{
		$this->feedback = explode('-', $this->date) ;
		$key = $this->feedback[1] - 1 ;
		return $this->french_month[$key] ;
	}
	
	
	public function getDayInt()
	{
		$this->feedback = explode('-', $this->date) ;
		$this->feedback = explode(' ', $this->feedback[2]) ;
		return $this->feedback[0] ;
	}
	
	public function getDayFText()
	{
		$this->feedback = explode('-', $this->date) ;
		$this->feedback = explode(' ', $this->feedback[2]) ;
		$key = $this->feedback[0] - 1 ;
		return $this->french_day[$key] ;
	}
	
	public function getHour()
	{
		$this->feedback = explode(' ', $this->date) ;
		$this->feedback = explode(':', $this->feedback[0]) ;
		return $this->feedback[0] ;
	}
	
	public function getMinute()
	{
		$this->feedback = explode(' ', $this->date) ;
		$this->feedback = explode(':', $this->feedback[1]) ;
		return $this->feedback[0] ;
	}
	
	public function getSecond()
	{
		$this->feedback = explode(' ', $this->date) ;
		$this->feedback = explode(':', $this->feedback[2]) ;
		return $this->feedback[0] ;
	}
}
?>