<?php

// Dependency

//require_once 'libs/phpmailer/class.phpmailer.php';
require 'libs/PHPMailer-master/PHPMailerAutoload.php';
require_once 'libs/PHPMailer-master/class.phpmailer.php';


/**
 *
 */

class CswMail
{
	private $phpMailer = null;
	private $sendType = null;
	private $template = null;


	/**
	 *
	 */

	public function __construct($sendType = null, $template = null)
	{
		$this->sendType = (empty($sendType)) ? MAILPHP : $sendType;
		$this->template = $template;
	}


	/**
	 * Descriptif de la classe phpMailer
	 *
	 * Méthode 																					Description	
	 *
	 * AddAddress (string $address, [string $name]) 											Action: ajout des destinataires. $adress représente le destinataire, $name est optionnel et représente le nom correspondant à l'adresse	
	 * AddCC (string $address, [string $name]) 													Action: ajout des destinataires en copie. $adress représente le destinataire, $name est optionnel et représente le nom correspondant à l'adresse	
	 * AddBCC (string $address, [string $name]) 												Action: ajout des destinataires en copie cachée. $adress représente le destinataire, $name est optionnel et représente le nom correspondant à l'adresse	
	 * AddReplyTo (string $address, [string $name]) 											Action: définit l'adresse de retour en cas d'échec. $adress représente le destinataire, $name est optionnel et représente le nom correspondant à l'adresse	
	 * AddAttachment(string $path, [string $name = ""], [string $encoding], [string $type]) 	Action: ajout d'un fichier en attachement, $path:chemin du fichier, $name:nom de l'attachement, $encoding:format d'encodage, $type:en-tête. $name, $encoding et $type sont optionnels.	
	 * ClearAddresses() 																		Action: vide le tableau contenant les adresses des destinataires	
	 * ClearAllRecipients() 																	Action: vide les tableaux contenant les adresses des destinataires, en copie et en copie cachée	
	 * ClearAttachments() 																		Action: supprime tous les attachements	
	 * ClearBCCs() 																				Action: supprime tous les destinataires en copie cachée	
	 * ClearCCs() 																				Action: supprime tous les destinataires en copie	
	 * ClearReplyTos() 																			Action: supprime tous les destinataires en adresse de retour	
	 * IsHTML($bool) 																			Action: spécifie si le corps du mail est du HTML, l'argument $bool est true ou false	
	 * IsSMTP($bool) 																			Action: spécifie que le mail sera envoyé sur un serveur SMTP	
	 * IsQmail() 																				Action: spécifie que le server mail local est Qmail	
	 * IsMail() 																				Action: spécifie que la fonction mail de PHP doit être utilisée	
	 * IsSendMail() 																			Action: spécifie que le programme sendmail doit être utilisé	
	 * Send() 																					Action: envoie l'e-mail	
	 *
	 * Propriété 																				Description	
	 *
	 * $Host																					Spécifie l'hôte (ou les hôtes)SMTP auquel il faut se connecter. Par défaut, le port utilisé est 25, on peut en spécifier un autre comme ceci: $mail->Host='hote:port' où port est le numéro de port
	 * $From																					Spécifie l'adresse de l'expéditeur
	 * $FromName																				Spécifie le nom de l'expéditeur
	 * $Body																					Contient le corps du message à envoyer
	 * $SMTPAuth																				Valeur: true ou false, true spécifie que le serveur SMTP exige une anthentification
	 * $Username																				Valeur: nom de l'utilisateur SMTP (nécessaire seulement si le serveur demande une authentification
	 * $Password																				Valeur: mot de passe de l'utilisateur SMTP (nécessaire seulement si le serveur demande une authentification
	 * $SMTPDebug																				Valeur: true ou false. Si la valeur est true, le débogeur sera activé. Très intéressant lorsqu'il y a des problème d'envoi
	 * $Subject																					Définit le sujet du mail
	 */


	public function send($mails = array(), $subject, $content, $attachements = array(), $ccs = array(), $bccs = array())
	{	
		$this->phpMailer = new PHPmailer();
		$this->phpMailer->CharSet = MAILCHARSET;

		// Config send type: mail() || SMTP

		if($this->sendType == MAILPHP)
			$this->phpMailer->IsMail();

		if($this->sendType == MAILSMTP)
		{
			$this->phpMailer->IsSMTP(true);
			$this->phpMailer->SMTPAuth = true; 
			//$this->phpMailer->SMTPDebug = true; 
			$this->phpMailer->Host = MAILHOST;
			$this->phpMailer->Port = MAILPORT;
	        $this->phpMailer->Username = MAILUSER; 
	        $this->phpMailer->Password = MAILPASSWORD;
    	}

		$this->phpMailer->IsHTML(true);

		$this->phpMailer->Sender = MAILFROM;
		$this->phpMailer->From = MAILFROM;
		$this->phpMailer->FromName = MAILFROMNAME;
		$this->phpMailer->AddReplyTo(MAILREPLY);


		// Set recipients

		if(!empty($mails))
			foreach($mails as $mail)
				$this->phpMailer->AddAddress($mail);


		// Set field cc and bcc, only for the SMTP send type

		if($this->sendType == MAILSMTP)
		{
			if(!empty($ccs))
				foreach($ccs as $cc)
					$this->phpMailer->AddCC($cc);

			if(!empty($bccs))
				foreach($bccs as $bcc)
					$this->phpMailer->AddBCC($bcc);
		}

		// Set attachements

		if(!empty($attachements))
			foreach($attachements as $attachement)
				$this->phpMailer->AddAttachment($attachement);

		// Set subject

		$this->phpMailer->Subject = $subject;

		// Get template

		if($this->template != null)
			if(file_exists($this->template))
			{
				$template = file_get_contents($this->template);
				$content = str_replace(MAILCONTENTPATTERN, $content, $template);
			}
		
		// Set content

		$this->phpMailer->Body = $content;

		// Send
				
		try 
		{
			if(!$this->phpMailer->Send())
				throw new Exception('Mail state error', 1); 
		} 
		catch (Exception $e) 
		{
				echo 'Debug: ' . $e->getMessage();
		}

		// Clear	

		$this->phpMailer->SmtpClose();
		unset($this->phpMailer);
	}


	/**
	 *
	 */

	public function setSendType($sendType)
	{
		$this->sendType = $sendType;
	}


	/**
	 *
	 */

	public function setTemplate($template)
	{
		$this->template = $template;
	}


	/**
	 *
	 */

	public function getSendType()
	{
		return $this->sendType;
	}


	/**
	 *
	 */

	public function getTemplate()
	{
		return $this->template;
	}
}

?>