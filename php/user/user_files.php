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

// Si le membre est bien loggé
if (isset($_SESSION['login']) && isset($_SESSION['password'])) {
	
	// Définition du chemin du dossier
	$dir = 'web/users/' . $userId . '/files';
	
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
				// si le nom existe on ajout un chiffre et on test en boucle !
				$newName = substr($fileName, 0, 40) . '.' . $fileExt;
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
				if (isset($_POST['checkbox' . $i])) {
					if (in_array($_POST['checkbox' . $i], $files)) {
						$listeArray[] = $_POST['checkbox' . $i];
						if (unlink($dir . '/' . $_POST['checkbox' . $i])) {
							$alert = $alert . "Le fichier " . $_POST['checkbox' . $i] . " à bien été supprimé.<br />";
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
				if (isset($_POST['checkbox' . $i])) {
					$nbFilesRemove++;
				}
			}
			if ($nbFilesRemove != 0)
			{
				$alert = '<form action="./index.php?user_files" method="post">';
				if ($nbFilesRemove > 1)	$alert = $alert . $message['remove_following_files'] . '<br />';
				else $alert = $alert . $message['remove_following_file'] . '<br />';
				for ($i=0; $i<$nbFiles; $i++) {
					if (isset($_POST['checkbox' . $i])) {
						$alert = $alert . '<input type="hidden" name="checkbox' . $i . '" value="' . $_POST['checkbox' . $i] . '" />';
						$alert = $alert . $_POST['checkbox' . $i] . '<br />';
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
		//$fileLink		= $message['legend_file_link'] . ' : <a href="./web/users/' .$userId. '/files/' . $_GET['id'] . '">' . $message['action_file_download'] . '</a>';
		$pathParts		= pathinfo($file);
		$fileExt		= strtolower($pathParts['extension']);
		$fileType		= $message['legend_file_type'] . ' : ' . $filesExtDef[$fileExt];
		$fileBigIcon	= ($filesExtType[$fileExt] == 'image') ? '<img class="iconBig" src="' . $file . '" title="' . $fileName . '" alt="" />' : '<img class="iconBig" src="' . $userDesignPath . '/icon_big_' . $filesExtType[$fileExt] . '.png" alt=""';
		// lien direct
		$fileDownload	= $message['legend_file_link'] . ' : <a href="./web/users/' .$userId. '/files/' . $_GET['id'] . '">' . $message['action_file_download'] . '</a>';
		$fileRemove		= '<a href="./index.php?user_files&amp;id=' . $_GET['id'] . '&amp;remove">' . $message['action_file_remove'] . '</a>';
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
	
	// Affichage du Linker
	echo '<div class="pageLinker">
		<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a> > 
		<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a> > 
		' . $message['navigation_tree_user_files'] . '
	</div>';
	
	// Affichage des informations sur le dossier
	echo '<div class="pageFolderInfo">
		<div class="pageImage">
			<img src="' . $userDesignPath . '/files.png" alt="" />
		</div>
		<div class="pageText">
			' . $message['legend_nb_files'] . ' : ' . $filesAvailable . '<br />
			' . $message['legend_used_space'] . ' : ' . $folderSpaceUsed . '<br />
			' . $message['legend_free_space'] . ' : ' . $folderSpaceAvailable . '<br />
			' . $message['legend_files_max_size'] . ' : ' . $maxSizeAllowed . '<br />
			' . $message['legend_allowed_ext'] . ' : ' . $extAllowed . '<br />
		</div>
	</div>';
	
	// Affichage des informations du le fichier
	if ($fileInfo != null)
		echo '<div class="pageFileInfo"">
			<div class="pageImage">
				' . $fileBigIcon . '
			</div>
			<div class="pageText">
				' . $fileName . '<br />
				' . $fileSize . '<br />
				' . $fileType . '<br />
				' . $fileTime . '<br />
				' . $fileDownload . ' / '.$fileRemove . '<br />
				<a href="./index.php?user_files">' . $message['action_close'] . '</a>
			</div>
		</div>';
	
	// Affichage des alertes
	if ($alert != null) {
		echo '<div class="pageAlert">
			'.$alert.'
		</div>';
	}
	
	// Affichage du formulaire d'envoi
	echo '<div>
		<form method="post" action="./index.php?user_files" enctype="multipart/form-data">
			<p>
				' . $message['legend_send_file'] . '<br />
				<input type="file" name="filesend" />
				<input type="submit" value="' . $message['action_send_file'] . '" />
			</p>
		</form>
	</div>';
	
	// Affichage sous forme de liste
	if ($_SESSION[$sessionPage . 'view'] == 'list') {
		echo '<div class="pageList">
			<form class="pageListForm" action="./index.php?user_files" method="post">
				<div style="text-align: left; padding-bottom: 10px; margin-top: 0px; padding-top: 0px;">
					' . $message['legend_view_type'] . '<a href="index.php?user_files&amp;view=miniatures">' . $message['action_view_miniatures'] . '</a>
				</div>
				<div>
					<table cellspacing="0px" class="pageListLegendTable">
						<tr class="pageListLegendRow">
							<td class="pageListLegendCell" style="width: 500px;">
								' . $message['legend_name'] . ' 
								<a href="index.php?user_files&amp;order=name&amp;sort=asc"><img class="legendSortButton" src="' . $userDesignPath . '/up.png" title="' . $message['info_order_up'] . '" alt="" /></a>
								<a href="index.php?user_files&amp;order=name&amp;sort=desc"><img class="legendSortButton" src="' . $userDesignPath . '/down.png" title="' . $message['info_order_down'] . '" alt="" /></a>
							</td>
							<td class="pageListLegendCell" style="width: 80px;">
								' . $message['legend_size'] . ' 
								<a href="index.php?user_files&amp;order=size&amp;sort=asc"><img class="legendSortButton" src="' . $userDesignPath . '/up.png" title="' . $message['info_order_up'] . '" alt="" /></a>
								<a href="index.php?user_files&amp;order=size&amp;sort=desc"><img class="legendSortButton" src="' . $userDesignPath . '/down.png" title="' . $message['info_order_down'] . '" alt="" /></a>
							</td>
							<td class="pageListLegendCell">
								' . $message['legend_type'] . ' 
								<a href="index.php?user_files&amp;order=type&amp;sort=asc"><img class="legendSortButton" src="' . $userDesignPath . '/up.png" title="' . $message['info_order_up'] . '" alt="" /></a>
								<a href="index.php?user_files&amp;order=type&amp;sort=desc"><img class="legendSortButton" src="' . $userDesignPath . '/down.png" title="' . $message['info_order_down'] . '" alt="" /></a>
							</td>
						</tr>
					</table>
				</div>
				<div class="pageListDisplay">';
				if ($nbFiles == 0) {
					echo $message['message_empty_file']; // Affichage d'un message dans le cas ou le dossier est vide
				} else {
					echo '<table cellspacing="0px" class="pageListTable">';
					$i=0; // Raz du compteur
					foreach ($tableOrder as $key=>$val) {
						echo '<tr class="pageListRowColor1" onclick="checkTheBox(\'table1_chk\', ' . $i . ');">
							<td class="pageListCell" style="width: 500px" id="table1_chk' . $i . '">
								<input type="checkbox" name="checkbox' . $i . '" id="checkbox' . $i . '" value="' . $tableName[$key] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . ');" />';
								echo '<img src="' . $userDesignPath . '/icon_mini_' . $filesExtType[$tableType[$key]] . '.png" alt="" />
								<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '">' . cutString($tableName[$key], 53, 1) . '</a>
							</td>
							<td class="pageListCell" style="width: 80px">' . round($tableSize[$key]/1000) . 'Ko</td>
							<td class="pageListCell">' . $tableType[$key] . '</td>
						</tr>';
						$i++;
					}
					echo '</table>';
				}
				echo '</div>
				<div style="text-align: left; padding-top: 10px">
					<a onclick="actionCheckbox(\'table1_chk\',' . $i . ', \'1\');">' . $message['action_select_all'] . '</a> / 
					<a onclick="actionCheckbox(\'table1_chk\',' . $i . ', \'0\');">' . $message['action_unselect_all'] . '</a> / 
					<a onclick="actionCheckbox(\'table1_chk\',' . $i . ', \'2\');">' . $message['action_inverse_selection'] . '</a><br />';
					echo '<input type="submit" name="remove" value="' . $message['action_remove_selection'] . '" />
				</div>
			</form>
		</div>';
	}
	
	// Affichage sous forme de miniatures
	elseif ($_SESSION[$sessionPage . 'view'] == 'miniatures') {
		echo '<div id="list" class="pageList">
			<form id="form" class="pageListForm" action="./index.php?user_files" method="post">
				<div style="text-align: left; padding-bottom: 10px">
					' . $message['legend_view_type'] . '<a href="index.php?user_files&amp;view=list">' . $message['action_view_list'] . '</a>
				</div>
				<div>
					<table cellspacing="0px" class="pageListLegendTable">
						<tr class="pageListLegendRow">
							<td class="pageListLegendCell" style="width: 500px;">
								' . $message['legend_name'] . ' 
								<a href="index.php?user_files&amp;order=name&amp;sort=asc"><img class="legendSortButton" src="' . $userDesignPath . '/up.png" title="trier par ordre croissant" alt="" /></a>
								<a href="index.php?user_files&amp;order=name&amp;sort=desc"><img class="legendSortButton" src="' . $userDesignPath . '/down.png" title="trier par ordre décroissant" alt="" /></a>
								 | ' . $message['legend_size'] . ' 
								<a href="index.php?user_files&amp;order=size&amp;sort=asc"><img class="legendSortButton" src="' . $userDesignPath . '/up.png" title="trier par ordre croissant" alt="" /></a>
								<a href="index.php?user_files&amp;order=size&amp;sort=desc"><img class="legendSortButton" src="' . $userDesignPath . '/down.png" title="trier par ordre décroissant" alt="" /></a>
								 | ' . $message['legend_type'] . ' 
								<a href="index.php?user_files&amp;order=type&amp;sort=asc"><img class="legendSortButton" src="' . $userDesignPath . '/up.png" title="trier par ordre croissant" alt="" /></a>
								<a href="index.php?user_files&amp;order=type&amp;sort=desc"><img class="legendSortButton" src="' . $userDesignPath . '/down.png" title="trier par ordre décroissant" alt="" /></a>
							</td>
						</tr>
					</table>
				</div>
				<div class="pageListDisplay">';
				if ($nbFiles == 0) {
					echo $message['message_empty_file']; // Affichage d'un message dans le cas ou le dossier est vide
				} else {
					echo '<table cellspacing="0px" style="border: 0px none;">';
					$i=0; // Raz du compteur
					foreach ($tableOrder as $key=>$val) {
						$file			= $dir . '/' . $tableName[$key];
						$fileName 		= $tableName[$key];
						$fileTimestamp 	= filemtime($file);
						$fileTime		= date('d\/m\/Y \- h\:m\:s \G\m\t' ,$fileTimestamp);
						$fileSize		= round(filesize($file)/1000) . 'Ko';
						$fileLink		= $domainName . '/web/users/' .$userId. '/files/' . $tableName[$key];
						$pathParts		= pathinfo($file);
						$fileExt		= strtolower($pathParts['extension']);
						$fileType		= $filesExtType[$fileExt];
						$fileBigIcon	= 'icon_big_' . $filesExtType[$fileExt] . '.png';
						echo '<div class="pageListIconDivFrame">
							<a href="./index.php?user_files&amp;id=' . $tableName[$key] . '" class="pageListIconInfo">
								<div class="pageListIconDivIcon">
									<img class="pageListIconNormal" src="' . $userDesignPath . '/icon_normal_' . $filesExtType[$tableType[$key]] . '.png" alt="" />
									<div class="pageListIconNormalDivText">
										' . cutString($tableName[$key], 12, 1) . '
									</div>	
								</div>
								<div class="pageListIconInfoBack">
								</div>
								<div class="pageListIconInfoFrame">
									<span class="pageListIconInfoText">
										<strong>'. $message['legend_file_name'] . '</strong><br />
										' . cutString($fileName, 24, 3) . '<br />
										<strong>' . $message['legend_file_size'] . '</strong><br />
										' . $fileSize . '<br />
										<strong>' . $message['legend_file_type'] . '</strong><br />
										fichier .' . $fileExt. '<br />
										<strong>' . $message['legend_file_date'] . '</strong><br />
										' . $fileTime . '<br />
									</span>
									<span class="pageListIconInfoImage">';
										if ($filesExtType[$fileExt] == "image")
											echo '<img style="max-width: 160px; max-height: 160px;" src="' . $dir . '/' . $fileName . '" />';
										else
											echo '<img src="' . $userDesignPath . '/' . $fileBigIcon . '" />';
									echo '</span>
								</div>
							</a>
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