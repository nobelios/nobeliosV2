<?php
// Nobelios V2.0
// Dernière modification le : 15/05/2009
// Blackout, toute copie pour usage non personnelle interdite

// Appel des pages externes './../' permet de sortir du dossier de l'editech
include_once('./../php/securit.php');
include_once('./../php/properties.php');
include_once('./../web/' .$language. '/language.php');
include_once('./../php/language/french.php');
include_once('./../php/functions.php');
include_once('./../geshi/geshi.php');
include_once('./../php/session.php');
include_once('./../php/functions/zip.lib.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Nobelios</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		
		<?php
		
		echo '<div id="headMini">
			<div class="headerMiniBackground">
				<div class="headerMiniLogo"></div>
			</div>
		</div>';
		
		
		
		
		if (ereg("MSIE", $_SERVER["HTTP_USER_AGENT"])) {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="ie" href="./../design/' . $userDesign . '/ie.css" />';
		} else if (ereg("^Mozilla/", $_SERVER["HTTP_USER_AGENT"])) {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="firefox" href="./../design/' . $userDesign . '/firefox.css" />';
		} else if (ereg("^Opera/", $_SERVER["HTTP_USER_AGENT"])) {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="firefox" href="./../design/' . $userDesign . '/firefox.css" />';
		} else {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="firefox" href="./../design/' . $userDesign . '/firefox.css" />';
		}
		?> 
		
	</head>
	<body>
		<div class="editechBackground">
			<script type="text/javascript">
				// id = id du bloc (<div>, <p> ...) contenant les checkbox
				// action = '0' pour tout décocher
				// action = '1' pour tout cocher
				// action = '2' pour inverser la sélection	
				function actionCheckbox(id, nb_elements, action) {
					var blnEtat=null;
					for (i=0; i<nb_elements ; i++) {
						var Chckbox = document.getElementById(id+i).firstChild;
						while (Chckbox!=null) {
							if (Chckbox.nodeName=="INPUT") {
								if (Chckbox.getAttribute("type")=="checkbox") {
									blnEtat = (action=='0') ? false : (action=='1') ? true : (document.getElementById(Chckbox.getAttribute("id")).checked) ? false : true;
									document.getElementById(Chckbox.getAttribute("id")).checked=blnEtat;
								}
							}
							Chckbox = Chckbox.nextSibling;
						}
					}
				}
				function checkTheBox(id, i) {
					var blnEtat=null;
					var Chckbox = document.getElementById(id+i).firstChild;
					while (Chckbox!=null) {
						if (Chckbox.nodeName=="INPUT") {
							if (Chckbox.getAttribute("type")=="checkbox") {
								blnEtat = (document.getElementById(Chckbox.getAttribute("id")).checked) ? false : true;
								document.getElementById(Chckbox.getAttribute("id")).checked=blnEtat;
							}
						}
						Chckbox = Chckbox.nextSibling;
					}
				}
			</script>
			
			<?php
				if (isset($_GET['images'])) {
					include_once('./images.php');
				} elseif (isset($_GET['files'])) {
					include('./files.php');
				} elseif (isset($_GET['videos'])) {
					include('./videos.php');
				} elseif (isset($_GET['msds'])) {
					include('./msds.php');
				} else {
					echo 'erreur';
				}
			?>
		</div>
	<body>
</html>