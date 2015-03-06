<?php 
/********************************************/
/* Générateur de badge souvenir             */
/* Version 1.0                              */
/* Author : JeT41                           */
/* https://github.com/jet41/badge_souvenir  */
/********************************************/

$fichier_conf = 'config.php';

function affiche_header($title) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>
<body>
<h1>Installation du badge souvenir</h1>
<?php	
}

function affiche_footer() {
?>
</body>
</html>
<?php
}

// Vérification du lancement de PHP
if (false) {
	affiche_header('Erreur PHP')
?>
	<h2>Oups, quelque chose ne va pas.</h2>
	<h3>Erreur : PHP n'est pas actif.</h3>
	<p>Cette installation recquiert l'utilisation de PHP. Votre serveur ne supporte pas PHP, ou PHP n'est pas lancé.</p>
<?php
	affiche_footer();
	die();
}

$step = isset( $_POST['step'] ) ? (int) $_POST['step'] : 0;

switch ($step) {
	case 0:
		define('LF',"\n");
		define('STOP',"\n	</fieldset>\n</form>\n</body>\n</html>");
		// Step initial : vérification de la présence d'un fichier de configuration
		affiche_header('Installation automatisée : pré-requis');
		$php = phpversion();
//		$config_exists = (file_exists($fichier_conf) AND filesize($fichier_conf) > 0) ? 1 : 0;
?>
<h2>Contrôle des pré-requis</h2>
<form action="install.php" method="post">
	<fieldset>
<?php
	if ($php<'4.4') print ('		<p class="alert">PHP activé, version '.$php.'. Cette installation n\'a été testée que sur php>4.4.</p>'.LF);
	else print('		<p class="ok">PHP activé, version '.$php.'. Cette installation a été testée sur php 4.4.3 et 5.4.14</p>'.LF);

	// Test de l'existence du fichier de configuration
	if (file_exists($fichier_conf) AND filesize($fichier_conf) > 0) {
		print ('		<p class="alert">Il existe déjà un fichier de configuration "'.$fichier_conf.'". Si vous continuez, il sera supprimé.</p>'.LF);
	} else {
		print ('		<p class="ok">Il n\'existe pas de fichier de configuration antérieur.</p>'.LF);
	}
	// Test de l'écriture d'un fichier
	$testfic = time().".jet41";
	if (!$fp = @fopen($testfic, "w")) die ('		<p class="nok">Le test d\'écriture a échoué : Impossible de créer un fichier dans le répertoire courant.</p>'.STOP);
	if (@fwrite($fp, "blablabla") === FALSE) die ('		<p class="nok">Le test d\'écriture a échoué : Impossible d\'écrire dans un fichier ouvert du répertoire courant.</p>'.STOP);
	if (!@fclose($fp)) die ('		<p class="nok">Le test d\'écriture a échoué : Impossible de fermer un fichier ouvert.</p>'.STOP);
	if (!@unlink($testfic)) die ('		<p class="nok">Le test d\'écriture a échoué : Impossible de supprimer un fichier existant ['.$testfic.']'.STOP);
?>
		<p class="ok">Le test d'écriture s'est bien passé.</p>
	</fieldset>
	<fieldset>
		<p><input type="hidden" name="step" value="1" /></p>
		<p><input id="submit" type="submit" name="submit" value="Continuer" /></p>
	</fieldset>
</form>
<?php
		affiche_footer();
		break;	// Fin du step initial (vérification des pré-requis)

	case 1:
		// Step 1 : Affichage du formulaire
		affiche_header('Installation automatisée : 1ère étape')
?>	

<h2>Etape 1 - Paramétrage</h2>
<form action="install.php" method="post">
<fieldset>
	<legend>Informations générales</legend>
	<p>
		<input type="hidden" name="step" value="2" />
		<label>Titre de la page</label><input type="text" name="titre_page" placeholder="Génération du badge souvenir" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Utilisé dans la balise &lt;title&gt;.</span><br/>
		<label>Adresse du favicon</label><input type="text" name="adresse_favicon" placeholder="http://chemin_absolu/favicon.png" pattern="[^\x22]*\.(png|gif|ico)$" title="Adresse d'une image png, gif ou ico" /><span class="info_form">Adresse du favicon (extension .png, .gif ou .ico), laisser vide si non défini.</span><br/>
		<label>Titre du formulaire</label><input type="text" name="h1" placeholder="Titre personalisé"  pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Utilisé dans la balise &lt;h1&gt; de la réponse au formulaire (précédé par "Générateur de badge - ").</span><br/>
		<label>Message standard</label><input type="text" name="message_std" placeholder="J'ai découvert blablabla !" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Message affiché au survol du badge. Pour un message personnalisé, renseigner également la zone ci-dessous.</span><br/>
		<label>Message "pseudo"</label><input type="text" name="message_pseudo" placeholder="PSEUDO a découvert blablabla !" pattern="[^\x22]*(PSEUDO)[^\x22]*" title="Merci de ne pas utiliser de guillemets et d'intégrer le mot PSEUDO. Sinon, laisser cette zone vide." /><span class="info_form">Utiliser obligatoirement la chaine PSEUDO dans le texte à l'endroit où le pseudo (si renseigné) devra être affiché.</span><br/>
		<label>Fichier badge</label><input type="text" name="badge_file" placeholder="http://.../.../badge.png" pattern="^https?://[^\x22]+\.(png|gif|jpg)$" title="Adresse d'une image png, gif ou jpg, obligatoirement absolue." /><span class="info_form">Adresse absolue de l'image du badge (extension .png, .gif ou .jpg).</span><br/>
		<label>Texte alternatif</label><input type="text" name="badge_alt" placeholder="Badge pour blablabla" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Texte alternatif pour l'image du badge.</span><br/>
		<label>Lien optionnel</label><input type="text" name="badge_lien" placeholder="http://coord.info/GC" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets"/><span class="info_form">Lien au clic sur le badge.</span><br/>
	</p>
</fieldset>
<fieldset>
	<legend>Sauvegarde des visites en base de données (optionnel)</legend>
	<p>
		<input type="hidden" name="sauvegarde_bdd" value="0" /> 
		<label>Sauvegarde BDD</label><input type="checkbox" name="sauvegarde_bdd" maxlength="40" /><span class="info_form">Si la case est cochée, remplir les champs ci-dessous.</span><br/>
		<label>BdD Host</label><input type="text" name="bdd_host" maxlength="40" placeholder="localhost" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Adresse de la base de données, par exemple sql.free.fr pour les pages perso Free</span><br/>
		<label>BdD Username</label><input type="text" name="bdd_username" maxlength="40" placeholder="root" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Nom d'utilisateur de la base de données</span><br/>
		<label>BdD Password</label><input type="text" name="bdd_password" maxlength="40" placeholder="" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Mot de passe associé</span><br/>
		<label>BdD Name</label><input type="text" name="bdd_name" maxlength="40" placeholder="bdd_name" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Nom de la base de données</span><br/>
		<label>BdD Table</label><input type="text" name="bdd_table" maxlength="40" placeholder="visites_badge" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Nom de la table</span><br/>
	</p>
</fieldset>
<fieldset>
	<legend>Sauvegarde des visites dans un fichier (optionnel)</legend>
	<p>
		<input type="hidden" name="sauvegarde_fichier" value="0" /> 
		<label>Sauvegarde en fichier</label><input type="checkbox" name="sauvegarde_fichier" maxlength="40" /><span class="info_form">Si la case est cochée, remplir le champ suivant</span><br/>
		<label>Nom du fichier</label><input type="text" name="fic_name" maxlength="40" placeholder="visites_badge.txt" pattern="[^\x22]+" title="Merci de ne pas utiliser de guillemets" /><span class="info_form">Nom du fichier texte pour la sauvegarde</span><br/>
	</p>
</fieldset>
<fieldset>
	<p>
		<input id="submit" type="submit" name="submit" value="Envoyer" />
	</p>
</fieldset>
</form>
<?php		
		affiche_footer();	
		break;	// Fin du case 1 (affichage du formulaire)
	
	case 2:
		affiche_header ('Installation automatisée : 2ème étape');

		// on crée des constantes dont on se servira plus tard
		define('LF',"\n");
		define('RETOUR', "\n	</fieldset>\n	<fieldset><p>".'<input id="retour" type="button" value="Retour" onclick="history.back()" />'."</p></fieldset>\n</form>\n</body>\n</html>");
		
		// Récupération des variables passées dans le formulaire
		foreach ($_POST as $key=>$val) {
			$$key = stripslashes(trim($val));
		}
?>
<h2>Etape 2 - Vérification des données entrées</h2>
<form action="install.php" method="post">
	<p><input type="hidden" name="step" value="3" /></p>
<?php
		// Vérification de la base de données
		if ($sauvegarde_bdd) {	// Vérification des paramètres de la base de données.
?>
	<fieldset>
		<legend>Base de données</legend>
<?php
			if ($bdd_host=="") die('		<p class="nok">Vous n\'avez pas indiqué de serveur de base de données.</p>'.RETOUR);
			if (!@mysql_connect($bdd_host, $bdd_username, $bdd_password)) die('		<p class="nok">Impossible de se connecter au serveur de base de données ['.$bdd_host.'].</p>'.RETOUR);
?>
		<p class="ok">Connexion au serveur de base de données [<?php echo $bdd_host; ?>] réussie.</p>
		<p><input type="hidden" name="sauvegarde_bdd" value="1" /></p>
		<p><input type="hidden" name="bdd_host" value="<?php echo $bdd_host; ?>" /></p>
		<p><input type="hidden" name="bdd_username" value="<?php echo $bdd_username; ?>" /></p>
		<p><input type="hidden" name="bdd_password" value="<?php echo $bdd_password; ?>" /></p>
<?php
			if ($bdd_name=="") die('		<p class="nok">Vous n\'avez pas indiqué de nom de base de données.</p>'.RETOUR);
			if (!mysql_select_db($bdd_name)) die('		<p class="nok">Impossible de se connecter à la base ['.$bdd_name.']</p>'.RETOUR);
?>
		<p class="ok">La base [<?php echo $bdd_name; ?>] a bien été sélectionnée.</p>
		<p><input type="hidden" name="bdd_name" value="<?php echo $bdd_name; ?>" /></p>
		<p><input type="hidden" name="bdd_table" value="<?php echo $bdd_table; ?>" /></p>
<?php
			if ($bdd_table=="") die('		<p class="nok">Vous n\'avez pas indiqué de nom de table pour l\'enregistrement des données.</p>'.RETOUR);
			if (mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$bdd_table."'"))==1) {
?>
		<p class="alert">La table [<?php echo $bdd_table; ?>] existe. Elle sera supprimée si vous continuez !</p>
		<p><input type="hidden" name="suppr_table" value="1" /></p>
<?php		
			} else print ('		<p class="ok">La table ['.$bdd_table.'] n\'existe pas et sera créée.</p>'."\n") ;
			print ("	</fieldset>\n");
		}		

		// Vérification du fichier de sauvegarde
		if ($sauvegarde_fichier) {	// Vérification du fichier (sachant qu'il est déjà possible d'écrire dans le répertoire via les pré-requis).
?>
	<fieldset>
		<legend>Fichier de sauvegarde</legend>
<?php
			if ($fic_name=="") die('		<p class="nok">Vous n\'avez pas indiqué de nom de fichier pour la sauvegarde.</p>'.RETOUR);
?>
		<p><input type="hidden" name="sauvegarde_fichier" value="1" /></p>
		<p><input type="hidden" name="fic_name" value="<?php echo $fic_name; ?>" /></p>
<?php		
			if (file_exists($fic_name)) {
?>
		<p class="alert">Le fichier [<?php echo $fic_name; ?>] existe. Il sera supprimé si vous continuez !</p>
		<p><input type="hidden" name="suppr_fichier" value="1" /></p>
<?php
			} else print ('		<p class="ok">Le fichier ['.$fic_name.'] sera créé.</p>'."\n");
			print ("	</fieldset>\n");
		}
