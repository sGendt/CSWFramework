<?php
class CswDate
{
	private static $date;
	private static $feedback;
	private static $format;
	private static $french_day = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	private static $french_month = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

	
	public static function getYear()
	{
		self::$feedback = explode('-', self::$date) ;
		return self::$feedback[0] ;
	}
	
	public static function getMonthInt()
	{
		self::$feedback = explode('-', self::$date) ;
		return self::$feedback[1] ;
	}
	
	public static function getMonthFText()
	{
		self::$feedback = explode('-', self::$date) ;
		$key = self::$feedback[1] - 1 ;
		return self::$french_month[$key] ;
	}
	
	
	public static function getDayInt()
	{
		self::$feedback = explode('-', self::$date) ;
		self::$feedback = explode(' ', self::$feedback[2]) ;
		return self::$feedback[0] ;
	}
	
	public static function getDayFText()
	{
		// correct bu to use correctly
		self::$feedback = explode('-', self::$date) ;
		self::$feedback = explode(' ', self::$feedback[2]) ;
		$key = self::$feedback[0] ;
		return self::$french_day[$key -1] ;
	}
	
	public static function getHour()
	{
		self::$feedback = explode(' ', self::$date) ;
		self::$feedback = explode(':', self::$feedback[1]) ;
		return self::$feedback[0] ;
	}
	
	public static function getMinute()
	{
		self::$feedback = explode(' ', self::$date) ;
		self::$feedback = explode(':', self::$feedback[1]) ;
		return self::$feedback[1] ;
	}
	
	public static function getSecond()
	{
		self::$feedback = explode(' ', self::$date) ;
		self::$feedback = explode(':', self::$feedback[1]) ;
		return self::$feedback[2] ;
	}

	public static function setTimestamp($timestamp)
	{
		self::$date = date('Y-m-d H:i:s', $timestamp) ;
	}

	public static function setDate($date)
	{
		self::$date = $date;
	}

	public static function getTimestamp($date)
	{
		$date = new DateTime($date);
		return  $date->getTimestamp();
	}

	public static function setformat($format)
	{
		self::$format = $format;
	}

	public static function getDate()
	{
		return self::$date;
	}


	public static function getStepInterval($timeStart, $timeEnd)
	{
		$dateStart = new DateTime();
		$dateStart->setTimestamp($timeStart);

		$dateEnd = new DateTime();
		$dateEnd->setTimestamp($timeEnd);

		$interval = $dateEnd->diff($dateStart);

		if($interval->y > 0)
			return 'y';
		elseif($interval->m > 0)
			return 'm';
		elseif($interval->d > 0)
			return 'd';
		elseif($interval->h > 0)
			return 'h';
		elseif($interval->i > 0)
			return 'i';
		elseif($interval->s > 0)
			return 's';
		else
			return false;
	}

	public static function getInterval($timeStart, $timeEnd)
	{
		$dateStart = new DateTime();
		$dateStart->setTimestamp($timeStart);

		$dateEnd = new DateTime();
		$dateEnd->setTimestamp($timeEnd);
		
		return $dateEnd->diff($dateStart);
	}

	public function getFuturTime($value, $type)
	{
		if($type == 'y')
			return mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear() + $value);
		elseif($type == 'm')
			return mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt() + $value, self::getDayInt(), self::getYear());
		elseif($type == 'd')
			return mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt() + $value, self::getYear());
		elseif($type == 'h')
			return mktime(self::getHour() + $value, self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear());
		elseif($type == 'i')
			return mktime(self::getHour(), self::getMinute() + $value, self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear());
		elseif($type == 's')
			return mktime(self::getHour(), self::getMinute(), self::getSecond() + $value, self::getMonthInt(), self::getDayInt(), self::getYear());
		else
			return false;
	}

	public function getPastTime($value, $type)
	{
		if($type == 'y')
			return mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear() - $value);
		elseif($type == 'm')
			return mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt() - $value, self::getDayInt(), self::getYear());
		elseif($type == 'd')
			return mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt() - $value, self::getYear());
		elseif($type == 'h')
			return mktime(self::getHour() - $value, self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear());
		elseif($type == 'i')
			return mktime(self::getHour(), self::getMinute() - $value, self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear());
		elseif($type == 's')
			return mktime(self::getHour(), self::getMinute(), self::getSecond() - $value, self::getMonthInt(), self::getDayInt(), self::getYear());
		else
			return false;
	}

	public function getFuturDate($value, $type)
	{
		if($type == 'y')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear() + $value));
		elseif($type == 'm')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt() + $value, self::getDayInt(), self::getYear()));
		elseif($type == 'd')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt() + $value, self::getYear()));
		elseif($type == 'h')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour() + $value, self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear()));
		elseif($type == 'i')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute() + $value, self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear()));
		elseif($type == 's')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond() + $value, self::getMonthInt(), self::getDayInt(), self::getYear()));
		else
			self::$date = date('Y-m-d H:i:s');

		return self::$date;
	}

	public function getPastDate($value, $type)
	{
		if($type == 'y')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear() - $value));
		elseif($type == 'm')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt() - $value, self::getDayInt(), self::getYear()));
		elseif($type == 'd')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt() - $value, self::getYear()));
		elseif($type == 'h')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour() - $value, self::getMinute(), self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear()));
		elseif($type == 'i')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute() - $value, self::getSecond(), self::getMonthInt(), self::getDayInt(), self::getYear()));
		elseif($type == 's')
			self::$date = date('Y-m-d H:i:s', mktime(self::getHour(), self::getMinute(), self::getSecond() - $value, self::getMonthInt(), self::getDayInt(), self::getYear()));
		else
			self::$date = date('Y-m-d H:i:s');

		return self::$date;
	}
}
?>