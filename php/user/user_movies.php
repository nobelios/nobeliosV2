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
	
	// Définition du chemin du dossier fichiers (files)
	$dir = 'web/users/'.$mysqlDataUsers['user_id'].'/files';
	
	// Taille du dossier fichiers
	$folderSize = folderSize($dir);
	
	// Aucune alerte par défaut
	$alert = null;
	
	// Aucune information par défaut
	$fileInfo = null;
	
	// Liste des fichiers 
	$files = testDir($dir);
	$nbFiles = nbFolder($dir);
	
	// $_SESSION['user_files_sort'].$_SESSION['user_files_order'];
	
	//-----------------------------------------------------------------
	// On ajoute un fichier
	//-----------------------------------------------------------------
	
	// Test si un fichier a été envoyé
	if (isset($_FILES['filesend'])) {
		
		if ($_FILES['filesend']['error'] == 0){
			$pathParts = pathinfo($_FILES['filesend']['name']);
			$fileExt = strtolower($pathParts['extension']); // passage de l'extension en misucule
			$fileName = substr($pathParts['basename'],0,strrpos($pathParts['basename'],'.'));
			$filesize = filesize($_FILES['filesend']['tmp_name']);
			
			// Detection des erreurs et définition des alertes
			$alert = (($folderSize + $_FILES['filesend']['size']) <= $fileFolderMax)	? $alert : $alert."Epace disponible insuffisant !<br />";
			$alert = ($_FILES['filesend']['size'] <= $fileMaxSize)						? $alert : $alert."Le fichier est trop volumineux !<br />";
			$alert = (in_array($fileExt, $allowedFileExt))								? $alert : $alert."L'extension de ce fichier n'est pas autorisée !<br />";
			
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
			$alert = "Echec de l'envoi de fichier :<br />".$alert."ProblÃ¨me d'envoi de fichier !<br />";
		}
		$alert = $alert."<a href=\"./index.php?user_files\">Fermer</a>";
	}
	
	//-----------------------------------------------------------------
	// Affichage des informations d'un fichier
	//-----------------------------------------------------------------
	
	// Information sur les fichiers
	$files = testDir($dir);
	$nbFiles = nbFolder($dir);
	
	if (isset($_GET['id']) && in_array($_GET['id'], $files))
	{
		// Nom du fichier
		$file = $dir . '/' . $_GET['id'];
		$fileName = 'nom : ' . $_GET['id'];
		$fileTimestamp = filemtime($file);
		$fileTime = 'derniÃ¨re modification le : ' . date('d\/m\/Y \à h\:m\:s \G\m\t' ,$fileTimestamp);
		$fileSize = 'taille : ' . round(filesize($file)/1000) . 'Ko';
		$fileLink =  '';
		$pathParts = pathinfo($file);
		$fileExt = strtolower($pathParts['extension']);
		$fileType = 'type : ' . $fileExt;
		// Lien du fichier (disponible en copie)
		// Télécharger le fichier (lien qui pointe sur le téléchargement du fichier, mÃªme si c'est une image)
		$fileInfo = 'demande d\'affichage';
	}
	
	
	//-----------------------------------------------------------------
	// On veut supprimer des fichiers
	//-----------------------------------------------------------------
	
	/*
	// On test si on veut accéder au fichier et si ce dernier existe
	if (!empty($_POST['remove']))

	for ($i=0; $i<$nbImages; $i++)
		if (isset($_POST['select'.$i]) && in_array($_POST['select'.$i]], testdir($dir))) {	
		// On supprimer le fichier
		if (isset($_GET['remove']) && isset($_GET['confirm'])) {
			if (unlink($dir.'/'.$_GET['file'])) {
				$alert = "Le fichier ".$_GET['file']." à bien été supprimé.<br />
				<a href=\"./index.php?user_files\">Fermer</a>";
			} else {
				$alert = "La suppression à échouée suite à un problÃ¨me technique.<br />";
			}	
		}
		// On demande la confirmation de la suppression
		elseif (isset($_GET['remove'])) {
			$alert = 'Etes vous sÃ»r(e) de bien vouloir supprimer le fichier '.$_GET['file'].' ?<br />
			<a href="./index.php?user_files&amp;file='.$_GET['file'].'&amp;remove&amp;confirm">oui</a>
			<a href="./index.php?user_files">non</a>';
		}
	}
	*/
	
	
	//------------------------------------------------------------------
	// Classement des fichiers et type d'affichage
	//------------------------------------------------------------------
	
	if (isset($_POST['remove'])) {
		// Information sur les fichiers
		$files = testDir($dir);
		$nbFiles = nbFolder($dir);
		if (isset($_POST['confirm'])) {
			for ($i=0; $i<$nbFiles; $i++) {
				if (isset($_POST['select' . $i])) {
					if (in_array($_POST['select' . $i], $files)) {
						$listeArray[] = $_POST['select' . $i];
						if (unlink($dir . '/' . $_POST['select' . $i])) {
							$alert = "Le fichier " . $_POST['select' . $i] . " à bien été supprimé.<br />
							<a href=\"./index.php?user_files\">Fermer</a>";
						} else {
							$alert = "La suppression à échouée suite à un problÃ¨me technique.<br />";
						}
					}
				}
			}
		} else {
			$nbFilesRemove = 0;
			for ($i=0; $i<$nbFiles; $i++) {
				if (isset($_POST['select' . $i])) {
					$nbFilesRemove++;
				}
			}
			if ($nbFilesRemove != 0)
			{
				$alert = '<form action="./index.php?user_files" method="post">';
				if ($nbFilesRemove > 1)	$alert = $alert . 'Voulez-vous supprimer les fichiers suivants ? <br />';
				else $alert = $alert . 'Voulez-vous supprimer le fichier suivant ? <br />';
				for ($i=0; $i<$nbFiles; $i++) {
					if (isset($_POST['select' . $i])) {
						$alert = $alert . '<input type="hidden" name="select' . $i . '" value="' . $_POST['select' . $i] . '" />';
						$alert = $alert . $_POST['select' . $i] . '<br />';
					}
				}
				$alert = $alert . '<input type="hidden" name="confirm">';
				$alert = $alert . '<input type="submit" name="remove" value="Confirmer la suppression" /></form>';
			}
		}
	}	
	
	//------------------------------------------------------------------
	// Classement des fichiers et type d'affichage
	//------------------------------------------------------------------
	
	// Suffix pour les sessions
	$sessionPage = 'user_files_';
	
	// Options de classement pour les fichiers utilisateur (enregistrement en session)
	if (!isset($_SESSION[$sessionPage . 'view'])) $_SESSION[$sessionPage . 'view'] = 'list';
	if (!isset($_SESSION[$sessionPage . 'order'])) $_SESSION[$sessionPage . 'order'] = 'name';
	if (!isset($_SESSION[$sessionPage . 'sort'])) $_SESSION[$sessionPage . 'sort'] = 'asc';
	if (isset($_GET['view']) && (($_GET['view'] == 'list') || ($_GET['view'] == 'miniatures'))) $_SESSION[$sessionPage . 'view'] = $_GET['view'];
	if (isset($_GET['order']) && (($_GET['order'] == 'name') || ($_GET['order'] == 'size') || ($_GET['order'] == 'type'))) $_SESSION[$sessionPage . 'order'] = $_GET['order'];
	if (isset($_GET['sort']) && (($_GET['sort'] == 'desc') || ($_GET['sort'] == 'asc'))) $_SESSION[$sessionPage . 'sort'] = $_GET['sort'];
	
	// Information sur les fichiers
	$files = testDir($dir);
	$nbFiles = nbFolder($dir);
	
	// Déclaration des tables
	$tableName = array();
	$tableSize = array();
	$tableType = array();
	
	// Boucle de liste des fichiers pour trier les données
	for ($i=0; $i<$nbFiles; $i++) {
		$file = $dir . '/' . $files[$i];
		$size = filesize($file);
		$pathParts = pathinfo($file);
		$fileExt = strtolower($pathParts['extension']);
		$tableName[$i] = $files[$i];
		$tableSize[$i] = $size;
		$tableType[$i] = $fileExt;
	}
	
	$tableSort = array(
		'name' => $tableName,
		'size' => $tableSize,
		'type' => $tableType
	);
	
	// On procÃ¨de à un tri sur le type de données	
	$tableOrder = $tableSort[$_SESSION['user_files_order']];
	
	// On procÃ¨de à un tri alphanumérique
	if ($_SESSION['user_files_sort'] == 'desc') arsort($tableOrder);
	else asort($tableOrder);
	
	//-----------------------------------------------------------------
	// Informations
	//-----------------------------------------------------------------
	
	// Liste des extensions autorisées
	$extAllowed = '';
	for ($i=0; $i<count($allowedFileExt); $i++) {
		$extAllowed = $extAllowed.$allowedFileExt[$i];
		$extAllowed = ($i == (count($allowedFileExt) - 1)) ? $extAllowed : $extAllowed." / "; // Ajoute de "/" entre les extentions autorisées
	}
	
	// Mise à jour de la taille du dossier images
	$folderSize = folderSize($dir);
	
	// Taille maximale par fichier
	$maxSizeAllowed = $fileMaxSize/'1000'.' Ko';
	
	// Affichage de l'espace utilisé/disponible et restant
	$filesAvailable 		= $nbfolder = nbFolder($dir);
	$folderSpaceUsed 		= round($folderSize / 1000)." Ko / ".round($fileFolderMax / 1000);
	$folderSpaceAvailable 	= round(($fileFolderMax - $folderSize) / 1000)." Ko / ".round($fileFolderMax / 1000);
	
	//-----------------------------------------------------------------
	// Partie Affichage
	//-----------------------------------------------------------------
	?>
	<script type="text/javascript" >
	function checkAll(form,booleen,num_table)
	{
		for (i=0, n=form.elements.length; i<n; i++) {
			if (form.elements[i].id.indexOf('table'+num_table) != -1)
				form.elements[i].checked = booleen;
		}
	}
	function checkTheBox(form,box,num_table) {
		if (form.elements['table'+num_table+'_chk'+box].checked)
			form.elements['table'+num_table+'_chk'+box].checked = false;
		else
			form.elements['table'+num_table+'_chk'+box].checked = true;
	}
	
	</script>
	<?php
	
	echo '<div class="linker">
		<a href="./index.php?home">Accueil</a> > 
		<a href="./index.php?user_space">Espace utilisateur</a> > 
		Mes fichiers<br />
	</div>';
	
	echo '<div class="divInPage">
		<div class="divInPageImage">
			<img src="./design/' . $userDesign . '/images/' . $language . '/movies.png" />
		</div>
		<div class="divInPageText">
			Fichiers disponibles : '.$filesAvailable.'<br />
			Espace utilisé : '.$folderSpaceUsed.'<br />
			Espace disponible : '.$folderSpaceAvailable.'<br />
			Taille maximale par fichier : '.$maxSizeAllowed.'<br />
			Extensions autorisées : '.$extAllowed.'<br />
		</div>
	</div>';
		
	if ($fileInfo != null)
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
			' . $fileInfo . '<br />
			' . $fileSize . '<br />
			' . $fileName . '<br />
			' . $fileTime . '<br />
			' . $fileType . '<br />
			<a href="./index.php?user_files">Fermer</a>
		</div>';
	
	if ($alert != null) {
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
			'.$alert.'
		</div>';
	}
	
	echo '<div>
		<form method="post" action="./index.php?user_files" enctype="multipart/form-data">
			<p>
				Envoyer un fichier :<br />
				<input type="file" name="filesend" />
				<input type="submit" value="Envoyer le fichier" />
			</p>
		</form>
	</div>';
	
	// Affichage sous forme de liste
	if ($_SESSION[$sessionPage . 'view'] == 'list') {
		echo '<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">
			<form id="form" action="./index.php?user_files" method="post">
					<a href="index.php?user_files&amp;view=list">liste</a> / <a href="index.php?user_files&amp;view=miniatures">miniatures</a><br />
					<div>
						<table cellspacing="0px" class="legendTable">
							<tr class="legendRow">
								<td class="legendCell" style="width: 500px;">
									Name 
									<a href="index.php?user_files&amp;order=name&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
									<a href="index.php?user_files&amp;order=name&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
								</td>
								<td class="legendCell" style="width: 80px;">
									Size
									<a href="index.php?user_files&amp;order=size&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
									<a href="index.php?user_files&amp;order=size&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
								</td>
								<td class="legendCell">
									Type 
									<a href="index.php?user_files&amp;order=type&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
									<a href="index.php?user_files&amp;order=type&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
								</td>
							</tr>
						</table>
					</div>
					<div class="listDisplay">';
					$files = testDir($dir);
					$nbFiles = nbFolder($dir);
					if ($nbFiles == 0) {
						echo 'Ce dossier est vide';
					} else {
						echo '<table cellspacing="0px" class="listTable">';
						$i=0; // Raz du compteur
						foreach ($tableOrder as $key=>$val) {
							echo '<tr onclick="checkTheBox(form, ' . $i . ', 1);" class="listRowColor1">
								<td class="listCell" style="width: 500px">
									<input id="table1_chk' . $i . '" type="checkbox" name="select' . $i . '" value="' . $tableName[$key] . '"> 
									<img src="./design/' . $userDesign . '/images/fr/icone_mini_image.png">
									<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '">' . $tableName[$key] . '</a>
								</td>
								<td class="listCell" style="width: 80px">' . round($tableSize[$key]/1000) . 'Ko</td>
								<td class="listCell">' . $tableType[$key] . '</td>
							</tr>';
							$i++;
						}
						echo '</form>
						</table>';
					}
				echo '</div>
				<div style="text-align: left; padding-top: 10px">
					<a onclick="checkAll(form, true, 1);">Tout selectionner</a> / <a onclick="checkAll(form, false, 1);">Tout déselectionner</a><br />
					<input type="submit" name="remove" value="Supprimer la selection">
				</div>
			</form>
		</div>';
	}
	
	// Affichage sous forme de miniatures
	elseif ($_SESSION[$sessionPage . 'view'] == 'miniatures') {
		echo 'mini<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">
			<form id="form" action="./index.php?user_files" method="post">
				<div style="max-height:300px; overflow: auto">
					<a href="index.php?user_files&amp;view=list">liste</a> / <a href="index.php?user_files&amp;view=miniatures">miniatures</a><br />
					<a href="index.php?user_files&amp;order=name&amp;sort=asc">Name_up</a>/<a href="index.php?user_files&amp;order=name&amp;sort=desc">Name_down</a> / 
					<a href="index.php?user_files&amp;order=size&amp;sort=asc">Size_up</a>/<a href="index.php?user_files&amp;order=size&amp;sort=desc">Size_down</a> / 
					<a href="index.php?user_files&amp;order=type&amp;sort=asc">Type_up</a>/<a href="index.php?user_files&amp;order=type&amp;sort=desc">Type_down</a><br />';
					$files = testDir($dir);
					$nbFiles = nbFolder($dir);
					if ($nbFiles == 0) {
						echo 'Ce dossier est vide';
					} else {
						echo '<table cellspacing="0px" style="border: 0px none;">';
						$i=0; // Raz du compteur
						foreach ($tableOrder as $key=>$val) {
							echo '<tr style="border: 0px none">
								<td style="border: 0px none; width: 500px">
									<input id="table1_chk'.$i.'" type="checkbox" name="select' . $i . '" value="' . $files[$i] . '"> 
									<img src="./design/'.$userDesign.'/images/fr/icone_mini_image.png">
									<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '">' . $tableName[$key] . '</a>
								</td>
								<td style="border: 0px none; width: 80px">' . round($tableSize[$key]/1000) . 'Ko</td>
								<td style="border: 0px none;">' . $tableType[$key] . '</td>
							</tr>';
							$i++;
						}
						echo '</form>
						</table>';
					}
				echo '</div>
				<div style="text-align: left; padding-top: 10px">
					<a onclick="checkAll(form, true, 1);">Tout selectionner</a> / <a onclick="checkAll(form, false, 1);">Tout déselectionner</a><br />
					<input type="submit" name="remove" value="Supprimer la selection">
				</div>
			</form>
		</div>';
	}
}

//-----------------------------------------------------------------
// Affichage de la page de login
//-----------------------------------------------------------------

else
{
	echo 'vous devez vous loggé en premier afin d\'accéder à cette section';
}







?>