<?php

// Si une session existe
if (isset($_SESSION['login']) && isset($_SESSION['password']) && !isset($_GET['kill_session']))
{
	echo '<a href="./index.php?&amp;kill_session">déconnexion</a>';
}
// Valeurs par défaut
else
{
	echo "vous devez vous loggé afin de pouvoir profiter de toutes les options de nobelios !";
	// Formulaire de connexion
	echo '<form method="POST" action="index.php">
		<input type="text" name="session_login" value="login" />
		<input type="password" name="session_password" />
		<input type="checkbox" name="session_cookie" value="cookie" /> mémoire
		<input type="submit" value="Se connecter" />
	</form>';
}

?>