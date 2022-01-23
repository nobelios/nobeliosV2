<?php
// Nobelios V2.0
// Dernière modification le : 15/05/2009
// Blackout, toute copie pour usage non personnelle interdite

// Inclusion des paramètres
include_once('./php/securit.php');
include_once('./php/properties.php');
include_once('./web/' .$language. '/language.php');
include_once('./php/language/french.php');
include_once('./php/functions.php');
include_once('./geshi/geshi.php');
include_once('./php/session.php');
include_once('./php/functions/zip.lib.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Nobelios</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		
		<?php
		if (ereg("MSIE", $_SERVER["HTTP_USER_AGENT"])) {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="ie" href="./design/' . $userDesign . '/ie.css" />';
		} else if (ereg("^Mozilla/", $_SERVER["HTTP_USER_AGENT"])) {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="firefox" href="./design/' . $userDesign . '/firefox.css" />';
		} else if (ereg("^Opera/", $_SERVER["HTTP_USER_AGENT"])) {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="firefox" href="./design/' . $userDesign . '/firefox.css" />';
		} else {
			echo '<link rel="stylesheet" media="screen" type="text/css" title="firefox" href="./design/' . $userDesign . '/firefox.css" />';
		}
		?> 
			
	</head>
	<body>
		
		<script type="text/javascript" src="./php/functions/function.js"></script>
		
		<?php 
		if($full==false) echo '<div style="margin: 0px 100px;">';
		else echo'<div>';
		?>
			<div id="head">
				<?php
				
				echo '<div class="headerBackground">
					<div class="headerLogo">
						<div class="headerText">
							Bonjour '. $userLogin . '
						</div>
					</div>
				</div>';
				?>
			</div>
			
			<div id="menu">
				<?php include('./php/menu.php') ?>
			</div>
			
			<div id="body">
				<div id="body_spacer">
					<?php
						if (isset($_GET['page_viewer'])) {
							include('./php/page_viewer.php');
						} elseif (isset($_GET['user_profil'])) {
							include('./php/user/user_profil.php');
						} elseif (isset($_GET['login'])) {
							include('./php/login.php');
						} elseif (isset($_GET['user_page'])) {
							include('./php/user/user_page.php');
						} elseif (isset($_GET['user_avatars'])) {
							include('./php/user/user_avatars.php');
						} elseif (isset($_GET['user_pages_editor'])) {
							include('./php/user/user_pages_editor.php');
						} elseif (isset($_GET['user_images'])) {
							include('./php/user/user_images.php');
						} elseif (isset($_GET['user_files'])) {
							include('./php/user/user_files.php');
						} elseif (isset($_GET['user_movies'])) {
							include('./php/user/user_movies.php');
						} elseif (isset($_GET['user_space'])) {
							include('./php/user/user_space.php');
						} elseif (isset($_GET['user_messages'])) {
							include('./php/user/user_messages.php');
						} elseif (isset($_GET['page_list'])) {
							include('./php/page_list.php');
						} elseif (isset($_GET['user_msds_editor'])) {
							include('./php/user/user_msds_editor.php');
						} else {
							echo '<div><a href="index.php?page_list">Consulter une page</a></div>';
							echo '<div><a href="index.php?user_space">Mon espace</a></div>';
						}
					?>
				</div>
			</div>
		</div>
	</body>
</html>	