?>
	<fieldset>
		<legend>Formulaire de badge</legend>
<?php

		// Vérification du titre de la page
		if ($titre_page != "") {
			print('		<p class="ok">Le titre de la page (dans la balise &lt;title&gt;) sera : '.$titre_page.'</p>'."\n");
			print('		<p><input type="hidden" name="titre_page" value="'.$titre_page.'" /></p>'."\n");
		} else { 
			print ('		<p class="info">Vous n\'avez pas défini de titre de page (dans la balise &lt;title&gt;). Le titre par défaut sera : Générateur de badge.</p>'."\n");
			print('		<p><input type="hidden" name="titre_page" value="Générateur de badge" /></p>'."\n");
		}


		// Vérification du favicon
		if ($adresse_favicon != "") {
			if (file_exists($adresse_favicon)) {
?>
		<p class="ok">Le favicon du site a été trouvé : <img src="<?php echo $adresse_favicon; ?>" alt="favicon" width="16" height="16" /></p>
		<p><input type="hidden" name="adresse_favicon" value="<?php echo $adresse_favicon; ?>" /></p>
<?php
			} else print ('		<p class="alert">Le favicon ['.$adresse_favicon.'] n\'a pas été trouvé. Il ne sera pas utilisé.</p>'."\n");
		}

		// Vérification du titre du formulaire
		if ($h1 != "") {
			print('		<p class="ok">Le titre du formulaire (dans la balise &lt;h1&gt;) sera : Générateur de badge - '.$h1.'</p>'."\n");
			print('		<p><input type="hidden" name="h1" value="Générateur de badge - '.$h1.'" /></p>'."\n");
		} else { 
			print ('		<p class="info">Vous n\'avez pas défini de titre de formulaire (dans la balise &lt;h1&gt;). Le titre par défaut sera : Générateur de badge.</p>'."\n");
			print('		<p><input type="hidden" name="h1" value="Générateur de badge" /></p>'."\n");
		}

		// Vérification du message standard
		if ($message_std != "") {
			print('		<p class="ok">Le message standard au survol de l\'image sera : '.$message_std.'</p>'."\n");
			print('		<p><input type="hidden" name="message_std" value="'.htmlentities($message_std,ENT_QUOTES,"UTF-8").'" /></p>'."\n");
		} else { 
			$message_std = "Réalisé avec le générateur de badge - JeT41";
			print('		<p class="info">Vous n\'avez pas défini de message standard au survol de l\'image. Il sera par défaut : Réalisé avec le générateur de badge - JeT41.</p>'."\n");
			print('		<p><input type="hidden" name="message_std" value="Réalisé avec le générateur de badge - JeT41" /></p>'."\n");
		}

		// Vérification du message personnalisé
		if ($message_pseudo != "") {
			print('		<p class="ok">Le message personnalisé au survol de l\'image sera : '.$message_pseudo.'</p>'."\n");
		} else { 
			print('		<p class="info">Vous n\'avez pas défini de message personnalisé au survol de l\'image. Il sera par défaut le même que le message standard.</p>'."\n");
			$message_pseudo = $message_std;
		}
		print('		<p><input type="hidden" name="message_pseudo" value="'.htmlentities($message_pseudo,ENT_QUOTES,"UTF-8").'" /></p>'."\n");
		$demo_perso = preg_replace('/PSEUDO/','JeT41',$message_pseudo);

		// Vérification de l'image du badge
		if ($badge_file != "") {
//			if (file_exists($badge_file)) {		// Ne fonctionne pas en lien absolu... Reste à trouver une solution.
//				print('		<p class="ok">L\'image de badge ['.$badge_file.'] a été trouvée : <img src="'.$badge_file.'" alt="Image du badge" width="16" height="16" /></p>'.LF);
				print('		<p><input type="hidden" name="badge_file" value="'.$badge_file.'" /></p>'.LF);
//			} 
//			else die ('		<p class="nok">L\'image de badge ['.$badge_file.'] n\'a pas été trouvée.</p>'. RETOUR);
		}	
		else die ('		<p class="nok">Vous n\'avez pas défini d\'adresse pour l\'image du badge.</p>'. RETOUR);

		// Vérification du texte alternatif
		if ($badge_alt != "") {
			print('		<p class="ok">Le texte alternatif de l\'image de badge sera : '.$badge_alt.'</p>'."\n");
			print('		<p><input type="hidden" name="badge_alt" value="'.htmlentities($badge_alt,ENT_QUOTES,"UTF-8").'" /></p>'."\n");
		} else { 
			print('		<p class="info">Vous n\'avez pas défini de texte alternatif pour l\'image. Il sera par défaut : Réalisé avec le générateur de badge - JeT41.</p>'."\n");
			print('		<p><input type="hidden" name="badge_alt" value="Réalisé avec le générateur de badge - JeT41" /></p>'."\n");
		}

		// Vérification du lien du badge
		if ($badge_lien != "") {
			print('		<p class="ok">Le lien du badge pointera sur  : <a href="'.$badge_lien.'">'.$badge_lien.'</a></p>'."\n");
			print('		<p><input type="hidden" name="badge_lien" value="'.$badge_lien.'" /></p>'."\n");
		} else print('		<p class="alert">Vous n\'avez pas défini de lien au clic sur le badge. Il n\'y en aura pas.</p>'."\n");
		
		// Génération des aperçus
		$ap_std = '<img src="'.$badge_file.'" alt="/!\\ Il y a un problème avec l\'adresse de l\'image /!\\ " title="'.htmlentities($message_std,ENT_QUOTES,"UTF-8").'" />';
		$ap_pseudo = '<img src="'.$badge_file.'" alt="/!\\ Il y a un problème avec l\'adresse de l\'image /!\\ " title="'.htmlentities($demo_perso,ENT_QUOTES,"UTF-8").'" />';
		if ($badge_lien!="") { $ap_std = '<a href="'.$badge_lien.'">'.$ap_std.'</a>'; $ap_pseudo =  '<a href="'.$badge_lien.'">'.$ap_pseudo.'</a>'; }
