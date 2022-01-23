<?php

echo '<div class="menuBloc">
	Navigation<br/>
	<a href="./index.php?home">Accueil</a><br />
	<a href="./index.php?page_list">Pages</a><br />
	</div>';

echo '<div class="menuBloc">
	Espace utilisateur<br/>';
	if (isset($_SESSION['login']) && isset($_SESSION['password']) && !isset($_GET['kill_session'])) {
		echo '<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
			<a href="http://localhost/Nobelios%20V2.0/index.php?user_space">Mon espace</a>';
	} else {
		echo '<a href="index.php?registration">Inscription</a><br />
			<a href="index.php?login">Connexion</a><br />';
	}
	echo '</div>';

echo '<div class="menuBloc">
	Espace utilisateur<br/>';
	if (isset($_SESSION['login']) && isset($_SESSION['password']) && !isset($_GET['kill_session'])) {
		echo '<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="./index.php?&amp;kill_session">Déconnexion</a><br />
		<a href="http://localhost/Nobelios%20V2.0/index.php?user_space">Mon espace</a>';
	} else {
		echo '<a href="index.php?registration">Inscription</a><br />
			<a href="index.php?login">Connexion</a><br />';
	}
	echo '</div>';
/*
// On liste les menus
foreach ($allowedSubject as $subject) {
	echo '<div class="menuBloc">';
		echo '<a href="./index.php?page_list&amp;subject='.$subject.'">'.$subject.'</a><br/>----------------<br/>';
		foreach ($allowedType as $type) {
		echo '<a href="./index.php?page_list&amp;subject='.$subject.'&amp;type='.$type.'">'.$type.'</a><br />';
	}
	echo '</div>';
}
*/

?>