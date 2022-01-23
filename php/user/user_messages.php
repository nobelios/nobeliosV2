<?php

//-----------------------------------------------------------------
// Galerie de fichiers de l'utilisateur
//-----------------------------------------------------------------

// Connexion à la base de données
connectDb();

// Si le membre est bien loggé
if (isset($_SESSION['login']) && isset($_SESSION['password']))
{
	// Connexion à la table utilisateurs (users)
	$userLoginSecured = mysql_real_escape_string($_SESSION['login']);
	$userPasswordSecured = mysql_real_escape_string($_SESSION['password']);
	$mysqlQueryUsers = mysql_query("SELECT * FROM users WHERE login='$userLoginSecured' && password='$userPasswordSecured'");
	$mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers);
	$recipient_id = $mysqlDataUsers['user_id'];
	
	$mysqlQueryMessages = mysql_query("SELECT * FROM messages WHERE recipient_id='$recipient_id'");
	$mysqlDataMessages = mysql_fetch_array($mysqlQueryMessages);
	
	// Aucune alerte par défaut
	$alert = null;
	
	//-----------------------------------------------------------------
	// Partie Affichage
	//-----------------------------------------------------------------
	
	
	echo '<div class="linker">
		<a href="./index.php?home">Accueil</a> > 
		<a href="./index.php?user_space">Espace utilisateur</a> > 
		Mes images<br />
	</div>';
	
	echo '<div class="divInPage">
		<div class="divInPageImage">
			<div class="divInPageImageBox">
				<img src="http://localhost/Nobelios%20V2.0/web/users/D2309EFC5AB81674/avatars/Cat_Eyeds.47D6F122233C4F21.jpg" />
			</div>
		</div>
		<div class="divInPageText">
			informations
		</div>
	</div>';
	
	if ($alert != null) {
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
			'.$alert.'
		</div>';
	}
	
	echo '<div id="messages_box" style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
		<div style="border: 1px solid black; display: table-cell; width: 200px;">
			Nouveau message<br />
			Boite de réception<br />
			Messages suivis<br />
			Messages envoyés<br />
			Brouillons<br />
			Corbeille<br />
			Liste noire
		</div>
		<div style="border: 1px solid black; display: table-cell; width: 200px;">
			informations
		</div>
	</div>';
	
	
}

//-----------------------------------------------------------------
// Affichage de la page de login
//-----------------------------------------------------------------

else
{
	echo 'vous devez vous loggé en premier afin d\'accéder à cette section';
}

?>