<?php

//-----------------------------------------------------------------
// Galerie d'images de l'utilisateur
//-----------------------------------------------------------------

// Connexion � la base de donn�es
connectDb();

// Si le membre est bien logg�
if (isset($_SESSION['login']) && isset($_SESSION['password'])) {
	
	// Connexion � la table utilisateurs (users)
	$userLoginSecured = mysql_real_escape_string($_SESSION['login']);
	$userPasswordSecured = mysql_real_escape_string($_SESSION['password']);
	$mysqlQueryUsers = mysql_query("SELECT * FROM users WHERE login='$userLoginSecured' && password='$userPasswordSecured'");
	$mysqlDataUsers = mysql_fetch_array($mysqlQueryUsers);
	
	// D�finition du chemin du dossier images (images)
	$dir = 'web/users/'.$mysqlDataUsers['user_id'].'/images';
	
	// Taille du dossier images
	$folderSize = folderSize($dir);

	// Aucune alerte par d�faut
	$alert = null;
	
	// Aucune image affich�e par d�faut
	$image = null;
	
	//-----------------------------------------------------------------
	// On ajoute une image
	//-----------------------------------------------------------------
	
	// Test si un fichier a �t� envoy�
	if (isset($_FILES['filesend'])) {
		
		if ($_FILES['filesend']['error'] == 0){
			$pathParts 	= pathinfo($_FILES['filesend']['name']);
			$fileExt 	= strtolower($pathParts['extension']); // passage de l'extension en misucule
			$fileName 	= substr($pathParts['basename'],0,strrpos($pathParts['basename'],'.'));
			$imagesize 	= getimagesize($_FILES['filesend']['tmp_name']);
			
			// Detection des erreurs et d�finition des alertes
			$alert .= (($folderSize + $_FILES['filesend']['size']) <= $imageFolderMax) 			? '' : "Epace disponible insuffisant !<br />";
			$alert .= ($_FILES['filesend']['size'] <= $imageMaxSize)							? '' : "Le fichier est trop volumineux !<br />";
			$alert .= (in_array($fileExt, $allowedImgExt))										? '' : "L'extension de ce fichier n'est pas autoris�e !<br />";
			$alert .= (($imagesize[0] <= $imageMaxWidth) && ($imagesize[1] <= $imageMaxHeight))	? '' : "L'image est trop grande !<br />";
			
			// Enregistrement du fichier
			if ($alert == null) {
				// G�n�rateur de nom pour fichier
				do {
				$newName = randomHex();
					$reqName = in_array($newName.'.'.$fileExt, testdir($dir));
				} while ($reqName == true);
				$newName = $fileName.'.'.$newName.'.'.$fileExt;
				// Test si la copie est r�ussie
				$alert .= (move_uploaded_file($_FILES['filesend']['tmp_name'], $dir.'/'.$newName) == 1) ? "L'envoi a bien �t� effectu� !<br />" : "Erreur de copie<br />";
			} else {
				$alert = "Echec de l'envoi de fichier :<br />".$alert;
			}
		} else {
			$alert = "Echec de l'envoi de fichier :<br />".$alert."Probl�me d'envoi de fichier !<br />";
		}
		$alert .= "<a href=\"./index.php?user_images\">Fermer</a>";
	}
	
	//-----------------------------------------------------------------
	// On veut supprimer ou afficher une image
	//-----------------------------------------------------------------
	
	// On test si on veut acc�der � l'image et si cette derni�re existe
	if (!empty($_GET['img']) && in_array($_GET['img'], testdir($dir))) {	
		// On supprimer le fichier
		if (isset($_GET['remove']) && isset($_GET['confirm'])) {
			if (unlink($dir.'/'.$_GET['img'])) {
				$alert = "L'image ".$_GET['img']." � bien �t� supprim�e.<br />
				<a href=\"./index.php?user_images\">Fermer</a>";
			} else {
				$alert = "La suppression � �chou�e suite � un probl�me technique.<br />";
			}
		}
		// On demande la confirmation de la suppression
		elseif (isset($_GET['remove'])) {
			$alert = 'Etes vous s�r(e) de bien vouloir supprimer l\'image '.$_GET['img'].' ?<br />
			<a href="./index.php?user_images&amp;img='.$_GET['img'].'&amp;remove&amp;confirm">oui</a>
			<a href="./index.php?user_images">non</a>';
		}
		// On veut afficher une image
		else {
			$image = '<a href="./index.php?user_images"><img src="'.$dir.'/'.$_GET['img'].'" title="Fermer" alt="" /></a>';
		}
	}
	
	//-----------------------------------------------------------------
	// Informations
	//-----------------------------------------------------------------
	
	// Liste des extensions autoris�es
	$extAllowed = '';
	for ($i=0; $i<count($allowedImgExt); $i++) {
		$extAllowed = $extAllowed.$allowedImgExt[$i];
		$extAllowed = ($i == (count($allowedImgExt) - 1)) ? $extAllowed : $extAllowed." / "; // Ajoute de "/" entre les extentions autoris�es
	}
	
	// Mise � jour de la taille du dossier images
	$folderSize = folderSize($dir);
	
	// Taille maximale par fichier
	$maxSizeAllowed = $imageMaxSize/'1000'.' Ko';
	
	// Dimensions maximales par fichier
	$maxDimensionsAllowed	= $imageMaxWidth.' pixels x '.$imageMaxWidth.' pixels';
	
	// Variables � afficher
	$imagesAvailable 		= $nbfolder = nbFolder($dir);
	$folderSpaceUsed 		= round($folderSize / 1000)." Ko / ".round($imageFolderMax / 1000);
	$folderSpaceAvailable 	= round(($imageFolderMax - $folderSize) / 1000)." Ko / ".round($imageFolderMax / 1000);
	
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
			<img src="./design/' . $userDesign . '/images/' . $language . '/images.png" />
		</div>
		<div class="divInPageText">
			Images disponibles : '.$imagesAvailable.'<br />
			Espace utilis� : '.$folderSpaceUsed.'<br />
			Espace disponible : '.$folderSpaceAvailable.'<br />
			Taille maximale par fichier : '.$maxSizeAllowed.'<br />
			Extensions autoris�es : '.$extAllowed.'<br />
			Dimensions maximales autoris�es : '.$maxDimensionsAllowed.'
		</div>
	</div>';
	
	if ($image != null)
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px; text-align: center;">
			'.$image.'<br />
			<a href="./index.php?user_images">Fermer</a>
		</div>';
	
	if ($alert != null) {
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
			'.$alert.'
		</div>';
	}
	
	echo '<div>
		<form method="post" action="./index.php?user_images" enctype="multipart/form-data">
			<p>
				Envoyer une image :<br />
				<input type="file" name="filesend" />
				<input type="submit" value="Envoyer le fichier" />
			</p>
		</form>
	</div>';
	
	echo '<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">';
		$images = testDir($dir);
		$nbImages = nbFolder($dir);
		for ($i=0; $i<$nbImages; $i++) {
			$file = $dir.'/'.$images[$i];
			$size = sizeOfFile($file);
			$imagesize = getimagesize($file);
				
			echo '<div class="imageBox">
				<div class="imageInBox">
					<a href="./index.php?user_images&amp;img='.$images[$i].'">
						<img class="imageMini" src="'.$dir.'/'.$images[$i].'" title="Afficher en taille r�elle" alt="" />
					</a>
				</div>
				<div>
					'.$size.'<br />
					'.$imagesize[0].'x'.$imagesize[1].'<br />
					<a href="./index.php?user_images&amp;img='.$images[$i].'&amp;remove"><img class="imageButton" src="./design/'.$userDesign.'/editech/remove.png" title="Supprimer" alt="" /></a>
				</div>			
			</div>';
		}				
	echo '</div>';
}

//-----------------------------------------------------------------
// Affichage de la page de login
//-----------------------------------------------------------------

else
{
	echo 'vous devez vous logg� en premier afin d\'acc�der � cette section';
}

?>