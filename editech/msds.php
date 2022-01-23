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
	if (in_array('properties.txt', $pageFolder = testDir($pageDir = './..' . $_GET['path']))) {
		
		$properties		= fopen($pageDir.'/properties.txt', 'r+');									// Ouverture du fichier properties.txt
		$getProperties	= fgets($properties);														// Lecture du fichier properties.txt
		$author			= preg_replace('#.*\[author\](.+)\[/author\].*#i', '$1', $getProperties); 	// Récupération de l'id de l'auteur
		$title			= preg_replace('#.*\[title\](.+)\[/title\].*#i', '$1', $getProperties);		// Récupération du titre de la page
		
		// Test si l'utilisateur est l'auteur de la page ou si opérateur système (tout droits accordés)
		if (($author == $userId) || ($level == 4)) {
			
			$dir = './../web/' . $language . '/msds/works';		// Définition du chemin du dossier
			$folderSize = folderSize($dir);						// Taille du dossier
			$alert = null;										// Aucune alerte par défaut
			$fileInfo = null;									// Aucune information par défaut
			
			//------------------------------------------------------------------
			// Définition de l'affichage
			//------------------------------------------------------------------
			
			// Suffix pour les sessions
			$sessionPage = 'editech_msds_';
			
			// Options de classement par défaut pour les fichiers utilisateur (enregistrement en session)
			if (!isset($_SESSION[$sessionPage . 'order'])) $_SESSION[$sessionPage . 'order'] = 'name';	// Organisation selon une donnée par défaut (name)
			if (!isset($_SESSION[$sessionPage . 'sort'])) $_SESSION[$sessionPage . 'sort'] = 'asc';		// Organisation selon un ordre par défaut (asc)
			
			// Options de classement pour les fichiers utilisateur (enregistrement en session)
			if (isset($_GET['order']) && (($_GET['order'] == 'name') || ($_GET['order'] == 'formula') || ($_GET['order'] == 'CAS_number') || ($_GET['order'] == 'EC_number'))) $_SESSION[$sessionPage . 'order'] = $_GET['order']; 	// Organiser selon une donnée (titre, taille, etc)
			if (isset($_GET['sort']) && (($_GET['sort'] == 'desc') || ($_GET['sort'] == 'asc'))) $_SESSION[$sessionPage . 'sort'] = $_GET['sort'];													// Organiser selon un ordre (croissant ou descroissant)
			
			//-----------------------------------------------------------------
			// Création de la liste des MSDS à afficher
			//-----------------------------------------------------------------
				
			echo '<div class="pageList">
				Liste des MSDS à afficher<br />';
				
				if (!isset($_SESSION[$sessionPage . 'select']) || isset($_GET['remove'])) $_SESSION[$sessionPage . 'select'] = array();	// Déclaration d'un tableau vierge pour la liste de produits
					
				if (!empty($_GET['id']) && in_array('msds.txt', $pageFolder = testDir($pageDir = $dir . '/' . $_GET['id'])) && !in_array($_GET['id'], $_SESSION[$sessionPage . 'select'])) {
					$_SESSION[$sessionPage . 'select'][] = htmlentities($_GET['id']);
				}
				
				if (count($_SESSION[$sessionPage . 'select']) != 0) {
					$techData = '<table center><br /><head><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_designation'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_formula'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_molar_mass'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_density'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_melting_point'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_boiling_point'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_solubility'] . '</cell><br /></head><br />';
					$secuData = '<table center><br /><head><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_designation'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_R_phrase'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_S_phrase'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_hazard'] . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $message['legend_msds_caution'] . '</cell><br /></head><br />';
					foreach($_SESSION[$sessionPage . 'select'] as $key=>$val) {
						// Lecture des propriétés de la page
						$openProperties = fopen($dir . '/'. $_SESSION[$sessionPage . 'select'][$key] . '/msds.txt', 'r');
						if ($openProperties) {
							$defaultProperties = fgets($openProperties, 4096);
							fclose($openProperties);
							$defaultName			= preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties)					? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) 					: ' ';
							$defaultFormula 		= preg_match('#.*\[formula\](.+)\[/formula\].*#i', $defaultProperties)				? preg_replace('#.*\[formula\](.+)\[/formula\].*#i', '$1', $defaultProperties) 				: ' ';
							$defaultMolarMass		= preg_match('#.*\[molar_mass\](.+)\[/molar_mass\].*#i', $defaultProperties)		? preg_replace('#.*\[molar_mass\](.+)\[/molar_mass\].*#i', '$1', $defaultProperties) 		: ' ';
							$defaultDensity 		= preg_match('#.*\[density\](.+)\[/density\].*#i', $defaultProperties)				? preg_replace('#.*\[density\](.+)\[/density\].*#i', '$1', $defaultProperties) 				: ' ';
							$defaultMeltingPoint 	= preg_match('#.*\[melting_point\](.+)\[/melting_point\].*#i', $defaultProperties)	? preg_replace('#.*\[melting_point\](.+)\[/melting_point\].*#i', '$1', $defaultProperties) 	: ' ';
							$defaultBoilingPoint 	= preg_match('#.*\[boiling_point\](.+)\[/boiling_point\].*#i', $defaultProperties)	? preg_replace('#.*\[boiling_point\](.+)\[/boiling_point\].*#i', '$1', $defaultProperties) 	: ' ';
							$defaultWaterSol		= preg_match('#.*\[water_sol\](.+)\[/water_sol\].*#i', $defaultProperties)			? preg_replace('#.*\[water_sol\](.+)\[/water_sol\].*#i', '$1', $defaultProperties) 			: ' ';
							$defaultOtherSol 		= preg_match('#.*\[other_sol\](.+)\[/other_sol\].*#i', $defaultProperties)			? preg_replace('#.*\[other_sol\](.+)\[/other_sol\].*#i', '$1', $defaultProperties) 			: ' ';
							$defaultRPhrase			= preg_match('#.*\[R_phrase\](.+)\[/R_phrase\].*#i', $defaultProperties)			? preg_replace('#.*\[R_phrase\](.+)\[/R_phrase\].*#i', '$1', $defaultProperties) 			: ' ';
							$defaultSPhrase	 		= preg_match('#.*\[S_phrase\](.+)\[/S_phrase\].*#i', $defaultProperties)			? preg_replace('#.*\[S_phrase\](.+)\[/S_phrase\].*#i', '$1', $defaultProperties) 			: ' ';
							$defaultHazard			= preg_match('#.*\[hazard\](.+)\[/hazard\].*#i', $defaultProperties)				? preg_replace('#.*\[hazard\](.+)\[/hazard\].*#i', '$1', $defaultProperties) 				: ' ';
							$defaultCaution 		= preg_match('#.*\[caution\](.+)\[/caution\].*#i', $defaultProperties)				? preg_replace('#.*\[caution\](.+)\[/caution\].*#i', '$1', $defaultProperties) 				: ' ';
							
							$techData .= '<line><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultName . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . preg_replace('#(\d+)#i', '<sub>$1</sub>',$defaultFormula) . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultMolarMass . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultDensity . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultMeltingPoint . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultBoilingPoint . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>eau=' . $defaultWaterSol . ', ' . $defaultOtherSol . '</cell><br /></line><br />';
							$secuData .= '<line><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultName . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultRPhrase . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultSPhrase . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultHazard . '</cell><br /><cell col=&quot;1&quot; row=&quot;1&quot;>' . $defaultCaution . '</cell><br /></line><br />';
						}
					}
					$techData .= '</table><br /><br />';
					echo html_entity_decode(parse(str_replace('&quot;', '"',str_replace('<br />', '',$techData)))) . "<br />";
					
					$secuData .= '</table>';
					echo html_entity_decode(parse(str_replace('&quot;', '"',str_replace('<br />', '',$secuData))));
					
					$techData = $content = str_replace('<br />', '\n', addslashes($techData));
					$secuData = $content = str_replace('<br />', '\n', addslashes($secuData));
					echo '<img class="editechButtonImg" src="./../' . $userDesignPath . '/editech/' . $language . '/insert.png" title="' . $message['info_insert'] . '" onclick="opener.insertTag(\'' . $techData . $secuData . '\',\'\',\'textarea\',\'flash\')" alt="" />';
					
					if (count($_SESSION[$sessionPage . 'select']) > 0)
						echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;remove">' . $message['button_msds_remove_all'] . '</a>';
				}
			echo '</div>';
			
			//-----------------------------------------------------------------
			// Classement des fichiers
			//-----------------------------------------------------------------
			
			// Mise à jour des information sur le fichiers
			$files = testDir($dir);		// Lecture du dossier (définit dans fonction.php)
			$nbFiles = nbFolder($dir);	// Compter un nombre de dossiers sans les sous-dossiers (définit dans fonction.php)
			
			// Déclaration des tableaux de classement
			$tableName 		= array();
			$tableFormula	= array();
			$tableCASNumber = array();
			$tableECNumber	= array();
			
			if (isset($_POST['search']))
				$search = htmlentities($_POST['search']);
			elseif (isset($_GET['search']))
				$search = htmlentities($_GET['search']);
			else
				$search = '';
			
			// Recherche parmis les titres
			if ($search != '') {
				$search = str_replace("\\", "\\\\", $search);
				$search = str_replace("#", "\#", $search);
				$search = str_replace("-", "\-", $search);
				$search = str_replace(",", "\,", $search);
				$search = str_replace("+", "\+", $search);
				$search = str_replace("[", "\[", $search);
				$search = str_replace("]", "\]", $search);
				$search = str_replace("(", "\(", $search);
				$search = str_replace(")", "\)", $search);
				$search = str_replace(".", "\.", $search);
				$search = str_replace("'", "\'", $search);
				$search = str_replace("*", ".*", $search);
			}
			
			// Boucle de liste des fichiers pour trier les données
			$j = 0;
			for ($i=0; $i<$nbFiles; $i++) {
				$file = $dir . '/' . $files[$i]; // On détermine le chemin du fichier
				
				// Lecture des propriétés de la page
				$openProperties = fopen($file . '/msds.txt', 'r');
				if ($openProperties) {
					$defaultProperties = fgets($openProperties, 4096);
					fclose($openProperties);
					$defaultName		= preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties)				? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) 				: '';
					$defaultSynonyms	= preg_match('#.*\[synonyms\](.+)\[/synonyms\].*#i', $defaultProperties)		? preg_replace('#.*\[synonyms\](.+)\[/synonyms\].*#i', '$1', $defaultProperties) 		: '';
					$defaultFormula		= preg_match('#.*\[formula\](.+)\[/formula\].*#i', $defaultProperties)			? preg_replace('#.*\[formula\](.+)\[/formula\].*#i', '$1', $defaultProperties) 			: '';
					$defaultCASNumber	= preg_match('#.*\[CAS_number\](.+)\[/CAS_number\].*#i', $defaultProperties)	? preg_replace('#.*\[CAS_number\](.+)\[/CAS_number\].*#i', '$1', $defaultProperties)	: '';
					$defaultECNumber	= preg_match('#.*\[EC_number\](.+)\[/EC_number\].*#i', $defaultProperties)		? preg_replace('#.*\[EC_number\](.+)\[/EC_number\].*#i', '$1', $defaultProperties) 		: '';
					
					if (isset($_POST['from']) && ($_POST['from'] == 'formula'))
					$from = $defaultFormula;
					elseif (isset($_POST['from']) && ($_POST['from'] == 'CAS_number'))
						$from = $defaultCASNumber;
					elseif (isset($_POST['from']) && ($_POST['from'] == 'EC_number'))
						$from = $defaultECNumber;
					else
						$from = $defaultName;
					
					// Bouclage des titres principaux
					if (($search != '' && preg_match('#' . $search . '#i', $from)) || !$search != '') {
						$tableId[$j]		= $files[$i];				// Id du produit
						$tableName[$j] 		= strtolower($defaultName);	// Enregistrement du nom du produit
						$tableFormula[$j] 	= $defaultFormula;			// Enregistrement du numéro CAS du produit
						$tableCASNumber[$j]	= $defaultCASNumber;		// Enregistrement du numéro CAS du produit
						$tableECNumber[$j] 	= $defaultECNumber;			// Enregistrement du numéro CE (EINECS)
					}
					
					// Bouclage des titres alternatifs
					if ($from == $defaultName || $from == $defaultFormula) {
						$tableSynonyms = explode("_", $defaultSynonyms);
						foreach ($tableSynonyms as $key=>$val) {
							if (($search != '' && preg_match('#' . $search . '#i', $tableSynonyms[$key])) || !$search != '') {
								$j++;
								$tableId[$j]		= $files[$i];				// Id du produit
								$tableName[$j] 		= $tableSynonyms[$key];		// Enregistrement des synonymes du produit
								$tableFormula[$j] 	= $defaultFormula;			// Enregistrement du numéro CAS du produit
								$tableCASNumber[$j]	= $defaultCASNumber;		// Enregistrement du numéro CAS du produit
								$tableECNumber[$j] 	= $defaultECNumber;			// Enregistrement du numéro CE (EINECS)
							}
						}
					}
					$j++;
				}
			}
			
			// Recherche parmis les titres
			if ($search != '') {
				$search = stripslashes($search);
				$search = str_replace(".*", "*", $search);
			}
			
			// On créer un tableau pour englober les tableaux de classement
			$tableSort = array(
				'name'			=> $tableName,
				'formula'		=> $tableFormula,
				'CAS_number'	=> $tableCASNumber,
				'EC_number'		=> $tableECNumber
			);
			
			// On procède à un tri sur le type de données	
			$tableOrder = $tableSort[$_SESSION[$sessionPage . 'order']];
			
			// On procède à un tri alphanumérique
			if ($_SESSION[$sessionPage . 'sort'] == 'desc') arsort($tableOrder);
			else asort($tableOrder);
			
			//-----------------------------------------------------------------
			// Partie Affichage
			//-----------------------------------------------------------------
			
			// Affichage des paramètres de recherche en cours
			if (!empty($_POST['search']) )
				$search = $_POST['search'];
			elseif (!empty($_GET['search']) )
				$search = $_GET['search'];
			else
				$search = '';
			
			// Passage des paramètres de recherche en cours dans les liens
			if (!empty($_POST['search']) || !empty($_GET['search']))
				$searchLink = '&amp;search=' . $search;
			else
				$searchLink = '';
				
			
			// Affichage des résultats
			echo '<div class="pageList">
				<form class="pageListForm" action="./index.php?msds&amp;path=' . $_GET['path'] . '" method="post">
					<div>
						Moteur de recherche de datasheet : utiliser * comme jocker de recherche<br />
						'.count($tableSort['name']).' resulats correspondent à votre recherche.<br />
						<form method="post" action="./index.php?msds&amp;path=' . $_GET['path'] . '">
							<input type="text" name="search" value="'.$search.'" size="40" maxlength="100" />
							<select name="from">
								<option value="name">name</option>
								<option value="formula">formula</option>
								<option value="CAS_number">CAS_number</option>
								<option value="EC_number">EC_number</option>
							</select>
							<input type="submit" value="Recherche" />
						</form>
					</div>
					<div style="margin-top: 10px;">
						<table cellspacing="0px" class="pageListLegendTable">
							<tr class="pageListLegendRow">
								<td class="pageListLegendCell" style="width: 400px;">';
									if ($_SESSION[$sessionPage . 'order'] == 'name') {
										if ($_SESSION[$sessionPage . 'sort'] == 'desc')
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=asc' . $searchLink . '">' . $message['legend_name'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
										else
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=desc' . $searchLink . '">' . $message['legend_name'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
									} else {
										echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=name&amp;sort=asc' . $searchLink . '">' . $message['legend_name'] . '</a>';
									}
								echo '</td>
								<td class="pageListLegendCell" style="width: 120px;">';
									if ($_SESSION[$sessionPage . 'order'] == 'formula') {
										if ($_SESSION[$sessionPage . 'sort'] == 'desc')
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=formula&amp;sort=asc' . $searchLink . '">' . $message['legend_formula'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
										else
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=formula&amp;sort=desc' . $searchLink . '">' . $message['legend_formula'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
									} else {
										echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=formula&amp;sort=asc' . $searchLink . '">' . $message['legend_formula'] . '</a>';
									}
								echo '</td>
								<td class="pageListLegendCell" style="width: 100px;">';
									if ($_SESSION[$sessionPage . 'order'] == 'CAS_number') {
										if ($_SESSION[$sessionPage . 'sort'] == 'desc')
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=CAS_number&amp;sort=asc' . $searchLink . '">' . $message['legend_CAS_number'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
										else
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=CAS_number&amp;sort=desc' . $searchLink . '">' . $message['legend_CAS_number'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
									} else {
										echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=CAS_number&amp;sort=asc' . $searchLink . '">' . $message['legend_CAS_number'] . '</a>';
									}
								echo '</td>
								<td class="pageListLegendCell">';
									if ($_SESSION[$sessionPage . 'order'] == 'EC_number') {
										if ($_SESSION[$sessionPage . 'sort'] == 'desc')
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=EC_number&amp;sort=asc' . $searchLink . '">' . $message['legend_EC_number'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
										else
											echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=EC_number&amp;sort=desc' . $searchLink . '">' . $message['legend_EC_number'] . ' <img class="legendSortButton" src="./../' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
									} else {
										echo '<a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;order=EC_number&amp;sort=asc' . $searchLink . '">' . $message['legend_EC_number'] . '</a>';
									}
								echo '</td>
							</tr>
						</table>
					</div>
					<div class="pageListDisplay">';
					if (count($tableOrder) == 0) {
						echo $message['message_no_find']; // Affichage d'un message dans le cas ou le dossier est vide
					} else {
						echo '<table cellspacing="0px" class="pageListTable">';
						$i=0; // Raz du compteur
						foreach ($tableOrder as $key=>$val) {
							if (($i%2) == 0)
								echo '<tr class="pageListRowColor1">';
							else
								echo '<tr class="pageListRowColor2">';
							echo '<td class="pageListCell" style="width: 400px;"><a href="./index.php?msds&amp;path=' . $_GET['path'] . '&amp;id=' . $tableId[$key] . '">' . ucfirst($tableName[$key]) . '</a></td>
								<td class="pageListCell" style="width: 120px;">' . preg_replace('#(\d+)#i', '<sub>$1</sub>',$tableFormula[$key]) . '</td>
								<td class="pageListCell" style="width: 100px;">' . $tableCASNumber[$key] . '</td>
								<td class="pageListCell">' . $tableECNumber[$key] . '</td>
							</tr>';
							$i++;
						}
						echo '</table>';
					}
				echo '</form>
			</div>';
			
			echo '<div class="pageList">
				Si le produit que vous recherchez n\'existe pas vous devez l\'enregistrer avec l\'éditeur de msds
			</div>';
		}
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