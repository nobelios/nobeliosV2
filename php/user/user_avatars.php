<?php

//-----------------------------------------------------------------
// Galerie d'avatars de l'utilisateur
//-----------------------------------------------------------------

// Connexion à la base de données
connectDb();

// Si le membre est bien loggé
if (isset($_SESSION['login']) && isset($_SESSION['password'])) {
	
	// Connexion à la table utilisateurs (users)
	$userLoginSecured = mysql_real_escape_string($_SESSION['login']);
	$userPasswordSecured = mysql_real_escape_string($_SESSION['password']);
	$mysqlQueryUsers = mysql_query("SELECT * FROM users WHERE login='$userLoginSecured' && password='$userPasswordSecured'");
	$mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers);
	
	// Définition du chemin du dossier avatars (avatars)
	$dir = 'web/users/'.$mysqlDataUsers['user_id'].'/avatars';
	
	// Taille du dossier avatars
	$folderSize = folderSize($dir);
	
	// Aucune alerte par défaut
	$alert = null;
	
	//-----------------------------------------------------------------
	// On ajoute un avatar
	//-----------------------------------------------------------------
	
	// Test si un fichier a été envoyé
	if (isset($_FILES['filesend'])) {
		
		if ($_FILES['filesend']['error'] == 0){
			$pathParts = pathinfo($_FILES['filesend']['name']);
			$fileExt = strtolower($pathParts['extension']); // passage de l'extension en misucule
			$fileName = substr($pathParts['basename'],0,strrpos($pathParts['basename'],'.'));
			$imagesize = getimagesize($_FILES['filesend']['tmp_name']);
			
			// Detection des erreurs et définition des alertes
			$alert = (($folderSize + $_FILES['filesend']['size']) <= $avatarFolderMax) 				? $alert : $alert."Epace disponible insuffisant !<br />";
			$alert = ($_FILES['filesend']['size'] <= $avatarMaxSize)								? $alert : $alert."Le fichier est trop volumineux !<br />";
			$alert = (in_array($fileExt, $allowedAvatarExt))										? $alert : $alert."L'extension de ce fichier n'est pas autorisée !<br />";
			$alert = (($imagesize[0] <= $avatarMaxWidth) && ($imagesize[1] <= $avatarMaxHeight))	? $alert : $alert."L'image est trop grande !<br />";
			
			// Enregistrement du fichier
			if ($alert == null) {
				// Générateur de nom pour fichier
				do {
				$newName = randomHex();
					$reqName = in_array($newName.'.'.$fileExt, testdir($dir));
				} while ($reqName == true);
				$newName = $fileName.'.'.$newName.'.'.$fileExt;
				// Test si la copie est réussie
				$alert = (move_uploaded_file($_FILES['filesend']['tmp_name'], $dir.'/'.$newName) == 1) ? $alert."L'envoi a bien été effectué !<br />" : $alert."Erreur de copie<br />";
			} else {
				$alert = "Echec de l'envoi de fichier :<br />".$alert;
			}
		} else {
			$alert = "Echec de l'envoi de fichier :<br />".$alert."Problème d'envoi de fichier !<br />";
		}
		$alert = $alert."<a href=\"./index.php?user_avatars\">Fermer</a>";
	}
	
	//-----------------------------------------------------------------
	// On veut afficher un autre avatar
	//-----------------------------------------------------------------
	
	// Enregistrement de l'avatar
	if (isset($_GET['avatar']) && isset($_GET['select'])) {
		$userAvatar = htmlspecialchars(mysql_real_escape_string($_GET['avatar']));
		mysql_query("UPDATE users SET avatar='$userAvatar'");
	} else {
		$userAvatar = $mysqlDataUsers['avatar'];
	}
	
	// Afficher avatar actuel
	if (in_array($userAvatar, testdir($dir))) $avatar = '<img src="'.$dir.'/'.$userAvatar.'" />';
	else $avatar = 'aucun<br />avatar';
	
	//-----------------------------------------------------------------
	// On veut supprimer un avatar
	//-----------------------------------------------------------------
	
	// On test si on veut accéder à l'image et si cette dernière existe
	if (!empty($_GET['avatar']) && in_array($_GET['avatar'], testdir($dir))) {	
		// On supprimer le fichier
		if (isset($_GET['remove']) && isset($_GET['confirm'])) {
			if (unlink($dir.'/'.$_GET['avatar'])) {
				$alert = "L'avatar ".$_GET['avatar']." à bien été supprimé.<br />
				<a href=\"./index.php?user_avatars\">Fermer</a>";
			} else {
				$alert = "La suppression à échouée suite à un problème technique.<br />";
			}	
		}
		// On demande la confirmation de la suppression
		elseif (isset($_GET['remove'])) {
			$alert = 'Etes vous sûr(e) de bien vouloir supprimer l\'avatar '.$_GET['avatar'].' ?<br />
			<a href="./index.php?user_avatars&amp;avatar='.$_GET['avatar'].'&amp;remove&amp;confirm">oui</a>
			<a href="./index.php?user_avatars">non</a>';
		}
	}
	
	//-----------------------------------------------------------------
	// Informations
	//-----------------------------------------------------------------
	
	// Liste des extensions autorisées
	$extAllowed = '';
	for ($i=0; $i<count($allowedAvatarExt); $i++) {
		$extAllowed = $extAllowed.$allowedAvatarExt[$i];
		$extAllowed = ($i == (count($allowedAvatarExt) - 1)) ? $extAllowed : $extAllowed." / "; // Ajoute de "/" entre les extentions autorisées
	}
		
	// Mise à jour de la taille du dossier images
	$folderSize = folderSize($dir);
	
	// Taille maximale par fichier
	$maxSizeAllowed = $avatarMaxSize/'1000'.' Ko';
	
	// Dimensions maximales par fichier
	$maxDimensionsAllowed = $avatarMaxWidth.' pixels x '.$avatarMaxWidth.' pixels';
	
	// Affichage de l'espace utilisé/disponible et restant
	$avatarsAvailable 		= $nbfolder = nbFolder($dir);
	$folderSpaceUsed 		= round($folderSize / 1000)." Ko / ".round($avatarFolderMax / 1000);
	$folderSpaceAvailable 	= round(($avatarFolderMax - $folderSize) / 1000)." Ko / ".round($avatarFolderMax / 1000);
	
	//-----------------------------------------------------------------
	// Partie Affichage
	//-----------------------------------------------------------------
	
	echo '<div class="linker">
		<a href="./index.php?home">Accueil</a> > 
		<a href="./index.php?user_space">Espace utilisateur</a> > 
		Mes avatars<br />
	</div>';
	
	echo '<div class="divInPage">
		<div class="divInPageImage">
			<div class="divInPageImageBox">
				'.$avatar.'
			</div>
		</div>
		<div class="divInPageText">
			Avatars disponibles : '.$avatarsAvailable.'<br />
			Espace utilisé : '.$folderSpaceUsed.'<br />
			Espace disponible : '.$folderSpaceAvailable.'<br />
			Taille maximale par fichier : '.$maxSizeAllowed.'<br />
			Extensions autorisées : '.$extAllowed.'<br />
			Dimensions maximales autorisées : '.$maxDimensionsAllowed.'
		</div>
	</div>';
	
	if ($alert != null) {
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
			'.$alert.'
		</div>';
	}
	
	echo '<div>
		<form method="post" action="./index.php?user_avatars" enctype="multipart/form-data">
			<p>
				Envoyer un avatar :<br />
				<input type="file" name="filesend" />
				<input type="submit" value="Envoyer le fichier" />
			</p>
		</form>
	</div>';
	
	
	// Affichage des avatars sour forme de miniature
	echo '<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">';
		$images = testDir($dir);
		$nbImages = nbFolder($dir);
		for ($i=0; $i<$nbImages; $i++) {
			$file = $dir.'/'.$images[$i];
			$size = sizeOfFile($file);
			$imagesize = getimagesize($file);
				
			echo '<div class="imageBox">
				<div class="imageInBox">
					<a href="./index.php?user_avatars&amp;avatar='.$images[$i].'&amp;select">
						<img class="imageMini" src="'.$dir.'/'.$images[$i].'" title="Afficher cet avatar" alt="" />
					</a>
				</div>
				<div>
					'.$size.'<br />
					'.$imagesize[0].'x'.$imagesize[1].'<br />
					<a href="./index.php?user_avatars&amp;avatar='.$images[$i].'&amp;remove"><img class="imageButton" src="./design/'.$userDesign.'/editech/remove.png" title="Supprimer" alt="" /></a>
				</div>			
			</div>';
		}		
	echo '</div>';
	
	/*
	// Affichage des avatars sour forme de liste
	echo '<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">
		<div style="overflow: auto; height: 400px">
		<table collspacing="0px" style="border: 0px none;">';
			$images = testDir($dir);
			$nbImages = nbFolder($dir);
			for ($i=0; $i<$nbImages; $i++) {
				$file = $dir.'/'.$images[$i];
				$size = sizeOfFile($file);
				$imagesize = getimagesize($file);
					
				echo '<tr>
					<td style="border: 0px none; width: 10px;">';
						if (isset($_GET['checkall'])) echo '<input checked type="checkbox" name="select" value="'.$images[$i].'" />';
						else echo '<input type="checkbox" name="select" value="'.$images[$i].'" />';
					echo '</td>
					<td style="border: 0px none; width: 500px;">
						'.$images[$i].'
					</td>
					<td style="border: 0px none; width: 80px;">
						'.$size.'
					</td>
					<td style="border: 0px none;">
						'.$imagesize[0].'x'.$imagesize[1].'
					</td>
				</tr>';
		}		
	echo '</table>
		</div>
		<div style="text-align: left;">
			<a href="./index.php?user_avatars&amp;checkall">Tout selectionner</a> / <a href="./index.php?user_avatars">Tout déselectionner</a>
		</div>
	</div>';
	*/
}

//-----------------------------------------------------------------
// Affichage de la page de login
//-----------------------------------------------------------------

else
{
	echo 'vous devez vous loggé en premier afin d\'accéder à cette section';
}

?>