?>
	</fieldset>
	<fieldset>
		<legend>Aperçu (standard et avec le pseudo JeT41)</legend>
		<p class="centre">
			<?php echo $ap_std.$ap_pseudo; ?><br/>
		</p>
	</fieldset>
	<p><br /><br /><input id="retour" type="button" value="Retour" onclick="history.back()" /><input id="submit" type="submit" value="Valider" /></p>
</form>
<?php
// Faire une variable tableau pour les warnings, cela déterminera s'il faut un bouton précédent ou pas.
// Faire une variable tableau pour les nok, cela déterminera s'il faut désactiver le bouton continuer ou pas.  => pas besoin, vu qu'avec NOK on retourne à la page précédente.

		affiche_footer();
		break;	// Fin du case 2 (traitement du formulaire)
	
	case 3:
		affiche_header ('Installation automatisée : 3ème étape');
		// on crée des constantes dont on se servira plus tard
		define('RETOUR', "\n	</fieldset>\n".'	<fieldset><p><input id="retour" type="button" value="Retour" onclick="history.back()" /></p></fieldset>'."\n	</form>\n</body>\n</html>");
		define ('LF',"\n");
?>
<h2>Etape 3 - Récapitulatif</h2>
	<form action="install.php" method="post">
	<fieldset>	
		<legend>Actions réalisées</legend>
