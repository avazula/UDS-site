<?php
/*
	********************************************************************************************
	CONFIGURATION
	********************************************************************************************
*/
// destinataire est votre adresse mail. Pour envoyer à plusieurs à la fois, séparez-les par une virgule
// On regarde à quelle destinataire envoyer le mail en fonction de la catégorie
if(isset($_POST['category'])){
        if($_POST['category'] == 'prog')
            $destinataire = 'buro@undessens.fr';
        else if($_POST['category'] == 'comm')
            $destinataire = 'buro@undessens.fr';
        else if($_POST['category'] == 'subv')
            $destinataire = 'buro@undessens.fr';
        else if($_POST['category'] == 'part')
			$destinataire = 'buro@undessens.fr';
		else if ($_POST['category'] == 'autre')
			$destinataire = 'buro@undessens.fr';
}
else{ // On envoie à l'adresse par défaut
    $destinataire = 'contact@undessens.fr';
}
    
 /*
 LE BON CONTACT A METTRE EST CELUI CI : contact@undessens.fr
 */
// copie ? (envoie une copie au visiteur)
$copie = 'oui'; // 'oui' ou 'non'

// Messages de confirmation du mail
$message_envoye = "Votre message nous est bien parvenu ! <a href=\"./index.html\"> Retour au site</a>.";
$message_non_envoye = "L'envoi du mail a échoué, veuillez réessayer SVP. <a href=\"./index.html#contact\"> Retour au site</a>.";

// Messages d'erreur du formulaire
$message_erreur_formulaire = "Vous devez d'abord <a href=\"./index.html#Contact\">envoyer le formulaire</a>.";
$message_formulaire_invalide = "Vérifiez que tous les champs soient bien renseignés et que l'email soit sans erreur.";

/*
	********************************************************************************************
	FIN DE LA CONFIGURATION
	********************************************************************************************
*/

// on teste si le formulaire a été soumis
if (!isset($_POST['name']) ||
!isset($_POST['email']) ||
!isset($_POST['category']) ||
!isset($_POST['subject']) ||
!isset($_POST['message']))
{
	// formulaire non envoyé
	echo '<p>'.$message_erreur_formulaire.'</p>'."\n";
	// died('Une erreur est survenue avec le formulaire. Merci de retenter plus tard.')
}
else
{
	/*
	 * cette fonction sert à nettoyer et enregistrer un texte
	 */
	function Rec($text)
	{
		$text = htmlspecialchars(trim($text), ENT_QUOTES);
		if (1 === get_magic_quotes_gpc())
		{
			$text = stripslashes($text);
		}

		$text = nl2br($text);
		return $text;
	};
    
    // Retourne un en-Tête pour l'objet du mail
    function ObjectHeader($category){
        if($category == 'prog')
            return '[Prog]';
        else if($category == 'comm')
            return '[Com]';
        else if($category == 'subv')
            return '[Sub]';
        else if($category == 'part')
            return '[Part]';
        else
            return '[Autre]';
    }

	/*
	 * Cette fonction sert à vérifier la syntaxe d'un email
	 */
	function IsEmail($email)
	{
		$value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
		return (($value === 0) || ($value === false)) ? false : true;
	}

	// formulaire envoyé, on récupère tous les champs.
	$nom     = (isset($_POST['name']))     ? Rec($_POST['name'])     : '';
	$email   = (isset($_POST['email']))   ? Rec($_POST['email'])   : '';
	// $categorie   = (isset($_POST['category']))   ? Rec($_POST['category'])   : '';
	$objet   = (isset($_POST['subject']))   ? Rec($_POST['subject'])   : '';
	$message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';
    // On récupère l'en-tête de l'objet
    if(isset($_POST['category']))
        $objetHeader = ObjectHeader($_POST['category']);
    // else
    //     $objetHeader ='[Autres]';
    
	// On va vérifier les variables et l'email ...
	$email = (IsEmail($email)) ? $email : ''; // soit l'email est vide si erroné, soit il vaut l'email entré

	if (($nom != '') && ($email != '') && ($objet != '') && ($message != ''))
	{
		// les 4 variables sont remplies, on génère puis envoie le mail
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'From:'.$nom.' <'.$email.'>' . "\r\n" .
					'Reply-To:'.$email. "\r\n" .
					'X-Mailer:PHP/'.phpversion()."\r\n";
        // Correction des accents ?
        $headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";


		// Remplacement de certains caractères spéciaux dans le message
		$message = str_replace("&#039;","'",$message);
		$message = str_replace("&#8217;","'",$message);
		$message = str_replace("&quot;",'"',$message);
		$message = str_replace('<br>',"\n",$message);
		$message = str_replace('<br />',"\n",$message);
		$message = str_replace("&lt;","<",$message);
		$message = str_replace("&gt;",">",$message);
		$message = str_replace("&amp;","&",$message);

		// Remplacement de certains caractères spéciaux dans l'objet
		$objet = str_replace("&#039;","'",$objet);
		$objet = str_replace("&#8217;","'",$objet);
		$objet = str_replace("&quot;",'"',$objet);
		$objet = str_replace('<br>','',$objet);
		$objet = str_replace('<br />','',$objet);
		$objet = str_replace("&lt;","<",$objet);
		$objet = str_replace("&gt;",">",$objet);
		$objet = str_replace("&amp;","&",$objet);

		// Envoi du mail
		if (mail($destinataire, '[Site] '.$objetHeader.' '.$objet, $message, $headers))
		{
            // Envoie d'un mail à contact si ce n'est pas déjà fait
            if($destinataire != 'contact@undessens.fr')
                mail('contact@undessens.fr', '[Site] '.$objetHeader.' '.$objet, $message, $headers);
            
            // Envoi du mail à l'envoyeur
            if ($copie == 'oui')
            {
                mail($email, '[Un des Sens] '.$objet, $message, 'From: <'.$destinataire.'>');
            }
            echo '<p>'.$message_envoye.'</p>'."<br>";
		}
		else
		{
			echo '<p>'.$message_non_envoye.'</p>'."<br>";
		};
	}
	else
	{
		// une des 3 variables (ou plus) est vide ...
		echo '<p>'.$message_formulaire_invalide.' <a href="./index.html#contact">Retour au formulaire</a></p>'."\n";
	};
}; // fin du if (!isset($_POST['envoi']))
?>
