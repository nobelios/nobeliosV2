<?php

//*****************************************************************
//	Nobelios V2.0 beta
//	Galerie des fichiers utilisateur
//	Script par Geoffrey HAUTECOUVERTURE
//	Toute reproduction totale ou partielle interdite
//	juillet - 2009
//*****************************************************************


//-----------------------------------------------------------------
// Galerie de fichiers de l'utilisateur
//-----------------------------------------------------------------

// Connexion à la base de données
connectDb();

// Si le membre est bien loggé
if (isset($_SESSION['login']) && isset($_SESSION['password']))
{
	// Connexion à la table utilisateurs (users)
	$userLoginSecurised		= mysql_real_escape_string($_SESSION['login']);
	$userPasswordSecurised	= mysql_real_escape_string($_SESSION['password']);
	$mysqlQueryUsers 		= mysql_query("SELECT * FROM users WHERE login='$userLoginSecurised' && password='$userPasswordSecurised'");
	$mysqlDataUsers			= mysql_fetch_array($mysqlQueryUsers);
	
	// Définition du chemin du dossier
	$dir = 'web/users/' . $mysqlDataUsers['user_id'] . '/files';
	
	// Taille du dossier
	$folderSize = folderSize($dir);
	
	// Aucune alerte par défaut
	$alert = null;
	
	// Aucune information par défaut
	$fileInfo = null;
	
	//------------------------------------------------------------------
	// Définition de l'affichage
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
	
	
	//-----------------------------------------------------------------
	// On ajoute un fichier
	//-----------------------------------------------------------------
	
	// Test si un fichier a été envoyé
	if (isset($_FILES['filesend'])) {
		if ($_FILES['filesend']['error'] == 0){
			$pathParts = pathinfo($_FILES['filesend']['name']);
			$fileExt = strtolower($pathParts['extension']); // passage de l'extension en minuscule
			$fileName = substr($pathParts['basename'],0,strrpos($pathParts['basename'],'.'));
			$filesize = filesize($_FILES['filesend']['tmp_name']);
			
			// Detection des erreurs et définition des alertes
			$alert = (($folderSize + $_FILES['filesend']['size']) <= $fileFolderMax)	? $alert : $alert . $message['alert_upload_err_disk_space'] . '<br />';
			$alert = ($_FILES['filesend']['size'] <= $fileMaxSize)						? $alert : $alert . $message['alert_upload_err_file_size'] . '<br />';
			$alert = (in_array($fileExt, $allowedFileExt))								? $alert : $alert . $message['alert_upload_err_file_ext'] . '<br />';
			
			// Enregistrement du fichier
			if ($alert == null) {
				// Générateur de nom pour fichier
				do {
				$newName = randomHex();
					$reqName = in_array($newName . '.' . $fileExt, testdir($dir));
				} while ($reqName == true);
				$newName = $fileName . '.' . $newName . '.' . $fileExt;
				// Test si la copie est réussie
				$alert = (move_uploaded_file($_FILES['filesend']['tmp_name'], $dir . '/' . $newName) == 1) ? $alert . $message['alert_upload_succeded'] . '<br />' : $alert . $message['alert_upload_err_copy'] . '<br />';
			} else {
				$alert = $message['alert_upload_err_failure'] . '<br />' . $alert;
			}			
		} else {
			$alert = $message['alert_upload_err_failure'] . '<br />';
		}
		// Detection des erreurs et définition des alertes
		$alert = (($_FILES['filesend']['error']) == 1) ? $alert . $message['alert_upload_err_ini_size'] . '<br />' 	: $alert; 		// UPLOAD_ERR_INI_SIZE
		$alert = (($_FILES['filesend']['error']) == 2) ? $alert . $message['alert_upload_err_form_size'] . '<br />' : $alert;		// UPLOAD_ERR_FORM_SIZE
		$alert = (($_FILES['filesend']['error']) == 3) ? $alert . $message['alert_upload_err_partial'] . '<br />'	: $alert; 		// UPLOAD_ERR_PARTIAL
		$alert = (($_FILES['filesend']['error']) == 4) ? $alert . $message['alert_upload_err_no_file'] . '<br />'	: $alert; 		// UPLOAD_ERR_NO_FILE
		$alert = $alert . '<a href="./index.php?user_files">' . $message['action_close'] . '<br /></a>';
	}

	//-----------------------------------------------------------------
	// On veut supprimer un ou plusieurs fichiers 
	// ou afficher les informations d'un fichier
	//-----------------------------------------------------------------
	
	// Liste des fichiers 
	$files = testDir($dir);
	$nbFiles = nbFolder($dir);
	
	// On demande la suppression de un ou plusieurs fichiers (affichage en liste)
	if (isset($_POST['remove']) && ($_SESSION[$sessionPage . 'view'] == 'list')) {
		if (isset($_POST['confirm'])) {
			for ($i=0; $i<$nbFiles; $i++) {
				if (isset($_POST['select' . $i])) {
					if (in_array($_POST['select' . $i], $files)) {
						$listeArray[] = $_POST['select' . $i];
						if (unlink($dir . '/' . $_POST['select' . $i])) {
							$alert = $alert . "Le fichier " . $_POST['select' . $i] . " à bien été supprimé.<br />";
						} else {
							$alert = $alert . $message['alert_remove_failure'] . '<br />';
						}
					}
				}
			}
			$alert = $alert . '<a href="./index.php?user_files">' . $message['action_close']  . '<br /></a>';
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
				if ($nbFilesRemove > 1)	$alert = $alert . $message['remove_following_files'] . '<br />';
				else $alert = $alert . $message['remove_following_file'] . '<br />';
				for ($i=0; $i<$nbFiles; $i++) {
					if (isset($_POST['select' . $i])) {
						$alert = $alert . '<input type="hidden" name="select' . $i . '" value="' . $_POST['select' . $i] . '" />';
						$alert = $alert . $_POST['select' . $i] . '<br />';
					}
				}
				$alert = $alert . '<input type="hidden" name="confirm">';
				$alert = $alert . '<input type="submit" name="remove" value=' . $message['action_confirm_yes'] . ' />';
				$alert = $alert . '<a href="./index.php?user_files">' . $message['action_confirm_no'] . '</a></form>';
			}
		}
	}
	
	// On demande la suppression d'un unique fichier (affichage en miniatures)
	elseif (isset($_GET['remove']) && ($_SESSION[$sessionPage . 'view'] == 'miniatures')) {
		if (isset($_GET['id']) && in_array($_GET['id'], testdir($dir))) {	
			// On supprimer le fichier
			if (isset($_GET['remove']) && isset($_GET['confirm'])) {
				if (unlink($dir.'/'.$_GET['id'])) {
					$alert = "Le fichier ".$_GET['id']." à bien été supprimé.<br />
					<a href=\"./index.php?user_files\">" . $message['action_close'] . "</a>";
				} else {
					$alert = $message['alert_remove_failure'] . '<br />';
				}	
			}
			// On demande la confirmation de la suppression
			elseif (isset($_GET['remove'])) {
				$alert = 'Etes vous sûr(e) de bien vouloir supprimer le fichier '.$_GET['id'].' ?<br />
				<a href="./index.php?user_files&amp;id='.$_GET['id'].'&amp;remove&amp;confirm">' . $message ['action_confirm_yes'] . '</a>
				<a href="./index.php?user_files">' . $message['action_confirm_no'] . '</a>';
			}
		}
	}
	
	// Affichage des informations du fichier selectionné
	elseif (isset($_GET['id']) && in_array($_GET['id'], $files)) {
		$file			= $dir . '/' . $_GET['id'];
		$fileName 		= $message['legend_file_name'] . ' : ' . $_GET['id'];
		$fileTimestamp 	= filemtime($file);
		$fileTime		= $message['legend_file_date'] . ' : ' . date('d\/m\/Y \- h\:m\:s \G\m\t' ,$fileTimestamp);
		$fileSize		= $message['legend_file_size'] . ' : ' . round(filesize($file)/1000) . 'Ko';
		$fileLink		= $message['legend_file_link'] . ' : ' . $domainName . '/web/users/' .$userId. '/files/' . $_GET['id'];
		$pathParts		= pathinfo($file);
		$fileExt		= strtolower($pathParts['extension']);
		$fileType		= $message['legend_file_type'] . ' : ' . $filesExtDef[$fileExt];
		$fileBigIcone	= 'icone_big_' . $filesExtType[$fileExt] . '.png';
		// lien direct
		$fileDownload	= '<a href="' . $domainName . '/web/users/' .$userId. '/files/' . $_GET['id'] . '">' . $message['action_file_download'] . '</a>';
		
		// Lien du fichier (disponible en copie)
		// Télécharger le fichier (lien qui pointe sur le téléchargement du fichier, même si c'est une image)
		$fileInfo		= true;
	}
	
	
	//-----------------------------------------------------------------
	// Classement des fichiers
	//-----------------------------------------------------------------
	
	// Mise à jour des information sur le fichiers
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
	
	// On procède à un tri sur le type de données	
	$tableOrder = $tableSort[$_SESSION['user_files_order']];
	
	// On procède à un tri alphanumérique
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
		function checkAll(form,booleen,num_table) {
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
		<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a> > 
		<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a> > 
		' . $message['navigation_tree_user_files'] . '<br />
	</div>';
	
	echo '<div class="divInPage">
		<div class="divInPageImage">
			<img src="./design/' . $userDesign . '/images/' . $language . '/files.png" />
		</div>
		<div class="divInPageText">
			' . $message['legend_nb_files'] . ' : ' . $filesAvailable . '<br />
			' . $message['legend_used_space'] . ' : ' . $folderSpaceUsed . '<br />
			' . $message['legend_free_space'] . ' : ' . $folderSpaceAvailable . '<br />
			' . $message['legend_files_max_size'] . ' : ' . $maxSizeAllowed . '<br />
			' . $message['legend_allowed_ext'] . ' : ' . $extAllowed . '<br />
		</div>
	</div>';
		
	if ($fileInfo != null)
		echo '<div class="divInPage" style="margin-top: 20px;">
			<div class="divInPageImage">
				<img src="./design/' . $userDesign . '/images/' . $language . '/' . $fileBigIcone . '" />
			</div>
			<div class="divInPageText">
				' . $fileName . '<br />
				' . $fileSize . '<br />
				' . $fileType . '<br />
				' . $fileTime . '<br />
				' . $fileLink . '<br />
				<a href="./index.php?user_files">' . $message['action_close'] . '</a>
			</div>
		</div>';
	
	if ($alert != null) {
		echo '<div style="border: 1px solid black; background-color: #888899; padding: 10px; margin-top: 10px;">
			'.$alert.'
		</div>';
	}
	
	echo '<div>
		<form method="post" action="./index.php?user_files" enctype="multipart/form-data">
			<p>
				' . $message['legend_send_file'] . '<br />
				<input type="file" name="filesend" />
				<input type="submit" value=' . $message['action_send_file'] . ' />
			</p>
		</form>
	</div>';
	
	// Affichage sous forme de liste
	if ($_SESSION[$sessionPage . 'view'] == 'list') {
		echo '<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">
			<form id="form" action="./index.php?user_files" method="post">
				<div style="text-align: left; padding-bottom: 10px">
					' . $message['legend_view_type'] . '<a href="index.php?user_files&amp;view=miniatures">' . $message['action_view_miniatures'] . '</a>
				</div>
				<div>
					<table cellspacing="0px" class="legendTable">
						<tr class="legendRow">
							<td class="legendCell" style="width: 500px;">
								' . $message['legend_name'] . ' 
								<a href="index.php?user_files&amp;order=name&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
								<a href="index.php?user_files&amp;order=name&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
							</td>
							<td class="legendCell" style="width: 80px;">
								' . $message['legend_size'] . ' 
								<a href="index.php?user_files&amp;order=size&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
								<a href="index.php?user_files&amp;order=size&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
							</td>
							<td class="legendCell">
								' . $message['legend_type'] . ' 
								<a href="index.php?user_files&amp;order=type&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
								<a href="index.php?user_files&amp;order=type&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
							</td>
						</tr>
					</table>
				</div>
				<div class="listDisplay">';
				if ($nbFiles == 0) {
					echo $message['message_empty_file']; // Affichage d'un message dans le cas ou le dossier est vide
				} else {
					echo '<table cellspacing="0px" class="listTable">';
					$i=0; // Raz du compteur
					foreach ($tableOrder as $key=>$val) {
						echo '<tr onclick="checkTheBox(form, ' . $i . ', 1);" class="listRowColor1">
							<td class="listCell" style="width: 500px">
								<input onclick="checkTheBox(form, ' . $i . ', 1);" id="table1_chk' . $i . '" type="checkbox" name="select' . $i . '" value="' . $tableName[$key] . '">';
								/*echo '<noscript>';
									if (isset($_GET['all']))
										echo '<input checked type="checkbox" name="select' . $i . '" value="' . $tableName[$key] . '">';
									else
										echo '<input type="checkbox" name="select' . $i . '" value="' . $tableName[$key] . '">';
								echo '</noscript>*/
								echo '<img src="./design/' . $userDesign . '/images/fr/icone_mini_' . $filesExtType[$tableType[$key]] . '.png">
								<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '">' . $tableName[$key] . '</a>
							</td>
							<td class="listCell" style="width: 80px">' . round($tableSize[$key]/1000) . 'Ko</td>
							<td class="listCell">' . $tableType[$key] . '</td>
						</tr>';
						$i++;
					}
					echo '</table>';
				}
				echo '</div>
				<div style="text-align: left; padding-top: 10px">
					<a onclick="checkAll(form, true, 1);">' . $message['action_select_all'] . '</a> / <a onclick="checkAll(form, false, 1);">' . $message['action_unselect_all'] . '</a><br />';
					/*<noscript>
						<a href="./index.php?user_files&amp;all">' . $message['action_select_all'] . '</a> / <a href="./index.php?user_files">' . $message['action_unselect_all'] . '</a><br />
					</noscript>*/
					echo '<input type="submit" name="remove" value="' . $message['action_remove_selection'] . '">
				</div>
			</form>
		</div>';
	}
	
	// Affichage sous forme de miniatures
	elseif ($_SESSION[$sessionPage . 'view'] == 'miniatures') {
		echo '<div id="list" style="border: 1px solid black;  background-color: #888899; padding: 10px; margin-top: 10px;" class="list">
			<form id="form" action="./index.php?user_files" method="post">
				<div style="text-align: left; padding-bottom: 10px">
					' . $message['legend_view_type'] . '<a href="index.php?user_files&amp;view=list">' . $message['action_view_list'] . '</a>
				</div>
				<div>
					<table cellspacing="0px" class="legendTable">
						<tr class="legendRow">
							<td class="legendCell" style="width: 500px;">
								' . $message['legend_name'] . ' 
								<a href="index.php?user_files&amp;order=name&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
								<a href="index.php?user_files&amp;order=name&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
							</td>
							<td class="legendCell" style="width: 80px;">
								' . $message['legend_size'] . ' 
								<a href="index.php?user_files&amp;order=size&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
								<a href="index.php?user_files&amp;order=size&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
							</td>
							<td class="legendCell">
								' . $message['legend_type'] . ' 
								<a href="index.php?user_files&amp;order=type&amp;sort=asc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/up.png" title="trier par ordre croissant" alt=""/></a>
								<a href="index.php?user_files&amp;order=type&amp;sort=desc"><img class="legendSortButton" src="./design/'.$userDesign.'/images/'.$language.'/down.png" title="trier par ordre décroissant" alt=""/></a>
							</td>
						</tr>
					</table>
				</div>
				<div class="listDisplay">';
				if ($nbFiles == 0) {
					echo $message['message_empty_file']; // Affichage d'un message dans le cas ou le dossier est vide
				} else {
					echo '<table cellspacing="0px" style="border: 0px none;">';
					$i=0; // Raz du compteur
					foreach ($tableOrder as $key=>$val) {
						echo '<div class="imageBox">
							<div class="imageInBox">
								<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '">';
								if ($filesExtType[$tableType[$key]] == 'image')
									echo '<img class="imageMini" src="' . $dir . '/' . $tableName[$key] . '" title="Afficher les caractéristiques" alt="" />';
								else
									echo '<img src="./design/'.$userDesign.'/images/fr/icone_mini_image.png">';
								echo '</a>
							</div>
							<div>
								' . cutString($tableName[$key], 16, 2) . '<br />
								<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '&amp;remove"><img class="imageButton" src="./design/' . $userDesign . '/editech/remove.png" title="' . $message['info_remove_file'] . '" alt="" /></a>
							</div>		
						</div>';
						$i++;
					}
					echo '</table>';
				}
				echo '</div>
			</form>
		</div>';
	}
}

//-----------------------------------------------------------------
// Affichage de la page de login
//-----------------------------------------------------------------

else
{
	include_once('./php/login.php');
}







?>