<?php
		// Récupération des variables passées dans le formulaire
		foreach ($_POST as $key=>$val) {
			$$key = stripslashes(trim($val));
		}
		$config='<?php'.LF;
		$config.='	// Titre utilisé dans la balise <title>'.LF;
		$config.='	$titre_page = "'.$titre_page.'";'.LF;
		$config.='	// Titre du formulaire, utilisé dans la balise <h1>'.LF;
		$config.='	$h1 = "'.$h1.'";'.LF;
		if (isset($adresse_favicon)) {
			$config.='	// Adresse du favicon'.LF;
			$config.='	$adresse_favicon = "'.$adresse_favicon.'";'.LF;
		}
		$config.='	// Message affiché lorsque l\'utilisateur a renseigné son pseudo (utiliser la variable $pseudo pour y faire référence)'.LF;
		$config.='	$message_pseudo = "'.$message_pseudo.'";'.LF;
		$config.='	// Message générique'.LF;
		$config.='	$message_std = "'.$message_std.'";'.LF;
		$config.=LF;
		$config.='	// Adresse de l\'image du badge'.LF;
		$config.='	$badge_file = "'.$badge_file.'";'.LF;
		$config.='	// Texte alternatif'.LF;
		$config.='	$badge_alt = "'.$badge_alt.'";'.LF;
		if (isset($badge_lien)) {
			$config.='	// Lien au clic sur le badge'.LF;
			$config.='	$badge_lien = "'.$badge_lien.'";'.LF;
		}
		if (isset($sauvegarde_bdd)) {
			$config.=LF;
			$config.='	/****** CONFIGURATION BDD *************/'.LF;
			$config.='	$sauvegarde_bdd = '.intval($sauvegarde_bdd).';'.LF;
			$config.='	// Adresse de la base de données, par exemple sql.free.fr pour les pages perso Free'.LF;
			$config.='	$bdd_host = "'.$bdd_host.'";'.LF;
			$config.='	// Nom d\'utilisateur de la base de données'.LF;
			$config.='	$bdd_username = "'.$bdd_username.'";'.LF;
			$config.='	// Mot de passe associé'.LF;
			$config.='	$bdd_password = "'.$bdd_password.'";'.LF;
			$config.='	// Nom de la base de données'.LF;
			$config.='	$bdd_name = "'.$bdd_name.'";'.LF;
			$config.='	// Nom de la table'.LF;
			$config.='	$bdd_table = "'.$bdd_table.'";'.LF;
		}
		if (isset($sauvegarde_fichier)) {
			$config.=LF;
			$config.='	/****** CONFIGURATION FICHIER *********/'.LF;
			$config.='	$sauvegarde_fichier = 1;'.LF;	
			$config.='	// Nom du fichier de sauvegarde'.LF;
			$config.='	$fic_name = "'.$fic_name.'";'.LF;
		}
		$config.='?>';
		
		
		// Création du fichier de configuration, message d'erreur en cas de soucis.
		if(!$fichier = fopen($fichier_conf, 'w')) die('		<p class="nok">Impossible de créer le fichier ['.$fichier_conf.'].</p>'. RETOUR);
		// Ecriture de la configuration dans le fichier, message d'erreur en cas de soucis
		if(fwrite($fichier, $config) == FALSE) die('		<p class="nok">Impossible d\'écrire dans le fichier ['. $fichier_conf.'].</p>'. RETOUR);
		if(fclose($fichier) == FALSE) die('		<p class="nok">Une erreur s\'est produite lors de la fermeture du fichier ['.$fichier_conf.'].</p>'. RETOUR);
		print ('		<p class="ok">Le fichier de configuration ['.$fichier_conf.'] a bien été écrit.</p>'.LF);

		if (isset($sauvegarde_bdd) && ($sauvegarde_bdd==1)) {
			if (!@mysql_connect($bdd_host, $bdd_username, $bdd_password)) die('		<p class="nok">Impossible de se connecter au serveur de base de données ['.$bdd_host.'].</p>'. RETOUR);
			if (!mysql_select_db($bdd_name)) die('		<p class="nok">Impossible de sélectionner la base ['.$bdd_name.'].</p>'.RETOUR);
			if (isset($suppr_table)) {
				mysql_query('DROP TABLE '.$bdd_table) or die ('		<p class="nok">Impossible de supprimer la table ['.$bdd_table.'].</p>'.RETOUR);
				print ('		<p class="info">La table ['.$bdd_table.'] existait et a bien été supprimée.</p>'.LF);
			}
			$req='CREATE TABLE IF NOT EXISTS `'.$bdd_table.'` (`date` timestamp NOT NULL default CURRENT_TIMESTAMP,`pseudo` text NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			mysql_query($req) or die ('		<p class="nok">Impossible de supprimer la table ['.$bdd_table.'].</p>'.RETOUR);
			print ('		<p class="ok">La table ['.$bdd_table.'] a bien été créée.</p>'.LF);
		}
		
		if (isset($sauvegarde_fichier) && ($sauvegarde_fichier==1)) {
			if (isset($suppr_fichier) && $suppr_fichier ==1 ) {
				if (file_exists($fic_name)) unlink ($fic_name) or die ('		<p class="nok">Impossible de supprimer le fichier ['.$fic_name.'].</p>'.RETOUR);
				print ('		<p class="info">Le fichier ['.$fic_name.'] existait et a bien été supprimé.</p>'.LF);
			}
		}
