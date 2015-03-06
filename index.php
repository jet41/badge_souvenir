<?php 
/********************************************/
/* Générateur de badge souvenir             */
/* Version 1.0                              */
/* Author : JeT41                           */
/* https://github.com/jet41/badge_souvenir  */
/********************************************/

if (file_exists('install.php')) header('Location: install.php');

$pseudo = (isset($_POST["pseudo"])) ? $_POST["pseudo"] : "";
include "config.php";
define ('LF',"\n");

function affiche_header($title, $favicon) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<?php echo $favicon; ?>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php	
}

function affiche_footer() {
?>
	<div id="about">
		<p>Version originale : <a href="http://www.geocaching.com/profile/?u=jet41">JeT41</a>. <!--Contributeurs : personne pour l'instant -->Vous voulez l'utiliser sur votre site et/ou y contribuer ? Le projet est disponible sur GitHub : <img src="icon/github.png" alt="GitHub" width="16" height="16" /> <a href="https://github.com/jet41/badge_souvenir">https://github.com/jet41/badge_souvenir</a></p>
	</div>
</body>
</html>
<?php
}

$step = isset( $_POST['step'] ) ? (int) $_POST['step'] : 0;
if (isset($adresse_favicon)) {
	switch(substr($adresse_favicon,-3)) {
		case 'png' : $favicon = '<link rel="icon" type="image/png" href="'.$adresse_favicon.'" />'.LF; break;
		case 'gif': $favicon = '<link rel="icon" type="image/gif" href="'.$adresse_favicon.'" />'.LF; break;
		case 'ico': $favicon = '<link rel="icon" type="image/x-icon" href="'.$adresse_favicon.'" />'.LF; break;
		default: $favicon=''; break;
	}
}

switch ($step) {
	case 0:
		affiche_header($titre_page, $favicon);
?>
	<h1><?php echo $h1;?></h1>
	<h3>Version 1.0<!-- - JeT41--></h3>
	<form method="post" action="index.php">
		<p>Bravo, vous avez gagné un badge. Pour le découvrir et pouvoir l'ajouter sur votre page de profil, cliquez sur Envoyer.</p>
		<p>Si vous entrez votre pseudo sur le jeu, le badge sera personnalisé, et je saurai qui est passé par ici. C'est bien entendu facultatif, mais ça fait plaisir ;)</p>
		<fieldset>
			<p>
				<input type="text" name="pseudo" value="" placeholder="pseudo sur geocaching.com" />
				<input id="submit" type="submit" name="envoi" value="Envoyer" />
				<input type="hidden" name="step" value="1" />
			</p>
		</fieldset>
	</form>
<?php
		affiche_footer();
	break;	
	
	case 1:
		affiche_header($titre_page, $favicon);
?>
	<h1><?php echo $h1;?></h1>
	<h3>Version 1.0<!-- - JeT41--></h3>
<?php
	
	if (isset($sauvegarde_bdd) && ($sauvegarde_bdd)) {
		// Connexion à la base de données
		@mysql_connect($bdd_host,$bdd_username,$bdd_password); //or die ("Erreur de connexion au serveur de base de données.");
		@mysql_select_db($bdd_name);// or die ("Erreur d'ouverture de la base de données.");
	
		// Envoi des informations
		$req = "INSERT INTO `".$bdd_name."`.`".$bdd_table."` (`pseudo`) VALUES ('".$pseudo."');";
		@mysql_query($req);// or die ("Erreur d'ajout dans la base de données.");
		mysql_close ();
	}
	
	if (isset($sauvegarde_fichier) && ($sauvegarde_fichier)) {
		$file = @fopen($fic_name, 'a+');// or die ("Erreur à l'ouverture du fichier de sauvegarde.");
		@fputs ($file, $gccode.";".date("Y-m-d H:i:s").";".$pseudo."\n");// or die ("Erreur d'écriture dans le fichier de sauvegarde");
		@fclose($file);// or die ("Erreur à la fermeture du fichier de sauvagarde.");
	}

	
?>
	<p>Voici le code à recopier dans votre <a href="http://www.geocaching.com/account/settings/profile">page de profil</a>, dans la zone "Bio" : </p> 
<?php 
	$message = ($pseudo) ? preg_replace('/PSEUDO/',$pseudo,$message_pseudo) : $message_std; 
	$badge = '<img src="'.$badge_file.'" alt="'.$badge_alt.'" title="'.$message.'" />';
	if (isset($badge_lien)) $badge = '<a href="'.$badge_lien.'">'.$badge.'</a>';
	
?>
	<p class="code">&lt;p&gt;<?php echo htmlspecialchars($badge); ?>&lt;/p&gt;</p>
	<p>Le résultat sera le suivant : </p>
	<p class="centre"><?php echo $badge; ?></p>
<?php
	affiche_footer();

	break;
}