<?php

$userIp = $_SERVER['REMOTE_ADDR']; // Adresse IP du visiteur

// Démarrage de la session
session_start();

// Lecture de l'adresse ip du visiteur
$serverName = $_SERVER['SERVER_NAME']; // Nom du serveur

// Connexion à la base de données
connectDb();

$pathAddr = $_SERVER['REQUEST_URI'];

//-----------------------------------------------------------------
// Déconnexion
//-----------------------------------------------------------------
// Demande de déconnexion
if (isset($_GET['kill_session']))
{
	session_destroy(); // Destruction de la session
	setcookie('session', ''); // Destruction du cookie
}

//-----------------------------------------------------------------
// Connexion
//-----------------------------------------------------------------
// Par COOKIE
if (!isset($_SESSION['login']) && !isset($_SESSION['password']) && !empty($_COOKIE['session']))
{
	$cookieSecured = htmlentities($_COOKIE['session']);
	$mysqlQueryUsers = mysql_query("SELECT login, password, user_ip, level FROM users");
	while ($mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers))
	{
		$cookieSession = md5(md5($mysqlDataUsers['login'].$mysqlDataUsers['password'].$mysqlDataUsers['user_ip'].$serverName));
		if ($cookieSession == $cookieSecured)
		{
			$_SESSION['login'] = $mysqlDataUsers['login'];
			$_SESSION['password'] = $mysqlDataUsers['password'];
			$_SESSION['level'] = $mysqlDataUsers['level'];
			break;
		}
	}
}
// Demande de connexion
else
{
	if (isset($_POST['session_login']) && isset($_POST['session_password']))
	{
		$userLoginSecured = mysql_real_escape_string($_POST['session_login']);
		$mysqlPasswordSecured = md5(md5($_POST['session_login'].$_POST['session_password']));
		$mysqlQueryUsers = mysql_query("SELECT login, password, user_ip, level FROM users WHERE login='$userLoginSecured'");
		$mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers);
		
		// Test si le login et le mot de passe sont valides
		if ($mysqlPasswordSecured == $mysqlDataUsers['password'])
		{
			// L'utilisateur veut rester connecté (création d'un cookie)
			if (isset($_POST['session_cookie']))
			{
				$cookieSession = md5(md5($userLoginSecured.$mysqlPasswordSecured.$userIp.$serverName)); // Génération du code cookie
				$cookieExpire = time() + 24*3600; // Le cookie expirera dans 24 heures
				setcookie('session', $cookieSession, $cookieExpire);
			}
			
			// Ecriture de l'ip de l'utilisateur dans la base de données
			mysql_query("UPDATE users SET user_ip='$userIp' WHERE login='$userLoginSecured'");
			
			// Définition de la session
			$_SESSION['login'] = $mysqlDataUsers['login'];
			$_SESSION['password'] = $mysqlDataUsers['password'];
			$_SESSION['level'] = $mysqlDataUsers['level'];
		}
		// Login ou mot de passe invalide
		else
		{
			echo "<b style=\"color: white\">login ou mot de passe invalide</b>";
		}
	}
}

//-----------------------------------------------------------------
// Définition des variables
//-----------------------------------------------------------------
// Si une session existe
if (isset($_SESSION['login']) && isset($_SESSION['password']) && !isset($_GET['kill_session']))
{
	// Connection à la table users
	$userLoginSecured = mysql_real_escape_string($_SESSION['login']);
	$userPasswordSecured = mysql_real_escape_string($_SESSION['password']);
	$mysqlQueryUsers = mysql_query("SELECT * FROM users WHERE login='$userLoginSecured' && password='$userPasswordSecured'");
	$mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers);
	// Définition des variables utilisateur
	$userLogin 		= $mysqlDataUsers['login'];
	$userPassword 	= $mysqlDataUsers['password'];
	$userIp			= $_SERVER['REMOTE_ADDR'];
	$userId 		= $mysqlDataUsers['user_id'];
	$userLevel		= $mysqlDataUsers['level'];
	$userDesign		= $mysqlDataUsers['design'];
}
// Valeurs par défaut
else
{
	$userLogin		= 'visiteur';
	$userPassword	= ''; 
	$userIp			= $_SERVER['REMOTE_ADDR']; // Adresse IP du visiteur
	$userId			= '';
	$userLevel		= 0;
	$userDesign		= 'normal';
}

// Déconnexion de la base de données
mysql_close();

?>