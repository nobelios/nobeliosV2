<?php

//*****************************************************************
//	Nobelios V2.0 beta
//	Gestion des nouveaux travaux
//	Script par Geoffrey HAUTECOUVERTURE
//	Toute reproduction totale ou partielle interdite
//	juillet - 2009
//*****************************************************************

//-----------------------------------------------------------------
// Gestion des nouveaux travaux
//-----------------------------------------------------------------

// Test si membre autorisé à rédiger ou modifier de pages
if (isset($_SESSION['login']) && isset($_SESSION['password']) && $userLevel >= 1) {

	// Test des dossiers de pages
	$dirTemp		= './web/' . $language . '/temp/' . $userId;	// Chemin d'accès aux fichiers temporaires
	$dirPages		= './web/' . $language . '/pages';	// Chemin d'accès aux pages
	$folderTemp		= testDir($dirTemp);
	$folderPages	= testDir($dirPages);
	$nbWork			= nbFolder($dirTemp); // Compte le nombre de dossier utilisateur
	
	// Aucune alerte par défaut
	$alert = null;

	// Définition de l'id du dossier
	$folderId = isset($_GET['id']) ? $_GET['id'] : '';
	
	
	//------------------------------------------------------------------
	// Définition de l'affichage
	//------------------------------------------------------------------
	
	// Suffix pour les sessions
	$sessionPage = 'user_pages_editor_';
	
	// Options de classement pour les fichiers utilisateur (enregistrement en session)
	if (!isset($_SESSION[$sessionPage . 'order']))																												$_SESSION[$sessionPage . 'order']	= 'name';
	if (!isset($_SESSION[$sessionPage . 'sort']))																												$_SESSION[$sessionPage . 'sort'] 	= 'asc';
	if (isset($_GET['order']) && (($_GET['order'] == 'name') || ($_GET['order'] == 'subject') || ($_GET['order'] == 'type') || ($_GET['order'] == 'request')))	$_SESSION[$sessionPage . 'order'] 	= $_GET['order'];
	if (isset($_GET['sort']) && (($_GET['sort'] == 'desc') || ($_GET['sort'] == 'asc')))																		$_SESSION[$sessionPage . 'sort'] 	= $_GET['sort'];
	
	
	//-----------------------------------------------------------------
	// Définition des variables contenant les données de la page
	//-----------------------------------------------------------------

	// Valeurs par défaut
	$defaultWork		= '';
	$defaultSubject		= '';
	$defaultType		= '';
	$defaultTitle		= '';
	$defaultPointer		= '';
	$defaultTag			= '';
	$defaultReference	= '';
	$defaultSmile 		= '';
	$defaultRequisite	= '';
	$defaultRequest		= 'works';
	
	// Définition par lecture de données existantes
	if (in_array($folderId, $folderTemp)) {
		// Lecture des propriétés de la page
		$openProperties = fopen($dirTemp . '/' . $folderId . '/properties.txt', 'r');
		if ($openProperties) {
			$defaultProperties = fgets($openProperties, 4096);
			fclose($openProperties);
			
			// Définition des données par défaut
			$defaultSubject		= preg_match('#.*\[subject\](.+)\[/subject\].*#i', $defaultProperties) 		? preg_replace('#.*\[subject\](.+)\[/subject\].*#i', '$1', $defaultProperties)		: $defaultSubject;
			$defaultType		= preg_match('#.*\[type\](.+)\[/type\].*#i', $defaultProperties) 			? preg_replace('#.*\[type\](.+)\[/type\].*#i', '$1', $defaultProperties) 			: $defaultType;
			$defaultTitle		= preg_match('#.*\[title\](.+)\[/title\].*#i', $defaultProperties)			? preg_replace('#.*\[title\](.+)\[/title\].*#i', '$1', $defaultProperties) 			: $defaultTitle;
			$defaultPointer		= preg_match('#.*\[pointer\](.+)\[/pointer\].*#i', $defaultProperties) 		? preg_replace('#.*\[pointer\](.+)\[/pointer\].*#i', '$1', $defaultProperties) 		: $defaultPointer;
			$defaultTag			= preg_match('#.*\[tag\](.+)\[/tag\].*#i', $defaultProperties) 				? preg_replace('#.*\[tag\](.+)\[/tag\].*#i', '$1', $defaultProperties) 				: $defaultTag;
			$defaultReference	= preg_match('#.*\[reference\](.+)\[/reference\].*#i', $defaultProperties) 	? preg_replace('#.*\[reference\](.+)\[/reference\].*#i', '$1', $defaultProperties) 	: $defaultReference;
			$defaultSmile 		= preg_match('#.*\[smile\](.+)\[/smile\].*#i', $defaultProperties) 			? preg_replace('#.*\[smile\](.+)\[/smile\].*#i', '$1', $defaultProperties) 			: $defaultSmile;
			$defaultRequisite	= preg_match('#.*\[requisite\](.+)\[/requisite\].*#i', $defaultProperties) 	? preg_replace('#.*\[requisite\](.+)\[/requisite\].*#i', '$1', $defaultProperties) 	: $defaultRequisite;
			$defaultRequest		= preg_match('#.*\[request\](.+)\[/request\].*#i', $defaultProperties) 		? preg_replace('#.*\[request\](.+)\[/request\].*#i', '$1', $defaultProperties) 		: $defaultRequisite;
		}
		
		// Lecture du texte de la page
		$openWork = fopen($dirTemp . '/' . $folderId .'/work.txt', 'r');
		if ($openWork) {
			$defaultWork = '';
			while (!feof($openWork))
				$defaultWork .= fgets($openWork, 4096);
			fclose($openWork);
		}
	}
	
	// Mise en mémoire des données postées
	$defaultWork		= isset($_POST['work']) 		? $_POST['work']				 		: $defaultWork;
	$defaultSubject		= isset($_POST['subject']) 		? htmlentities($_POST['subject']) 		: $defaultSubject;
	$defaultType		= isset($_POST['type']) 		? htmlentities($_POST['type']) 			: $defaultType;
	$defaultTitle		= isset($_POST['title'])		? htmlentities($_POST['title']) 		: $defaultTitle;
	$defaultPointer		= isset($_POST['pointer']) 		? htmlentities($_POST['pointer']) 		: $defaultPointer;
	$defaultTag			= isset($_POST['tag']) 			? htmlentities($_POST['tag']) 			: $defaultTag;
	$defaultReference	= isset($_POST['reference']) 	? htmlentities($_POST['reference']) 	: $defaultReference;
	$defaultSmile 		= isset($_POST['smile']) 		? htmlentities($_POST['smile']) 		: $defaultSmile;
	$defaultRequisite	= isset($_POST['requisite']) 	? htmlentities($_POST['requisite']) 	: $defaultRequisite;
	
	//-----------------------------------------------------------------
	// On veut ajouter ou éditer une page
	//-----------------------------------------------------------------

	if ((isset($_GET['new']) && !isset($_GET['edit']) && ($nbWork < $workLimit)) || (isset($_GET['edit']) && in_array($folderId, $folderTemp))) {
		
		// Création d'un nouveau fichier
		if (!empty($_POST['subject']) && !empty($_POST['type']) && !empty($_POST['title']) && !in_array($folderId, $folderTemp)) {
			
			// Création d'un dossier dans le fichier des travaux
			// Génération de nom de page jusqu'à ce qu'on trouve un disponible
			do {
				$folderId = randomHex();
				$reqName = in_array($folderId, $folderTemp);
			} while ($reqName == true);
			
			// Création des dossiers
			mkdir($dirTemp . '/' . $folderId, '0777');					// Création de la racine de la page
			mkdir($dirTemp . '/' . $folderId . '/images', '0777');		// Création du conteneur d'image
			mkdir($dirTemp . '/' . $folderId . '/files', '0777');		// Création du conteneur de fichiers
			mkdir($dirTemp . '/' . $folderId . '/save', '0777'); 		// Création du conteneur de sauvegardes
			mkdir($dirTemp . '/' . $folderId . '/videos', '0777'); 		// Création du conteneur de vidéos
			
			// Création des propriétés
			$properties = fopen($dirTemp.'/'.$folderId.'/properties.txt', 'w+');
			$propertiesBases = preg_replace('#\[subject\].*\[/subject\]#i', '[subject]' . $_POST['subject'] . '[/subject]' , $propertiesBases);
			$propertiesBases = preg_replace('#\[type\].*\[/type\]#i', '[type]' . $_POST['type'] . '[/type]' , $propertiesBases);
			$propertiesBases = preg_replace('#\[title\].*\[/title\]#i', '[title]' . $_POST['title'] . '[/title]' , $propertiesBases);
			$propertiesBases = preg_replace('#\[author\].*\[/author\]#i', '[author]' . $userId . '[/author]' , $propertiesBases);
			$propertiesBases = preg_replace('#\[request\].*\[/request\]#i', '[request]works[/request]' , $propertiesBases);
			fputs($properties, $propertiesBases);
			fclose($properties);
			
			// Création de la page de travail
			$work = fopen($dirTemp.'/'.$folderId.'/work.txt', 'w+');
			fclose($work);
		}
		
		// Formulaire de création d'un nouveau fichier
		if ((empty($_POST['subject']) || empty($_POST['type']) || empty($_POST['title'])) && !in_array($folderId, $folderTemp)) {
			
			// affichage des erreurs
			if (isset($_POST['subject']) || isset($_POST['type']) || isset($_POST['title'])) {
				// Message d'erreur
				$erroralert		= $message['alert_form_empty_field'] . '<br />';
				$alert			= $erroralert;
				// Vérification des champs de formulaire (indique les champs vides)
				if ($defaultSubject == '')	$alert .= $message['alert_form_empty_subject'] . '<br/>';
				if ($defaultType == '')		$alert .= $message['alert_form_empty_type'] . '<br/>';
				if ($defaultTitle == '')	$alert .= $message['alert_form_empty_title'] . '<br/>';
				if ($defaultWork == '')		$alert .= $message['alert_form_empty_work'] . '<br/>';
			}
			
			// Affichage du Linker
			echo '<div class="pageLinker">
				<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_pages_editor">' . $message['navigation_tree_make_works'] . '</a>&nbsp;>&nbsp;
				' . $message['navigation_tree_make_works_new'] . '
			</div>';
			
			// Affichage des informations
			echo '<div class="pageFolderInfo">
				<div class="pageImage">
					<img src="' . $userDesignPath . '/images/make_works.png" alt="" />
				</div>
				<div class="pageText">
					' . $message['legend_work_new'] . '
				</div>
			</div>';
			
			// Affichage des alertes
			if ($alert != null) {
				echo '<div class="pageAlert">
					' . $alert . '
				</div>';
			}
			
			echo'<div class="pageList">
				<form method="post" id="work_page" action="./index.php?user_pages_editor&amp;new">
					<table>
						<tr>
							<td>' . $message['legend_form_subject'] . '</td>
							<td>
								<select name="subject">';
								// Selection d'un sujet
								foreach ($allowedSubject as $key=>$val) {
									if ($allowedSubject[$key] == $defaultSubject)
										echo '<option value="' . $allowedSubject[$key]  . '" selected>' . $message['choice_subject_' . $allowedSubject[$key]] . '</option>';
									else
										echo '<option value="' . $allowedSubject[$key]  . '">' . $message['choice_subject_' . $allowedSubject[$key]] . '</option>';
								}
								echo '</select>
							</td>
						</tr>
						<tr>
							<td>' . $message['legend_form_type'] . '</td> 
							<td>
								<select name="type">';
								// Selection d'une catégorie
								foreach ($allowedType as $key=>$val) {
									if ($allowedType[$key] == $defaultType)
										echo '<option value="' . $allowedType[$key]  . '" selected>' . $message['choice_type_' . $allowedType[$key]] . '</option>';
									else
										echo '<option value="' . $allowedType[$key]  . '">' . $message['choice_type_' . $allowedType[$key]] . '</option>';
								}
								echo '</select>
							</td>
						</tr>
						<tr>
							<td>' . $message['legend_form_title'] . '</td>
							<td><input type="text" name="title" size="50" maxlength="100" value="' . $defaultTitle . '" /></td>
						</tr>
					</table>
					<p>
						<input type="submit" value="' . $message['action_send'] . '" />
					</p>
				</form>
			</div>
			<a href="./index.php?user_pages_editor">' . $message['action_cancel'] . '</a><br />';
		}
		
		// On veut éditer ou ajouter des données
		elseif (in_array($folderId, $folderTemp) && !empty($_POST['work']) && !empty($_POST['subject']) && !empty($_POST['type']) && !empty($_POST['title']) && isset($_POST['pointer']) && isset($_POST['tag']) && isset($_POST['reference']) && isset($_POST['smile']) && isset($_POST['requisite'])) {			
			// Ajout des propriétés
			$properties = fopen($dirTemp.'/'.$folderId.'/properties.txt', 'r+');
			$getProperties = fgets($properties);
			$getProperties = preg_replace('#\[subject\].*\[/subject\]#i', '[subject]' . $_POST['subject'] . '[/subject]' , $getProperties);
			$getProperties = preg_replace('#\[type\].*\[/type\]#i', '[type]' . $_POST['type'] . '[/type]' , $getProperties);
			$getProperties = preg_replace('#\[title\].*\[/title\]#i', '[title]' . $_POST['title'] . '[/title]' , $getProperties);
			$getProperties = preg_replace('#\[pointer\].*\[/pointer\]#i', '[pointer]' . $_POST['pointer'] . '[/pointer]' , $getProperties);
			$getProperties = preg_replace('#\[tag\].*\[/tag\]#i', '[tag]' . $_POST['tag'] . '[/tag]' , $getProperties);
			$getProperties = preg_replace('#\[reference\].*\[/reference\]#i', '[reference]' . $_POST['reference'] . '[/reference]' , $getProperties);
			$getProperties = preg_replace('#\[smile\].*\[/smile\]#i', '[smile]' . $_POST['smile'] . '[/smile]' , $getProperties);
			$getProperties = preg_replace('#\[requisite\].*\[/requisite\]#i', '[requisite]' . $_POST['requisite'] . '[/requisite]' , $getProperties);
			$getProperties = preg_replace('#\[author\].*\[/author\]#i', '[author]' . $userId . '[/author]' , $getProperties);
			$getProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]works[/request]' , $getProperties);
			fseek($properties, 0);
			fputs($properties, $getProperties);
			fclose($properties);
			
			// Ajout du texte
			$work = fopen($dirTemp . '/' . $folderId . '/work.txt', 'r+');
			ftruncate($work, 0);
			fseek($work, 0);
			fputs($work, $_POST['work']);
			fclose($work);
		}
		
		// On affiche le formulaire d'édition d'une page
		else {
			$folderDir = './web/' . $language . '/temp/' . $userId . '/' . $folderId;
			
			// affichage des erreurs
			if (isset($_POST['subject']) && isset($_POST['type']) && isset($_POST['title']) && isset($_POST['work'])) {
				// Message d'erreur
				$erroralert		= $message['alert_form_empty_field'] . '<br />';
				$alert			= $erroralert;
				// Vérification des champs de formulaire (indique les champs vides)
				if ($defaultSubject == '')	$alert .= $message['alert_form_empty_subject'] . '<br/>';
				if ($defaultType == '')		$alert .= $message['alert_form_empty_type'] . '<br/>';
				if ($defaultTitle == '')	$alert .= $message['alert_form_empty_title'] . '<br/>';
				if ($defaultWork == '')		$alert .= $message['alert_form_empty_work'] . '<br/>';
			}
			
			// Affichage du Linker
			echo '<div class="pageLinker">
				<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_pages_editor">' . $message['navigation_tree_make_works'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '">' . $message['navigation_tree_make_works_optn'] . '</a>&nbsp;>&nbsp;
				' . $message['navigation_tree_make_works_edit'] . '
			</div>';
			
			// Affichage des informations
			echo '<div class="pageFolderInfo">
				<div class="pageImage">
					<img src="' . $userDesignPath . '/images/make_works.png" alt="" />
				</div>
				<div class="pageText">
					' . $message['legend_work_edit'] . '
				</div>
			</div>';
			
			// Affichage des alertes
			if ($alert != null) {
				echo '<div class="pageAlert">
					' . $alert . '
				</div>';
			}
			
			// Code HTML : formulaire
			echo '<div class="pageList">
				<form method="post" id="work_page" action="./index.php?user_pages_editor&amp;id=' . $folderId . '&amp;edit">
					<table>
						<tr>
							<td>' . $message['legend_subject'] . '</td>
							<td>
								<select name="subject">';
								// Selection d'un sujet
								foreach ($allowedSubject as $key=>$val) {
									if ($allowedSubject[$key] == $defaultSubject)
										echo '<option value="' . $allowedSubject[$key]  . '" selected>' . $message['choice_subject_' . $allowedSubject[$key]] . '</option>';
									else
										echo '<option value="' . $allowedSubject[$key]  . '">' . $message['choice_subject_' . $allowedSubject[$key]] . '</option>';
								}
								echo '</select>
							</td>
						</tr>
						<tr>
							<td>' . $message['legend_form_type'] . '</td> 
							<td>
								<select name="type">';
								// Selection d'une catégorie
								foreach ($allowedType as $key=>$val) {
									if ($allowedType[$key] == $defaultType)
										echo '<option value="' . $allowedType[$key]  . '" selected>' . $message['choice_type_' . $allowedType[$key]] . '</option>';
									else
										echo '<option value="' . $allowedType[$key]  . '">' . $message['choice_type_' . $allowedType[$key]] . '</option>';
								}
								echo '</select>
							</td>
						</tr>
						<tr>
							<td>' . $message['legend_form_title'] . '</td>
							<td><input type="text" name="title" size="50" maxlength="100" value="' . $defaultTitle . '" /></td>
						</tr>
						<tr>
							<td>' . $message['legend_form_pointer'] . '</td>
							<td><input type="text" name="pointer" size="50" maxlength="100" value="' . $defaultPointer . '" /></td>
						</tr>
						<tr>
							<td>' . $message['legend_form_tag'] . '</td>
							<td><input type="text" name="tag" size="50" maxlength="100" value="' . $defaultTag . '" /></td>
						</tr>
						<tr>
							<td>' . $message['legend_form_reference'] . '</td>
							<td><input type="text" name="reference" size="50" maxlength="100" value="' . $defaultReference . '" /></td>
						</tr>
						<tr>
							<td>' . $message['legend_form_smile'] . '</td>
							<td><input type="text" name="smile" size="50" maxlength="100" value="' . $defaultSmile . '" /></td>
						</tr>
						<tr>
							<td>' . $message['legend_form_requisite'] . '</td>
							<td><input type="text" name="requisite" size="50" maxlength="100" value="' . $defaultRequisite . '" /></td>
						</tr>
					</table>
					<script type="text/javascript" src="./editech/full_editech.js"></script>';
					
					include('./editech/full_editech.php');
					
					echo'<div>
						<input type="submit" value="' . $message['action_send'] . '" />
					</div>
				</form>
				<p>';
					
					if (isset($_GET['new']))
						echo '<a href="./index.php?user_pages_editor">' . $message['action_finish_later'] . '</a>';
					else
						echo '<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '">' . $message['action_cancel'] . '</a>';
				echo '</p>
			</div>';
		}
	}
	
	
	//-----------------------------------------------------------------
	// On veut supprimer une ou plusieurs pages
	//-----------------------------------------------------------------
	
	// On demande la suppression d'une unique page
	elseif (isset($_GET['remove']) && in_array($folderId, $folderTemp)) {
		if (isset($_GET['confirm'])) {
			$alert .= $message0002.' '.$folderId.' '.$defaultTitle.' '.$message0003.'.<br/>';
			clearDir($dirTemp .'/'. $folderId);
			$alert .= '<a href="./index.php?user_pages_editor">'.$button0003.'</a>';
		}
		// On demande la confirmation de la suppression de la page 
		else {
			$alert .= $message0013.' '.$folderId.' '.$defaultTitle.': ('.$message0014.')<br />';
			$alert .= '<a href="./index.php?user_pages_editor&amp;id='.$folderId.'&amp;remove&amp;confirm">'.$button0001.'</a><br />';
			$alert .= '<a href="./index.php?user_pages_editor&amp;id='.$folderId.'">'.$button0002.'</a>';
		}
	}
	
	// On demande la suppression d'une ou plusieurs pages
	elseif (isset($_POST['remove'])) {
		if (isset($_POST['confirm'])) {
			for ($i=0; $i<$nbWork; $i++) {
				if (isset($_POST['checkbox' . $i])) {
					if (in_array($_POST['checkbox' . $i], $folderTemp)) {
						$listeArray[] = $_POST['checkbox' . $i];
						// Effacement du répertoire
						if (clearDir($dirTemp . '/' . $_POST['checkbox' . $i]))
							$alert .= $message['message_page_deleted'] . ' ' . $_POST['checkbox' . $i] . ' : ' . $defaultTitle . '.<br/>';
						else 
							$alert .= $message['alert_remove_failure'] . '<br />';
						echo '<a href="./index.php?user_pages_editor">' . $message['action_return_to_work'] . '</a>';
					}
				}
			}
			$alert .= '<a href="./index.php?user_pages_editor">' . $message['action_close']  . '<br /></a>';
		} else {
			$nbWorkRemove = 0;
			for ($i=0; $i<$nbWork; $i++) {
				if (isset($_POST['checkbox' . $i])) {
					$nbWorkRemove++;
				}
			}
			if ($nbWorkRemove != 0)
			{
				$alert .= '<form action="./index.php?user_pages_editor" method="post">';
				if ($nbWorkRemove > 1) $alert .= $message['remove_following_files'] . '<br />';
				else $alert .= $message['remove_following_file'] . '<br />';
				for ($i=0; $i<$nbWork; $i++) {
					if (isset($_POST['checkbox' . $i])) {
						$alert .= '<input type="hidden" name="checkbox' . $i . '" value="' . $_POST['checkbox' . $i] . '" />';
						$alert .= $_POST['checkbox' . $i] . '<br />';
					}
				}
				$alert .= '<input type="hidden" name="confirm">';
				$alert .= '<input type="submit" name="remove" value=' . $message['action_confirm_yes'] . ' />';
				$alert .= '<a href="./index.php?user_pages_editor">' . $message['action_confirm_no'] . '</a></form>';
			}
		}
	}
	
	
	//-----------------------------------------------------------------
	// On veut publier une ou plusieurs pages
	//-----------------------------------------------------------------
	
		
	// On demande la suppression d'une unique page
	elseif (isset($_GET['publish']) && in_array($folderId, $folderTemp)) {
		if (isset($_GET['confirm'])) {
			// Lecture du texte de la page
			if ($userLevel > 1) {
				if ($defaultWork != '') {
					// Génération de nom de page jusqu'à ce qu'on trouve un disponible
					do {
						$newFolderId = randomHex();
						$reqName = in_array($newFolderId, $folderPages);
					} while ($reqName == true);
					
					// Modification des liens relatifs pour les images et les fichiers
					$work = fopen($dirTemp . '/' . $folderId . '/work.txt', 'r+');
					ftruncate($work, 0);
					fseek($work, 0);
					fputs($work, str_replace($dirTemp . '/' . $folderId, 'pages/' . $newFolderId, $defaultWork));
					fclose($work);
					
					// Déplacement et suppression du dossier de travaux
					copyDir($dirTemp . '/' . $folderId, $dirPages . '/' . $newFolderId);
					clearDir($dirTemp . '/' . $folderId);
					
					$alert .= "A été publié " . $folderId . "<br />";
				} else {
					$alert .= "N'a pas été publié " . $folderId . "<br />";
				}
			}
			else {
				// Demande de publication des pages et affichage des pages non publiées
				if ($defaultWork != '') {
					// Marqueur de requête
					$properties = fopen($dirTemp . '/' . $folderId .'/properties.txt', 'r+');
					$getProperties = fgets($properties);
					$getProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]request[/request]' , $getProperties);
					fseek($properties, 0);
					fputs($properties, $getProperties);
					fclose($properties);
					$alert .= "Demande de publication " . $folderId . "<br />";
				} else {
					$alert .= "Demande impossible " . $folderId . "<br />";
				}
			}
		}
		// On demande la confirmation de la publication de la page 
		else {
			// Liste les pages qui seront ou ne seront pas publiés
			$alert .= "Vous vous publir la page suivante ?<br />";
			if ($defaultWork != '')
				$alert .= "Sera publié " . $folderId . '<br />';
			else
				$alert .= "Ne peut pas être publié " . $folderId . "<br />";
			$alert .= '<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '&amp;publish&amp;confirm">' . $message['action_confirm_yes'] . '</a>';
			$alert .= '<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '">' . $message['action_confirm_no'] . '</a>';
		}
	}
	
	// On demande la publication d'une ou plusieurs pages
	elseif (isset($_POST['publish'])) {
		if (isset($_POST['confirm'])) {
			for ($i=0; $i<$nbWork; $i++) {
				if (isset($_POST['checkbox' . $i])) {
					if (in_array($_POST['checkbox' . $i], $folderTemp)) {
						// Lecture du texte de la page
						$openWork = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/work.txt', 'r');
						if ($openWork) {
							$defaultWork = '';
							while (!feof($openWork))
								$defaultWork .= fgets($openWork, 4096);
							fclose($openWork);
						}
						if ($userLevel > 1) {
							if ($defaultWork != '') {
								// Génération de nom de page jusqu'à ce qu'on trouve un disponible
								do {
									$newFolderId = randomHex();
									$reqName = in_array($newFolderId, $folderPages);
								} while ($reqName == true);
								
								// Modification des liens relatifs pour les images et les fichiers
								$work = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/work.txt', 'r+');
								ftruncate($work, 0);
								fseek($work, 0);
								fputs($work, str_replace($dirTemp . '/' . $_POST['checkbox' . $i], 'pages/' . $newFolderId, $defaultWork));
								fclose($work);
								
								// Déplacement et suppression du dossier de travaux
								copyDir($dirTemp . '/' . $_POST['checkbox' . $i], $dirPages . '/' . $newFolderId);
								clearDir($dirTemp . '/' . $_POST['checkbox' . $i]);
								
								$alert .= "A été publié " . $_POST['checkbox' . $i] . "<br />";
							} else {
								$alert .= "N'a pas été publié " . $_POST['checkbox' . $i] . "<br />";
							}
						}
						else {
							// Demande de publication des pages et affichage des pages non publiées
							if ($defaultWork != '') {
								$listeArray[] = $_POST['checkbox' . $i];
								// Marqueur de requête
								$properties = fopen($dirTemp . '/' . $_POST['checkbox' . $i] .'/properties.txt', 'r+');
								$getProperties = fgets($properties);
								$getProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]request[/request]' , $getProperties);
								fseek($properties, 0);
								fputs($properties, $getProperties);
								fclose($properties);
								$alert .= "A été publié " . $_POST['checkbox' . $i] . "<br />";
							} else {
								$alert .= "N'a pas été publié " . $_POST['checkbox' . $i] . "<br />";
							}
						}
					}
				}
			}
			$alert .= '<a href="./index.php?user_pages_editor">' . $message['action_close']  . '<br /></a>';
		} else {
			$nbWorkPublish = 0;
			for ($i=0; $i<$nbWork; $i++) {
				if (isset($_POST['checkbox' . $i])) {
					$nbWorkPublish++;
				}
			}
			if ($nbWorkPublish != 0)
			{
				$alert .= '<form action="./index.php?user_pages_editor" method="post">';
				if ($nbWorkPublish > 1) $alert .= $message['publish_following_files'] . '<br />';
				else $alert .= $message['publish_following_file'] . '<br />';
				for ($i=0; $i<$nbWork; $i++) {
					if (isset($_POST['checkbox' . $i])) {
						// Lecture du texte de la page
						$openWork = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/work.txt', 'r');
						if ($openWork) {
							$defaultWork = '';
							while (!feof($openWork))
								$defaultWork .= fgets($openWork, 4096);
							fclose($openWork);
						}
						// Liste les pages qui seront ou ne seront pas publiés
						$alert .= '<input type="hidden" name="checkbox' . $i . '" value="' . $_POST['checkbox' . $i] . '" />';
						if ($defaultWork != '')
							$alert .= "Sera publié " . $_POST['checkbox' . $i] . '<br />';
						else
							$alert .= "Ne peut pas être publié " . $_POST['checkbox' . $i] . "<br />";
					}
				}
				$alert .= '<input type="hidden" name="confirm">';
				$alert .= '<input type="submit" name="publish" value=' . $message['action_confirm_yes'] . ' />';
				$alert .= '<a href="./index.php?user_pages_editor">' . $message['action_confirm_no'] . '</a></form>';
			}
		}
	}
	
	//-----------------------------------------------------------------
	// Classement des fichiers
	//-----------------------------------------------------------------

	// Mise à jour des information sur le fichiers
	$folderTemp 		= testDir($dirTemp);
	$nbWork				= nbFolder($dirTemp);
	$nbWorkRequest 		= 0;
	
	// Déclaration des tables
	$tableName 		= array();
	$tableSubject 	= array();
	$tableType		= array();
	$tableRequest	= array();
	
	// Boucle de liste des fichiers pour trier les données
	for ($i=0; $i<$nbWork; $i++) {
		$file = $dirTemp . '/' . $folderTemp[$i];
		
		// Lectures des données des pages
		$openProperties = fopen($file . '/properties.txt', 'r');
		if ($openProperties) {
			$defaultProperties = '';
			while (!feof($openProperties))
				$defaultProperties .= fgets($openProperties, 4096);
			fclose($openProperties);
			$tableId[$i] 		= $folderTemp[$i];
			$tableName[$i]		= preg_match('#.*\[title\](.+)\[/title\].*#i', $defaultProperties) 		? preg_replace('#.*\[title\](.+)\[/title\].*#i', '$1', $defaultProperties)							: '';
			$tableSubject[$i] 	= preg_match('#.*\[subject\](.+)\[/subject\].*#i', $defaultProperties)	? $pageSubjectName[preg_replace('#.*\[subject\](.+)\[/subject\].*#i', '$1', $defaultProperties)]	: ''; // $pageSubjectName permet un classement valide dans la langue de l'utilisateur
			$tableType[$i]		= preg_match('#.*\[type\](.+)\[/type\].*#i', $defaultProperties)		? $pageTypeName[preg_replace('#.*\[type\](.+)\[/type\].*#i', '$1', $defaultProperties)]				: ''; // $pageTypeName permet un classement valide dans la langue de l'utilisateur
			$tableRequest[$i]	= preg_match('#.*\[request\](.+)\[/request\].*#i', $defaultProperties) 	? $pageRequestName[preg_replace('#.*\[request\](.+)\[/request\].*#i', '$1', $defaultProperties)]	: ''; // $pageRequestName permet un classement valide dans la langue de l'utilisateur
			// Compteur de requêtes de publication
			if ($tableRequest[$i] == $pageRequestName['request'])
				$nbWorkRequest++;
		}
	}
	
	$tableSort = array(
		'name'		=> $tableName,
		'subject'	=> $tableSubject,
		'type' 		=> $tableType,
		'request'	=> $tableRequest
		
	);
	
	// On procède à un tri sur le type de données	
	$tableOrder = $tableSort[$_SESSION['user_pages_editor_order']];
	
	// On procède à un tri alphanumérique
	if ($_SESSION['user_pages_editor_sort'] == 'desc') arsort($tableOrder);
	else asort($tableOrder);
	
	
	//-----------------------------------------------------------------
	// On veut afficher un apperçu de la page
	//-----------------------------------------------------------------

	// On affiche un apperçu de la page
	if (in_array($folderId, $folderTemp) && ((!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['confirm']))) || (!empty($_POST['work']) && !empty($_POST['subject']) && !empty($_POST['type']) && !empty($_POST['title']) && isset($_POST['pointer']) && isset($_POST['tag']) && isset($_POST['reference']) && isset($_POST['smile']) && isset($_POST['requisite']))) {	
		
		// Affichage du Linker
		echo '<div class="pageLinker">
			<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a>&nbsp;>&nbsp;
			<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a>&nbsp;>&nbsp;
			<a href="./index.php?user_pages_editor">' . $message['navigation_tree_make_works'] . '</a>&nbsp;>&nbsp;
			' . $message['navigation_tree_make_works_optn'] . '
		</div>';
		
		// Affichage des informations
		echo '<div class="pageFolderInfo">
			<div class="pageImage">
				<img src="' . $userDesignPath . '/images/make_works.png" alt="" />
			</div>
			<div class="pageText">
				<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '&amp;publish">' . $button0006 . '</a><br />
				<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '&amp;edit">' . $button0007 . '</a><br />
				<a href="./index.php?user_pages_editor&amp;id=' . $folderId . '&amp;remove">' . $button0008 . '</a><br />
				<a href="./index.php?user_pages_editor">' . $button0003 . '</a>
			</div>
		</div>';
		
		// Affichage des alertes
		if ($alert != null) {
			echo '<div class="pageAlert">
				' . $alert . '
			</div>';
		}
		
		echo '<div class="pageFileInfo">
			<div class="pageText">
				<table>
					<tr>
						<td><strong>Catégorie:</strong></td>
						<td>' . $defaultType . '</td>
					</tr>
					<tr>
						<td><strong>Subject:</strong></td>
						<td>' . $defaultSubject . '</td>
					</tr>
					<tr>
						<td><strong>Pointeurs:</strong></td>
						<td>' . $defaultPointer . '</td>
					</tr>
					<tr>
						<td><strong>Tags:</strong></td>
						<td>' . $defaultTag . '</td>
					</tr>
					<tr>
						<td><strong>Référence:</strong></td>
						<td>' . $defaultReference . '</td>
					</tr>
					<tr>
						<td><strong>Smile:</strong></td>
						<td>' . $defaultSmile . '</td>
					</tr>
					<tr>
						<td><strong>Requis:</strong></td>
						<td>' . $defaultRequisite . '</td>
					</tr>
				</table>
			</div>
		</div>';
		
		echo'<div>
			<div style="text-align:justify">
				<div class="pageTitle">
					' . $defaultTitle . '
				</div>
				<div>
					' . parse($defaultWork) . '
				</div>
			</div>
		</div>';
	}

	
	//-----------------------------------------------------------------
	// On affiche les travaux en cours
	//-----------------------------------------------------------------
	elseif (!isset($_GET['edit']) && !isset($_GET['new'])) {
		
		// Affichage du Linker
		echo '<div class="pageLinker">
			<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a>&nbsp;>&nbsp;
			<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a>&nbsp;>&nbsp;
			' . $message['navigation_tree_make_works'] . '
		</div>';
		
		// Affichage des informations
		echo '<div class="pageFolderInfo">
			<div class="pageImage">
				<img src="' . $userDesignPath . '/images/make_works.png" alt="" />
			</div>
			<div class="pageText">
				' . $message['legend_work_pending'] . ' : ' . $nbWork . ' / ' . $workLimit . '<br />
				' . $message['legend_work_validation'] . ' : automatique<br />
				' . $message['legend_work_validation_pending'] . ' : ' . $nbWorkRequest . ' / ' . $workLimit . '<br />
			</div>
		</div>';
		
		// Affichage des alertes
		if ($alert != null) {
			echo '<div class="pageAlert">
				' . $alert . '
			</div>';
		}
		
		echo '<div class="pageList">';
		if ($nbWork < $workLimit)
			echo "<a href=\"./index.php?user_pages_editor&amp;new\">Ajouter une page</a><br />";
		else
			echo "Limite atteinte, veuillez terminer vos travaux en cours avant de créer de nouvelles pages<br />";
		
		echo "</div>";		
		
		// Affichage sous forme de liste
		echo '<div class="pageList">
			<form class="pageListForm" action="./index.php?user_pages_editor" method="post">
				<div>
					<table cellspacing="0px" class="pageListLegendTable">
						<tr class="pageListLegendRow">
							<td class="pageListLegendCell" style="width: 500px;">';
								if ($_SESSION[$sessionPage . 'order'] == 'name') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_pages_editor&amp;order=name&amp;sort=asc">' . $message['legend_name'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_pages_editor&amp;order=name&amp;sort=desc">' . $message['legend_name'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_pages_editor&amp;order=name&amp;sort=asc">' . $message['legend_name'] . '</a>';
								}
							echo '</td>
							<td class="pageListLegendCell" style="width: 90px">'; 
								if ($_SESSION[$sessionPage . 'order'] == 'subject') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_pages_editor&amp;order=subject&amp;sort=asc">' . $message['legend_subject'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_pages_editor&amp;order=subject&amp;sort=desc">' . $message['legend_subject'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_pages_editor&amp;order=subject&amp;sort=asc">' . $message['legend_subject'] . '</a>';
								}
							echo '</td>
							<td class="pageListLegendCell" style="width: 90px">'; 
								if ($_SESSION[$sessionPage . 'order'] == 'type') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_pages_editor&amp;order=type&amp;sort=asc">' . $message['legend_type'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_pages_editor&amp;order=type&amp;sort=desc">' . $message['legend_type'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_pages_editor&amp;order=type&amp;sort=asc">' . $message['legend_type'] . '</a>';
								}
							echo '</td>
							<td class="pageListLegendCell">'; 
								if ($_SESSION[$sessionPage . 'order'] == 'request') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_pages_editor&amp;order=request&amp;sort=asc">' . $message['legend_request'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_pages_editor&amp;order=request&amp;sort=desc">' . $message['legend_request'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_pages_editor&amp;order=request&amp;sort=asc">' . $message['legend_request'] . '</a>';
								}
							echo '</td>
						</tr>
					</table>
				</div>	
				<div class="pageListDisplay">';
				if ($nbWork == 0) {
					echo $message['message_empty_file']; // Affichage d'un message dans le cas ou le dossier est vide
				} else {
					echo '<table cellspacing="0px" class="pageListTable">';
					$i=0; // Raz du compteur
					foreach ($tableOrder as $key=>$val) {
						echo '<tr class="pageListRowColor1" onclick="checkTheBox(\'table1_chk\', ' . $i . ');">
							<td class="pageListCell" style="width: 500px" id="table1_chk' . $i . '">
								<input type="checkbox" name="checkbox' . $i . '" id="checkbox' . $i . '" value="' . $tableId[$key] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . ');" />
								<a href="./index.php?user_pages_editor&amp;id=' . $tableId[$key] . '">' . cutString($tableName[$key], 53, 1) . '</a>
							</td>
							<td class="pageListCell" style="width: 90px;">' . $tableSubject[$key] . '</td>
							<td class="pageListCell" style="width: 90px">' . $tableType[$key] . '</td>
							<td class="pageListCell">' . $tableRequest[$key] . '</td>
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
					<input type="submit" name="publish" value="' . $message['action_publish_selection'] . '" />
				</div>
			</form>
		</div>';
	}
}

// Si il n'a pas le droit de poster une page
else
{
	include_once('./php/login.php');
}

?>