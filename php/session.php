<?php

$userIp = $_SERVER['REMOTE_ADDR']; // Adresse IP du visiteur

// D�marrage de la session
session_start();

// Lecture de l'adresse ip du visiteur
$serverName = $_SERVER['SERVER_NAME']; // Nom du serveur

// Connexion � la base de donn�es
connectDb();

$pathAddr = $_SERVER['REQUEST_URI'];

//-----------------------------------------------------------------
// D�connexion
//-----------------------------------------------------------------
// Demande de d�connexion
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
			// L'utilisateur veut rester connect� (cr�ation d'un cookie)
			if (isset($_POST['session_cookie']))
			{
				$cookieSession = md5(md5($userLoginSecured.$mysqlPasswordSecured.$userIp.$serverName)); // G�n�ration du code cookie
				$cookieExpire = time() + 24*3600; // Le cookie expirera dans 24 heures
				setcookie('session', $cookieSession, $cookieExpire);
			}
			
			// Ecriture de l'ip de l'utilisateur dans la base de donn�es
			mysql_query("UPDATE users SET user_ip='$userIp' WHERE login='$userLoginSecured'");
			
			// D�finition de la session
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
// D�finition des variables
//-----------------------------------------------------------------
// Si une session existe
if (isset($_SESSION['login']) && isset($_SESSION['password']) && !isset($_GET['kill_session']))
{
	// Connection � la table users
	$userLoginSecured = mysql_real_escape_string($_SESSION['login']);
	$userPasswordSecured = mysql_real_escape_string($_SESSION['password']);
	$mysqlQueryUsers = mysql_query("SELECT * FROM users WHERE login='$userLoginSecured' && password='$userPasswordSecured'");
	$mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers);
	// D�finition des variables utilisateur
	$userLogin 		= $mysqlDataUsers['login'];
	$userPassword 	= $mysqlDataUsers['password'];
	$userIp			= $_SERVER['REMOTE_ADDR'];
	$userId 		= $mysqlDataUsers['user_id'];
	$userLevel		= $mysqlDataUsers['level'];
	$userDesign		= $mysqlDataUsers['design'];
}
// Valeurs par d�faut
else
{
	$userLogin		= 'visiteur';
	$userPassword	= ''; 
	$userIp			= $_SERVER['REMOTE_ADDR']; // Adresse IP du visiteur
	$userId			= '';
	$userLevel		= 0;
	$userDesign		= 'normal';
}

// D�connexion de la base de donn�es
mysql_close();

?>