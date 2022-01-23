<?php

//*****************************************************************
//	Nobelios V2.0 beta
//	Galerie d'images Editech
//	Script par Geoffrey HAUTECOUVERTURE
//	Toute reproduction totale ou partielle interdite
//	Aout - 2009
//*****************************************************************

//-----------------------------------------------------------------
// Galerie de fichiers de l'utilisateur
//-----------------------------------------------------------------

// On test si le membre est bien loggé
if (isset($_SESSION['login']) && isset($_SESSION['password']) && isset($_GET['path'])) {
	
	// On test si la page et le fichier properties.txt (contenant l'identifiant de l'auteur) existent
	if (file_exists('./..' . $_GET['path']) && in_array('properties.txt', $pageFolder = testDir($pageDir = './..' . $_GET['path'] . '/..'))) {
		
		$properties		= fopen($pageDir.'/properties.txt', 'r+');									// Ouverture du fichier properties.txt
		$getProperties	= fgets($properties);														// Lecture du fichier properties.txt
		$author			= preg_replace('#.*\[author\](.+)\[/author\].*#i', '$1', $getProperties); 	// Récupération de l'id de l'auteur
		$title			= preg_replace('#.*\[title\](.+)\[/title\].*#i', '$1', $getProperties);		// Récupération du titre de la page
		
		// Test si l'utilisateur est l'auteur de la page ou si opérateur système (tout droits accordés)
		if (($author == $userId) || ($level == 4)) {
			
			$dir = './../' . $_GET['path'];		// Définition du chemin du dossier
			$folderSize = folderSize($dir);		// Taille du dossier
			$alert = null;						// Aucune alerte par défaut
			$fileInfo = null;					// Aucune information par défaut
			
			//------------------------------------------------------------------
			// Définition de l'affichage
			//------------------------------------------------------------------
			
			// Suffix pour les sessions
			$sessionPage = 'editech_images_';
			
			// Options de classement par défaut pour les fichiers utilisateur (enregistrement en session)
			if (!isset($_SESSION[$sessionPage . 'view'])) $_SESSION[$sessionPage . 'view'] = 'list';	// Affichage par défaut (list)
			if (!isset($_SESSION[$sessionPage . 'order'])) $_SESSION[$sessionPage . 'order'] = 'name';	// Organisation selon une donnée par défaut (name)
			if (!isset($_SESSION[$sessionPage . 'sort'])) $_SESSION[$sessionPage . 'sort'] = 'asc';		// Organisation selon un ordre par défaut (asc)
			
			// Options de classement pour les fichiers utilisateur (enregistrement en session)
			if (isset($_GET['view']) && (($_GET['view'] == 'list') || ($_GET['view'] == 'miniatures'))) $_SESSION[$sessionPage . 'view'] = $_GET['view'];								// Affichage utilisé (liste, icones, etc)
			if (isset($_GET['order']) && (($_GET['order'] == 'name') || ($_GET['order'] == 'size') || ($_GET['order'] == 'type'))) $_SESSION[$sessionPage . 'order'] = $_GET['order']; 	// Organiser selon une donnée (titre, taille, etc)
			if (isset($_GET['sort']) && (($_GET['sort'] == 'desc') || ($_GET['sort'] == 'asc'))) $_SESSION[$sessionPage . 'sort'] = $_GET['sort'];										// Organiser selon un ordre (croissant ou descroissant)
			
			//-----------------------------------------------------------------
			// On ajoute un fichier
			//-----------------------------------------------------------------
			
			// Test si un fichier a été envoyé
			if (isset($_FILES['filesend'])) {
			
				if ($_FILES['filesend']['error'] == 0){
				
					$pathParts 	= pathinfo($_FILES['filesend']['name']);									// Récupération des informations sur le fichier
					$fileExt 	= strtolower($pathParts['extension']);										// Récupération de l'extension et passage en minuscules
					$fileName 	= substr($pathParts['basename'],0 , strrpos($pathParts['basename'], '.'));	// Récupération du nom (découpage de la position 0 au début de l'extension)
					$imageSize 	= getimagesize($_FILES['filesend']['tmp_name']);							// Récupération de la taille de l'image dans un tableau ([0]=largeur, [1]=hauteur)
					
					// Detection des erreurs et définition des alertes (pour l'upload)
					$alert .= (($folderSize + $_FILES['filesend']['size']) <= $imageFolderMax)				? '' : $message['alert_upload_err_disk_space'] . '<br />'; 	// Espace disque insuffisant
					$alert .= ($_FILES['filesend']['size'] <= $imageMaxSize)								? '' : $message['alert_upload_err_file_size'] . '<br />';	// Fichier trop volumineux
					$alert .= (in_array($fileExt, $allowedImgExt))											? '' : $message['alert_upload_err_file_ext'] . '<br />';	// Extension non autorisée
					$alert .= (($imageSize[0] <= $imageMaxWidth) && ($imageSize[1] <= $imageMaxHeight))		? '' : $message['alert_upload_err_img_size'] . '<br />';	// Dimensions de l'image non-autorisées
					
					// Enregistrement du fichier
					if ($alert == null) {
						
						/* CODE DESACTIVE, ce code permet d'éviter l'écrasement des fichiers présent avec un nom identique au fichier uploadé
						// Générateur de nom pour fichier
						do {
							$newName = randomHex();	// Appel du générateur de chaine hexadécimale (fonction.php)
							$reqName = in_array($newName . '.' . $fileExt, testdir($dir));
						} while ($reqName == true);
						*/
						
						// si le nom existe on ajout un chiffre et on test en boucle !
						$newName = substr($fileName, 0, 40) . '.' . $fileExt;
						// Test si la copie est réussie
						$alert .= (move_uploaded_file($_FILES['filesend']['tmp_name'], $dir . '/' . $newName) == 1) ? $message['alert_upload_succeded'] . '<br />' : $message['alert_upload_err_copy'] . '<br />';
					} else {
						$alert = $message['alert_upload_err_failure'] . '<br />' . $alert;
					}			
				} else {
					$alert .= $message['alert_upload_err_failure'] . '<br />';
				}
				// Detection des erreurs et définition des alertes
				$alert .= (($_FILES['filesend']['error']) == 1) ? $message['alert_upload_err_ini_size'] . '<br />' 	: '';			// UPLOAD_ERR_INI_SIZE (taille du fichier suppérieure à la limite de php.ini)
				$alert .= (($_FILES['filesend']['error']) == 2) ? $message['alert_upload_err_form_size'] . '<br />' : '';			// UPLOAD_ERR_FORM_SIZE (taille du fichier suppérieure à la limite du formulaire)
				$alert .= (($_FILES['filesend']['error']) == 3) ? $message['alert_upload_err_partial'] . '<br />'	: ''; 			// UPLOAD_ERR_PARTIAL (téléchargement incomplet)
				$alert .= (($_FILES['filesend']['error']) == 4) ? $message['alert_upload_err_no_file'] . '<br />'	: ''; 			// UPLOAD_ERR_NO_FILE (aucun fichier envoyé)
				// Affichage d'un lien de fermeture des alertes
				$alert .= '<div class="pageButton">
					<a href="./index.php?images&amp;path=' . $_GET['path'] . '">' . $message['action_close']  . '<br /></a>
				</div>'; 
			}

			//-----------------------------------------------------------------
			// On veut supprimer un ou plusieurs fichiers 
			// ou afficher les informations d'un fichier
			//-----------------------------------------------------------------
			
			// Liste des fichiers 
			$files = testDir($dir);		// Lecture du dossier (définit dans fonction.php)
			$nbFiles = nbFolder($dir);	// Compter un nombre de dossiers sans les sous-dossiers (définit dans fonction.php)
			
			// On demande la suppression d'un fichier unique (affichage en miniatures)
			if (isset($_GET['remove']) && isset($_GET['id'])) {
				// On test si le fichier existe
				if (isset($_GET['id']) && in_array($_GET['id'], testdir($dir))) {	
					// On test si il a confirmation de la suppression
					if (isset($_GET['remove']) && isset($_GET['confirm'])) {
						// On supprimer le fichier et on retourne une alerte
						if (unlink($dir . '/' . $_GET['id'])) {
							$alert .= $message['alert_remove_success'] . ' : ' . $_GET['id'] . '<br />
							<div class="pageButton">
								<a href="./index.php?images&amp;path=' . $_GET['path'] . '">' . $message['action_close']  . '<br /></a>
							</div>'; 
						} else {
							$alert .= $message['alert_remove_failure'] . ' : ' . $_GET['id'] . '<br />';
						}	
					} elseif (isset($_GET['remove'])) {
						$alert .= $message['remove_following_file'] . '<br />
						' . $_GET['id'] . '<br />
						<div class="pageButton">
							<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;id=' . $_GET['id'] . '&amp;remove&amp;confirm">' . $message ['action_confirm_yes'] . '</a>
							<a href="./index.php?images&amp;path=' . $_GET['path'] . '">' . $message['action_confirm_no'] . '</a>
						</div>';
					}
				}
			}
			
			// On demande la suppression de un ou plusieurs fichiers (affichage en liste)
			elseif (isset($_POST['remove'])) {
				// On test si il a confirmation de la suppression
				if (isset($_POST['confirm'])) {
					// On liste chaque fichier
					for ($i=0; $i<$nbFiles; $i++) {
						// On test si le fichier est à supprimé (définit par les cases à cocher)
						if (isset($_POST['checkbox' . $i])) {
							// On test si le fichier existe
							if (in_array($_POST['checkbox' . $i], $files)) {
								// On supprime le fichier et on retourne une alerte
								if (unlink($dir . '/' . $_POST['checkbox' . $i])) {
									$alert .= $message['alert_remove_success'] . ' : ' . $_POST['checkbox' . $i] . '<br />';
								} else {
									$alert .= $message['alert_remove_failure'] . ' : ' . $_POST['checkbox' . $i] . '<br />';
								}
							}
						}
					}
					// Affichage d'un lien de fermeture des alertes
					$alert .= '<div class="pageButton">
						<a href="./index.php?images&amp;path=' . $_GET['path'] . '">' . $message['action_close']  . '<br /></a>
					</div>'; 
				} else {
					// On détermine le nombre de fichiers à effacer
					$nbFilesRemove = 0;	// Initialisation du compteur d'effacement
					for ($i=0; $i<$nbFiles; $i++) {
						if (isset($_POST['checkbox' . $i])) {
							$nbFilesRemove++;	// Incrémentation du compteur
						}
					}
					// On test si on doit effacer un ou plusieurs fichiers
					if ($nbFilesRemove != 0) {
						$alert .= '<form action="./index.php?images&amp;path=' . $_GET['path'] . '" method="post">';
						if ($nbFilesRemove > 1)	$alert = $alert . $message['remove_following_files'] . '<br />'; 	// Si on efface plusieurs fichiers on affiche un message au pluriel
						else $alert = $alert . $message['remove_following_file'] . '<br />';						// Sinon on affiche un message au singulier
						// On affiche le nom des fichiers à effacer dans une boite d'alerte
						for ($i=0; $i<$nbFiles; $i++) {
							if (isset($_POST['checkbox' . $i])) {
								$alert .= '<input type="hidden" name="checkbox' . $i . '" value="' . $_POST['checkbox' . $i] . '" />'; 	// Création d'un champ de formulaire caché
								$alert .= $_POST['checkbox' . $i] . '<br />';															// Affichage du nom du fichier
							}
						}
						$alert .= '<input type="hidden" name="confirm">
						<div class="pageButton">
							<button type="submit" name="remove">
								' . $message['action_confirm_yes'] . '
							</button>
							<a href="./index.php?images&amp;path=' . $_GET['path'] . '">' . $message['action_confirm_no'] . '</a>
						</div>
						</form>';
					}
				}
			}
			
			//-----------------------------------------------------------------
			// Affichage les informations d'un fichier
			//-----------------------------------------------------------------
			
			// Affichage des informations du fichier selectionné
			elseif (isset($_GET['id']) && in_array($_GET['id'], $files)) {
				$file				= $dir . '/' . $_GET['id'];																																																		// On détermine le chemin du fichier
				$fileName 			= $_GET['id'];																																																					// Nom du fichier
				$fileTimestamp 		= filemtime($file);																																																				// Timestamp du fichier
				$fileTime			= date('d\/m\/Y \- h\:m\:s \G\m\t' ,$fileTimestamp);																																											// Date et heure de la dernière modification du fichier
				$fileSize			= round(filesize($file)/1000) . 'Ko';																																															// Taille du fichier
				$pathParts			= pathinfo($file);																																																				// Initialisation des informations sur le nom du fichier
				$fileExt			= strtolower($pathParts['extension']);																																															// Extension du fichier
				$fileType			= $filesExtDef[$fileExt];																																																		// Attribution d'un nom pour d'extension du fichier (wmv = windows media video)
				$fileBigIcon		= ($filesExtType[$fileExt] == 'image') ? '<img src="' . $file . '" title="' . $fileName . '" alt="" />' : '<img class="iconBig" src="./../' . $userDesignPath . '/images/icon_big_' . $filesExtType[$fileExt] . '.png" alt=""';	// Miniature si il s'agit d'une image (selon $filesExtType[extension]) ou d'une icone
				$fileInsert			= '<a href="#" onclick="opener.insertTag(\'&lt;image url=&quot;' . $_GET['path'] . '/' . $_GET['id'] . '&quot; title=&quot;&quot;&gt;\',\'\',\'textarea\',\'images\')">' . $message['action_file_insert'] . '</a>';
				$fileInsertMini		= '<a href="#" onclick="opener.insertTag(\'&lt;imagemini url=&quot;' . $_GET['path'] . '/' . $_GET['id'] . '&quot; title=&quot;&quot;&gt;\',\'\',\'textarea\',\'images\')">' . $message['action_file_insert_mini'] . '</a>';
				$fileDownload		= '<a href="./../' . $_GET['path'] . '/' . $_GET['id'] . '">' . $message['action_file_download'] . '</a>';																														// Téléchargement du fichier
				$fileRemove			= '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;id=' . $_GET['id'] . '&amp;remove">' . $message['action_file_remove'] . '</a>';																				// Effacement du fichier
				$imageSize 			= getimagesize($file);																																																			// Définition d'un tableau avec les dimentions de l'image
				$imageSizeWidth		= $imageSize[0];
				$imageSizeHeight	= $imageSize[1];
				$fileInfo			= true;
			}
			
			//-----------------------------------------------------------------
			// Classement des fichiers
			//-----------------------------------------------------------------
			
			// Mise à jour des information sur le fichiers
			$files = testDir($dir);		// Lecture du dossier (définit dans fonction.php)
			$nbFiles = nbFolder($dir);	// Compter un nombre de dossiers sans les sous-dossiers (définit dans fonction.php)
			
			// Déclaration des tableaux de classement
			$tableName = array();
			$tableSize = array();
			$tableType = array();
			
			// Boucle de liste des fichiers pour trier les données
			for ($i=0; $i<$nbFiles; $i++) {
				$file = $dir . '/' . $files[$i]; 					// On détermine le chemin du fichier
				$size = filesize($file);							// Taille du fichier
				$pathParts = pathinfo($file);						// Initialisation des informations sur le nom du fichier
				$fileExt = strtolower($pathParts['extension']);		// Passage de l'extension en minature
				$tableName[$i] = $files[$i];						// Enregistrement du nom des fichiers en tableau
				$tableSize[$i] = $size;								// Enregistrement de la taille des fichiers en tableau
				$tableType[$i] = $fileExt;							// Enregistrement de l'extension des fichiers en tableau
			}
			
			// On créer un tableau pour englober les tableaux de classement
			$tableSort = array(
				'name' => $tableName,
				'size' => $tableSize,
				'type' => $tableType
			);
			
			// On procède à un tri sur le type de données	
			$tableOrder = $tableSort[$_SESSION[$sessionPage . 'order']];
			
			// On procède à un tri alphanumérique
			if ($_SESSION[$sessionPage . 'sort'] == 'desc') arsort($tableOrder);
			else asort($tableOrder);
			
			//-----------------------------------------------------------------
			// Informations
			//-----------------------------------------------------------------
			
			// Liste des extensions autorisées
			$extAllowed = '';
			for ($i=0; $i<count($allowedImgExt); $i++) {
				$extAllowed = $extAllowed.$allowedImgExt[$i];
				$extAllowed = ($i == (count($allowedImgExt) - 1)) ? $extAllowed : $extAllowed . ' / '; // Ajoute de "/" entre les extentions autorisées
			}
			
			$folderSize 			= folderSize($dir);																		// Mise à jour de la taille du dossier images		
			$maxSizeAllowed 		= $imageMaxSize/1000 . ' Ko';															// Taille maximale par fichier
			$filesAvailable 		= nbFolder($dir);																		// Nombre de fichiers disponibles
			$folderSpaceUsed 		= round($folderSize/1000) . " Ko / ".round($imageFolderMax/1000);						// Espace utilisé / espace total
			$folderSpaceAvailable 	= round(($imageFolderMax-$folderSize)/1000) . " Ko / " . round($imageFolderMax/1000);	// Espace disponible / espace total
			
			//-----------------------------------------------------------------
			// Partie Affichage
			//-----------------------------------------------------------------
			
			echo $message['message_editech_images_title'] . ' : ' . $title;
			
			// Affichage des informations du dossier
			echo '<div class="pageFolderInfo">
				<div class="pageImage">
					<img src="./../' . $userDesignPath . '/images/images.png" alt="" />
				</div>
				<div class="pageText">
					' . $message['legend_nb_files'] . ' : ' . $filesAvailable . '<br />
					' . $message['legend_used_space'] . ' : ' . $folderSpaceUsed . '<br />
					' . $message['legend_free_space'] . ' : ' . $folderSpaceAvailable . '<br />
					' . $message['legend_files_max_size'] . ' : ' . $maxSizeAllowed . '<br />
					' . $message['legend_allowed_ext'] . ' : ' . $extAllowed . '<br />
				</div>
			</div>';
			
			// Affichage des informations du fichier
			if ($fileInfo != null)
				echo '<div class="pageFileInfo"">
					<div>
						' . $fileBigIcon . '
					</div>
					<div class="pageText">
						' . $message['legend_file_name'] . ' : ' . $fileName . '<br />
						' . $message['legend_file_size'] . ' : ' . $fileSize . '<br />
						' . $message['legend_file_image_width'] . ' : ' . $imageSizeWidth . ' ' . $message['legend_pixels'] . '<br />
						' . $message['legend_file_image_height'] . ' : ' . $imageSizeHeight . ' ' . $message['legend_pixels'] . '<br />
						' . $message['legend_file_type'] . ' : ' . $fileType . '<br />
						' . $message['legend_file_date'] . ' : ' . $fileTime . '<br />
						' . $message['legend_file_link'] . ' : ' . $fileInsert . ' / ' . $fileInsertMini . ' / ' . $fileDownload . ' / ' . $fileRemove . '<br />
						<div class="pageButton">
							<a href="./index.php?images&amp;path=' . $_GET['path'] . '">' . $message['action_close']  . '<br /></a>
						</div>
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
				<form method="post" action="./index.php?images&amp;path=' . $_GET['path'] . '" enctype="multipart/form-data">
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
					<form class="pageListForm" action="./index.php?images&amp;path=' . $_GET['path'] . '" method="post">
						<div style="text-align: left; padding-bottom: 10px; margin-top: 0px; padding-top: 0px;">
							' . $message['legend_view_type'] . '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;view=miniatures">' . $message['action_view_miniatures'] . '</a>
						</div>
						<div>
							<table cellspacing="0px" class="pageListLegendTable">
								<tr class="pageListLegendRow">
									<td class="pageListLegendCell" style="width: 500px;">';
										if ($_SESSION[$sessionPage . 'order'] == 'name') {
											if ($_SESSION[$sessionPage . 'sort'] == 'desc')
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=asc">' . $message['legend_name'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
											else
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=desc">' . $message['legend_name'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
										} else {
											echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=asc">' . $message['legend_name'] . '</a>';
										}
									echo '</td>
									<td class="pageListLegendCell" style="width: 80px;">';
										if ($_SESSION[$sessionPage . 'order'] == 'size') {
											if ($_SESSION[$sessionPage . 'sort'] == 'desc')
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=size&amp;sort=asc">' . $message['legend_size'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
											else
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=size&amp;sort=desc">' . $message['legend_size'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
										} else {
											echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=size&amp;sort=asc">' . $message['legend_size'] . '</a>';
										}
									echo '</td>
									<td class="pageListLegendCell">';
										if ($_SESSION[$sessionPage . 'order'] == 'type') {
											if ($_SESSION[$sessionPage . 'sort'] == 'desc')
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=type&amp;sort=asc">' . $message['legend_type'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
											else
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=type&amp;sort=desc">' . $message['legend_type'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
										} else {
											echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=type&amp;sort=asc">' . $message['legend_type'] . '</a>';
										}
									echo '</td>
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
										<input type="checkbox" name="checkbox' . $i . '" id="checkbox' . $i . '" value="' . $tableName[$key] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . ');" />
										<img src="./../' . $userDesignPath . '/images/icon_mini_' . $filesExtType[$tableType[$key]] . '.png" alt="" />
										<img class="editechButtonImg" src="./../' . $userDesignPath . '/editech/' . $language . '/insert.png" title="' . $message['info_insert'] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . '); opener.insertTag(\'&lt;image url=&quot;' . $_GET['path'] . '/' . $tableName[$key] . '&quot; title=&quot;&quot;&gt;\',\'\',\'textarea\',\'images\')" alt="" />
										<img class="editechButtonImg" src="./../' . $userDesignPath . '/editech/' . $language . '/insertmini.png" title="' . $message['info_insert_mini'] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . '); opener.insertTag(\'&lt;imagemini url=&quot;' . $_GET['path'] . '/' . $tableName[$key] . '&quot; title=&quot;&quot;&gt;\',\'\',\'textarea\',\'images\')" alt="" />
										<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;id=' . $tableName[$key] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . ');">' . cutString($tableName[$key], 53, 1) . '</a>
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
							echo '<div class="pageButton">
								<button type="submit" class="formButton" name="remove">
									' . $message['action_remove_selection'] . '
								</button>
							</div>
						</div>
					</form>
				</div>';
			}
			
			// Affichage sous forme de miniatures
			elseif ($_SESSION[$sessionPage . 'view'] == 'miniatures') {
				echo '<div id="list" class="pageList">
					<form id="form" class="pageListForm" action="./index.php?images&amp;path=' . $_GET['path'] . '" method="post">
						<div style="text-align: left; padding-bottom: 10px">
							' . $message['legend_view_type'] . '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;view=list">' . $message['action_view_list'] . '</a>
						</div>
						<div>
							<table cellspacing="0px" class="pageListLegendTable">
								<tr class="pageListLegendRow">
									<td class="pageListLegendCell">';
										if ($_SESSION[$sessionPage . 'order'] == 'name') {
											if ($_SESSION[$sessionPage . 'sort'] == 'desc')
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=asc">' . $message['legend_name'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
											else
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=desc">' . $message['legend_name'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
										} else {
											echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=asc">' . $message['legend_name'] . '</a>';
										}
										echo ' | ';
										if ($_SESSION[$sessionPage . 'order'] == 'size') {
											if ($_SESSION[$sessionPage . 'sort'] == 'desc')
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=size&amp;sort=asc">' . $message['legend_size'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
											else
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=size&amp;sort=desc">' . $message['legend_size'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
										} else {
											echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=size&amp;sort=asc">' . $message['legend_size'] . '</a>';
										}
										echo ' | ';
										if ($_SESSION[$sessionPage . 'order'] == 'type') {
											if ($_SESSION[$sessionPage . 'sort'] == 'desc')
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=type&amp;sort=asc">' . $message['legend_type'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
											else
												echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=type&amp;sort=desc">' . $message['legend_type'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
										} else {
											echo '<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;order=type&amp;sort=asc">' . $message['legend_type'] . '</a>';
										}
									echo '</td>
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
								$fileTimestamp 	= filemtime($file);
								$pathParts		= pathinfo($file);
								$fileBigIcon 	= '<img class="iconBig" src="' . $file . '" title="' . $tableName[$key] . '" alt="" />';
								echo '<div class="pageListMiniDivFrame">
									<a href="./index.php?images&amp;path=' . $_GET['path'] . '&amp;id=' . $tableName[$key] . '">
										<div class="pageListMiniDivIcon">
											' . $fileBigIcon . '
										</div>
										<div class="pageListIconNormalDivText">
												' . cutString($tableName[$key], 15, 1) . '
										</div>
									</a>
									<img class="editechButtonImg" src="./../' . $userDesignPath . '/editech/' . $language . '/insert.png" title="' . $message['info_insert'] . '" onclick="opener.insertTag(\'&lt;image url=&quot;' . $_GET['path'] . '/' . $tableName[$key] . '&quot; title=&quot;&quot;&gt;\',\'\',\'textarea\',\'images\')" alt="" />
									<img class="editechButtonImg" src="./../' . $userDesignPath . '/editech/' . $language . '/insertmini.png" title="' . $message['info_insert_mini'] . '" onclick="opener.insertTag(\'&lt;imagemini url=&quot;' . $_GET['path'] . '/' . $tableName[$key] . '&quot; title=&quot;&quot;&gt;\',\'\',\'textarea\',\'images\')" alt="" />
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
		else
		{
			include_once('./../php/error.php');
		}
	}
	else
	{
		include_once('./../php/error.php');
	}
}

//-----------------------------------------------------------------
// Affichage de la page de login
//-----------------------------------------------------------------

else
{
	include_once('./../php/error.php');
}

?>