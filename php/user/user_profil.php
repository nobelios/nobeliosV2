<?php

//-----------------------------------------------------------------
// Profil de l'utilisateur
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

	// Variables sans vérification + sécurisation
	$userName			= isset($_POST['name']) 		? htmlspecialchars(mysql_real_escape_string($_POST['name']))		: $mysqlDataUsers['name'];
	$userForname		= isset($_POST['forname']) 		? htmlspecialchars(mysql_real_escape_string($_POST['forname']))		: $mysqlDataUsers['forname'];
	$userLocation		= isset($_POST['location']) 	? htmlspecialchars(mysql_real_escape_string($_POST['location']))	: $mysqlDataUsers['location'];
	$userTag			= isset($_POST['tag']) 			? htmlspecialchars(mysql_real_escape_string($_POST['tag']))			: $mysqlDataUsers['tag'];
	$userStudies		= isset($_POST['studies']) 		? htmlspecialchars(mysql_real_escape_string($_POST['studies']))		: $mysqlDataUsers['studies'];
	$userHobbies		= isset($_POST['hobbies']) 		? htmlspecialchars(mysql_real_escape_string($_POST['hobbies']))		: $mysqlDataUsers['hobbies'];
	$userWebsite		= isset($_POST['website']) 		? htmlspecialchars(mysql_real_escape_string($_POST['website']))		: $mysqlDataUsers['website'];
	$userSignature		= isset($_POST['signature'])	? htmlspecialchars(mysql_real_escape_string($_POST['signature']))	: $mysqlDataUsers['signature'];
	$userMsn			= isset($_POST['msn']) 			? htmlspecialchars(mysql_real_escape_string($_POST['msn']))			: $mysqlDataUsers['msn'];
	$userIam			= isset($_POST['iam']) 			? htmlspecialchars(mysql_real_escape_string($_POST['iam']))			: $mysqlDataUsers['iam'];
	$userIcq			= isset($_POST['icq']) 			? htmlspecialchars(mysql_real_escape_string($_POST['icq']))			: $mysqlDataUsers['icq'];
	$userYahoo			= isset($_POST['yahoo']) 		? htmlspecialchars(mysql_real_escape_string($_POST['yahoo']))		: $mysqlDataUsers['yahoo'];
	$userSkype			= isset($_POST['skype']) 		? htmlspecialchars(mysql_real_escape_string($_POST['skype']))		: $mysqlDataUsers['skype'];
	
	// Test double sur les variables
	$userSex			= isset($_POST['sex']) 			? (($_POST['sex'] == 1) ? 1: 0)									: $mysqlDataUsers['sex'];
	$userPublicEmail	= isset($_POST['name'])			? (isset($_POST['public_email']) == 1 ? 1: '')					: $mysqlDataUsers['public_email'];
	
	// On doit encore faire d'autres test sur la date de naissance
	// Affichage de la date de naissance
	$userBirthday = $mysqlDataUsers['birthday'];
	if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year']))
	{
		$d = $_POST['day'];
		$m = $_POST['month'];
		$Y = $_POST['year'];
		$userBirthday = mktime(0, 0, 0, $m, $d, $Y);
	}

	if (isset($_POST['name']))
	{
		// Mise à jour de la base de données
		mysql_query("UPDATE users SET 
					name			= '$userName',
					forname			= '$userForname',
					location		= '$userLocation',
					tag				= '$userTag',
					studies			= '$userStudies',
					hobbies			= '$userHobbies',
					website			= '$userWebsite',
					signature		= '$userSignature',
					msn				= '$userMsn',
					iam				= '$userIam',
					icq				= '$userIcq',
					yahoo			= '$userYahoo',	
					skype			= '$userSkype',
					sex				= '$userSex',
					public_email	= '$userPublicEmail',
					birthday		= '$userBirthday'");
	}
	
	echo '<h2>Modification du profil utilisateur</h2>';
	
	// Affichage du Linker
	echo '<div class="linker">
		<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a> > 
		<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a> > 
		Profil
	</div>';
	
	echo '<form method="post" action="./index.php?user_profil">
		<div class="pageList">
			<table class="userForm">
			<tr><td colspan="2">Information personnelles</td></tr>
			<tr class="userForm">
				<td class="userFormLegende">Name</td>
				<td class="userFormField"><input type="text" maxlength="30" size="30" name="name" value="' . $userName . '" /></td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Forname</td>
				<td class="userFormField"><input type="text" maxlength="30" size="30" name="forname" value="' . $userForname . '" /></td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Sex</td>
				<td class="userFormField">';
					// Définition du sexe
					if ($userSex == 1) echo '<input checked type="radio" name="sex" value="1" />homme <input type="radio" name="sex" value="0" />femme';
					else echo '<input type="radio" name="sex" value="1" />homme <input checked type="radio" name="sex" value="0" />femme';
				echo '</td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Date de naissance</td>
				<td class="userFormField">
					<select name="day">';
					// Définition de la date de naissance
					for ($i=1; $i<=31; $i++)
					{
						if ($i<10) $i = '0' . $i; // Ajout du zéro devant les nombres en dessous de 10
						if ($i == date('d', $userBirthday))	echo '<option selected value="' . $i . '">' . $i . '</option>';
						else echo '<option value="' . $i . '">' . $i . '</option>';
					}
					echo '</select>
					<select name="month">';
					for ($i=1; $i<=12; $i++)
					{
						if ($i<10) $i = '0' . $i; // Ajout du zéro devant les nombres en dessous de 10
						if ($i == date('m', $userBirthday))	echo '<option selected value="' . $i . '">' . $i . '</option>';
						else echo '<option value="' . $i . '">' . $i . '</option>';
					}
					echo '</select>
					<select name="year">';
					for ($i=1900; $i<=2009; $i++)
					{
						if ($i == date('Y', $userBirthday))	echo '<option selected value="' . $i . '">' . $i . '</option>';
						else echo '<option value="' . $i . '">' . $i . '</option>';
					}
					echo '</select>
				</td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Location</td>
				<td class="userFormField"><input type="text" maxlength="50" size="50" name="location" style="width: 260px" value="' . $userLocation . '" /></td>
			</tr>			
			<tr class="userForm">
				<td class="userFormLegende">Tag (recherche)</td>
				<td class="userFormField"><input type="text" maxlength="50" size="50" name="tag" style="width: 260px" value="' . $userTag . '" /></td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Studies</td>
				<td class="userFormField"><textarea maxlength="250" name="studies" style="width: 260px">' . $userStudies . '</textarea></td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Hobbies</td>
				<td class="userFormField"><textarea maxlength="250" name="hobbies" style="width: 260px">' . $userHobbies . '</textarea></td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Web site</td>
				<td class="userFormField"><input type="text" maxlength="50" size="50" name="website" style="width: 260px" value="' . $userWebsite . '" /></td>
			</tr>
			<tr class="userForm">
				<td class="userFormLegende">Signature</td>
				<td class="userFormField"><textarea maxlength="50" name="signature" style="width: 260px">' . $userSignature . '</textarea></td>
			</tr>
			</table>
		</div>
		<div class="pageList">
			<table class="userForm">
				<tr><td colspan="2">Options de contact</td></tr>
				<tr class="userForm">
					<td class="userFormLegende">Public email</td>
					<td class="userFormField">';
						// Définition du choix d'affichage de l'email
						if ($userPublicEmail == 1) echo '<input checked type="checkbox" name="public_email" value="1" />rendre mon email public';
						else echo '<input unchecked type="checkbox" name="public_email" value="1" />rendre mon email public';
					echo '</td>
				</tr>
				<tr class="userForm">
					<td class="userFormLegende">Msn</td>
					<td class="userFormField"><input type="text" maxlength="50" size="50" name="msn" style="width: 260px" value="' . $userMsn . '" /></td>
				</tr>
				<tr class="userForm">
					<td class="userFormLegende">Iam</td>
					<td class="userFormField"><input type="text" maxlength="50" size="50" name="iam" style="width: 260px" value="' . $userIam . '" /></td>
				</tr>
				<tr class="userForm">
					<td class="userFormLegende">Icq</td>
					<td class="userFormField"><input type="text" maxlength="50" size="50" name="icq" style="width: 260px" value="' . $userIcq . '" /></td>
				</tr>
				<tr class="userForm">
					<td class="userFormLegende">Yahoo</td>
					<td class="userFormField"><input type="text" maxlength="50" size="50" name="yahoo" style="width: 260px" value="' . $userYahoo . '" /></td>
				</tr>
				<tr class="userForm">
					<td class="userFormLegende">Skype</td>
					<td class="userFormField"><input type="text" maxlength="50" size="50" name="skype" style="width: 260px" value="' . $userSkype . '" /></td>
				</tr>
			</table>
			<br />
			<input type="submit" value="Valider" />
			<input type="reset" value="Annuler" />
			
		</div>
	</form>';
}


?>