?>
	</fieldset>
	<fieldset>
		<legend>Et maintenant...</legend>
		<p class="fini">
			L'installation est maintenant terminée. En cliquant sur le bouton ci-dessous, vous allez supprimer ce script d'installation et être redirigé vers le formulaire de badge.<br/>
			Si vous aimez ce code et qu'il vous a été utile, n'hésitez pas à me le dire par mail, facebook... ou lors d'un event, ça fait toujours plaisir :) <br/>
			La liste des contributeurs est visible sur la page du projet : <a href="https://github.com/jet41/badge_souvenir">https://github.com/jet41/badge_souvenir</a><br/>
			N'hésitez pas à venir contribuer aussi, pour améliorer ce script et le formulaire de badge.
		</p>
		<p class="fini">JeT41 (geocaching.com) - Jet Geocaching (facebook).</p>
		<p>
			<input type="hidden" name="step" value="4" />
			<input id="submit" type="submit" value="Terminer" />
		</p>
	</fieldset>
	</form>
<?php		
		affiche_footer();

		break;
	case 4:	// Suppression du fichier d'installation, redirection vers formulaire_badge.php
		unlink ('install.css');
		unlink ('install.png');
		unlink ('install.php');
		header('Location: index.php');
	
		break;
	default:
		# code...
		break;
}
die();


?>