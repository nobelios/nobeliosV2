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
	$dirTemp		= './web/' . $language . '/msds/temp/' . $userId;	// Chemin d'accès aux fichiers temporaires
	$dirWorks		= './web/' . $language . '/msds/works';	// Chemin d'accès aux pages
	$folderTemp		= testDir($dirTemp);
	$folderWorks	= testDir($dirWorks);
	$nbWork			= nbFolder($dirTemp); // Compte le nombre de dossier utilisateur
	
	// Aucune alerte par défaut
	$alert = null;

	// Définition de l'id du dossier
	$folderId = isset($_GET['id']) ? $_GET['id'] : '';
	
	//------------------------------------------------------------------
	// Définition de l'affichage
	//------------------------------------------------------------------
	
	// Suffix pour les sessions
	$sessionPage = 'user_msds_editor_';
	
	// Options de classement pour les fichiers utilisateur (enregistrement en session)
	if (!isset($_SESSION[$sessionPage . 'order']))																														$_SESSION[$sessionPage . 'order']	= 'name';
	if (!isset($_SESSION[$sessionPage . 'sort']))																														$_SESSION[$sessionPage . 'sort'] 	= 'asc';
	if (isset($_GET['order']) && (($_GET['order'] == 'name') || ($_GET['order'] == 'formula') || ($_GET['order'] == 'cas_number') || ($_GET['order'] == 'request')))	$_SESSION[$sessionPage . 'order'] 	= $_GET['order'];
	if (isset($_GET['sort']) && (($_GET['sort'] == 'desc') || ($_GET['sort'] == 'asc')))																				$_SESSION[$sessionPage . 'sort'] 	= $_GET['sort'];
	
	//-----------------------------------------------------------------
	// Définition des variables contenant les données de la page
	//-----------------------------------------------------------------

	// Valeurs par défaut
	$defaultName			= '';
	$defaultSynonyms		= '';
	$defaultType			= '';
	$defaultSynthesis		= '';
	$defaultCASNumber		= '';
	$defaultECNumber		= '';
	$defaultFormula			= '';
	$defaultSmile			= '';
	$defaultRPhrase			= '';
	$defaultSPhrase			= '';
	$defaultDensity			= '';
	$defaultMolarMass		= '';
	$defaultPH				= '';
	$defaultForm			= '';
	$defaultColor			= '';
	$defaultOdour			= '';
	$defaultMeltingPoint	= '';
	$defaultBoilingPoint	= '';
	$defaultVaporPressure	= '';
	$defaultFlashPoint		= '';
	$defaultThermalDec		= '';
	$defaultRefractiveIndex	= '';
	$defaultIgnitionTemp	= '';
	$defaultWaterSol		= '';
	$defaultOtherSol		= '';
	$defaultImage			= '';
	$defaultDL50Oral		= '';
	$defaultDL50Dermal		= '';
	$defaultHazard			= '';
	$defaultCaution			= '';
	$defaultOldPictogram	= '';
	$defaultNewPictogram	= '';
	$defaultUse				= '';
	$defaultAuthor			= '';
	$defaultRequest			= 'works';
	
	// Définition par lecture de données existantes
	if (in_array($folderId, $folderTemp)) {
		// Lecture de la fiche
		$openProperties = fopen($dirTemp . '/' . $folderId . '/msds.txt', 'r');
		if ($openProperties) {
			$defaultProperties = fgets($openProperties, 4096);
			fclose($openProperties);
			
			// Définition des données par défaut
			$defaultName			= preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) 							? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties)							: $defaultName;
			$defaultSynonyms		= preg_match('#.*\[synonyms\](.+)\[/synonyms\].*#i', $defaultProperties) 					? preg_replace('#.*\[synonyms\](.+)\[/synonyms\].*#i', '$1', $defaultProperties) 					: $defaultSynonyms;
			$defaultType			= preg_match('#.*\[type\](.+)\[/type\].*#i', $defaultProperties)							? preg_replace('#.*\[type\](.+)\[/type\].*#i', '$1', $defaultProperties) 							: $defaultType;		
			$defaultSynthesis		= preg_match('#.*\[synthesis\](.+)\[/synthesis\].*#i', $defaultProperties) 					? preg_replace('#.*\[synthesis\](.+)\[/synthesis\].*#i', '$1', $defaultProperties) 					: $defaultSynthesis;
			$defaultCASNumber		= preg_match('#.*\[CAS_number\](.+)\[/CAS_number\].*#i', $defaultProperties) 				? preg_replace('#.*\[CAS_number\](.+)\[/CAS_number\].*#i', '$1', $defaultProperties) 				: $defaultCASNumber;
			$defaultECNumber		= preg_match('#.*\[EC_number\](.+)\[/EC_number\].*#i', $defaultProperties) 					? preg_replace('#.*\[EC_number\](.+)\[/EC_number\].*#i', '$1', $defaultProperties) 					: $defaultECNumber;
			$defaultFormula			= preg_match('#.*\[formula\](.+)\[/formula\].*#i', $defaultProperties) 						? preg_replace('#.*\[formula\](.+)\[/formula\].*#i', '$1', $defaultProperties) 						: $defaultFormula;
			$defaultSmile 			= preg_match('#.*\[smile\](.+)\[/smile\].*#i', $defaultProperties) 							? preg_replace('#.*\[smile\](.+)\[/smile\].*#i', '$1', $defaultProperties) 							: $defaultSmile;
			$defaultRPhrase			= preg_match('#.*\[R_phrase\](.+)\[/R_phrase\].*#i', $defaultProperties) 					? preg_replace('#.*\[R_phrase\](.+)\[/R_phrase\].*#i', '$1', $defaultProperties) 					: $defaultRPhrase;
			$defaultSPhrase			= preg_match('#.*\[S_phrase\](.+)\[/S_phrase\].*#i', $defaultProperties) 					? preg_replace('#.*\[S_phrase\](.+)\[/S_phrase\].*#i', '$1', $defaultProperties) 					: $defaultSPhrase;
			$defaultDensity			= preg_match('#.*\[density\](.+)\[/density\].*#i', $defaultProperties) 						? preg_replace('#.*\[density\](.+)\[/density\].*#i', '$1', $defaultProperties) 						: $defaultDensity;
			$defaultMolarMass		= preg_match('#.*\[molar_mass\](.+)\[/molar_mass\].*#i', $defaultProperties) 				? preg_replace('#.*\[molar_mass\](.+)\[/molar_mass\].*#i', '$1', $defaultProperties) 				: $defaultMolarMass;
			$defaultPH				= preg_match('#.*\[pH\](.+)\[/pH\].*#i', $defaultProperties) 								? preg_replace('#.*\[pH\](.+)\[/pH\].*#i', '$1', $defaultProperties) 								: $defaultPH;
			$defaultForm 			= preg_match('#.*\[form\](.+)\[/form\].*#i', $defaultProperties) 							? preg_replace('#.*\[form\](.+)\[/form\].*#i', '$1', $defaultProperties) 							: $defaultForm;
			$defaultColor			= preg_match('#.*\[color\](.+)\[/color\].*#i', $defaultProperties) 							? preg_replace('#.*\[color\](.+)\[/color\].*#i', '$1', $defaultProperties) 							: $defaultColor;
			$defaultOdour			= preg_match('#.*\[odour\](.+)\[/odour\].*#i', $defaultProperties) 							? preg_replace('#.*\[odour\](.+)\[/odour\].*#i', '$1', $defaultProperties) 							: $defaultOdour;	
			$defaultMeltingPoint	= preg_match('#.*\[melting_point\](.+)\[/melting_point\].*#i', $defaultProperties) 			? preg_replace('#.*\[melting_point\](.+)\[/melting_point\].*#i', '$1', $defaultProperties) 			: $defaultMeltingPoint;
			$defaultBoilingPoint	= preg_match('#.*\[boiling_point\](.+)\[/boiling_point\].*#i', $defaultProperties) 			? preg_replace('#.*\[boiling_point\](.+)\[/boiling_point\].*#i', '$1', $defaultProperties) 			: $defaultBoilingPoint;
			$defaultVaporPressure	= preg_match('#.*\[vapor_pressure\](.+)\[/vapor_pressure\].*#i', $defaultProperties) 		? preg_replace('#.*\[vapor_pressure\](.+)\[/vapor_pressure\].*#i', '$1', $defaultProperties) 		: $defaultVaporPressure;
			$defaultFlashPoint 		= preg_match('#.*\[flash_point\](.+)\[/flash_point\].*#i', $defaultProperties) 				? preg_replace('#.*\[flash_point\](.+)\[/flash_point\].*#i', '$1', $defaultProperties) 				: $defaultFlashPoint;
			$defaultThermalDec		= preg_match('#.*\[thermal_dec\](.+)\[/thermal_dec\].*#i', $defaultProperties) 				? preg_replace('#.*\[thermal_dec\](.+)\[/thermal_dec\].*#i', '$1', $defaultProperties) 				: $defaultThermalDec;
			$defaultRefractiveIndex	= preg_match('#.*\[refractive_index\](.+)\[/refractive_index\].*#i', $defaultProperties) 	? preg_replace('#.*\[refractive_index\](.+)\[/refractive_index\].*#i', '$1', $defaultProperties)	: $defaultRefractiveIndex;
			$defaultIgnitionTemp	= preg_match('#.*\[ignition_temp\](.+)\[/ignition_temp\].*#i', $defaultProperties) 			? preg_replace('#.*\[ignition_temp\](.+)\[/ignition_temp\].*#i', '$1', $defaultProperties) 			: $defaultIgnitionTemp;
			$defaultWaterSol		= preg_match('#.*\[water_sol\](.+)\[/water_sol\].*#i', $defaultProperties) 					? preg_replace('#.*\[water_sol\](.+)\[/water_sol\].*#i', '$1', $defaultProperties) 					: $defaultWaterSol;
			$defaultOtherSol		= preg_match('#.*\[other_sol\](.+)\[/other_sol\].*#i', $defaultProperties) 					? preg_replace('#.*\[other_sol\](.+)\[/other_sol\].*#i', '$1', $defaultProperties) 					: $defaultOtherSol;
			$defaultImage 			= preg_match('#.*\[image\](.+)\[/image\].*#i', $defaultProperties) 							? preg_replace('#.*\[image\](.+)\[/image\].*#i', '$1', $defaultProperties) 							: $defaultImage;
			$defaultDL50Oral		= preg_match('#.*\[DL50_oral\](.+)\[/DL50_oral\].*#i', $defaultProperties) 					? preg_replace('#.*\[DL50_oral\](.+)\[/DL50_oral\].*#i', '$1', $defaultProperties)					: $defaultDL50Oral;
			$defaultDL50Dermal		= preg_match('#.*\[DL50_dermal\](.+)\[/DL50_dermal\].*#i', $defaultProperties) 				? preg_replace('#.*\[DL50_dermal\](.+)\[/DL50_dermal\].*#i', '$1', $defaultProperties) 				: $defaultDL50Dermal;
			$defaultHazard			= preg_match('#.*\[hazard\](.+)\[/hazard\].*#i', $defaultProperties) 						? preg_replace('#.*\[hazard\](.+)\[/hazard\].*#i', '$1', $defaultProperties) 						: $defaultHazard;
			$defaultCaution			= preg_match('#.*\[caution\](.+)\[/caution\].*#i', $defaultProperties) 						? preg_replace('#.*\[caution\](.+)\[/caution\].*#i', '$1', $defaultProperties) 						: $defaultCaution;
			$defaultOldPictogram	= preg_match('#.*\[old_pictogram\](.+)\[/old_pictogram\].*#i', $defaultProperties) 			? preg_replace('#.*\[old_pictogram\](.+)\[/old_pictogram\].*#i', '$1', $defaultProperties) 			: $defaultOldPictogram;
			$defaultNewPictogram	= preg_match('#.*\[new_pictogram\](.+)\[/new_pictogram\].*#i', $defaultProperties) 			? preg_replace('#.*\[new_pictogram\](.+)\[/new_pictogram\].*#i', '$1', $defaultProperties) 			: $defaultNewPictogram;
			$defaultUse 			= preg_match('#.*\[use\](.+)\[/use\].*#i', $defaultProperties) 								? preg_replace('#.*\[use\](.+)\[/use\].*#i', '$1', $defaultProperties) 								: $defaultUse;
			$defaultAuthor 			= preg_match('#.*\[author\](.+)\[/author\].*#i', $defaultProperties) 						? preg_replace('#.*\[author\](.+)\[/author\].*#i', '$1', $defaultProperties) 						: $defaultAuthor;
			$defaultRequest 		= preg_match('#.*\[request\](.+)\[/request\].*#i', $defaultProperties) 						? preg_replace('#.*\[request\](.+)\[/request\].*#i', '$1', $defaultProperties) 						: $defaultRequest;
		}
	}
	
	// Mémorisation des anciens pictrogrammes (ancienne norme)
	if (isset($_POST['name']) && isset($_POST['old_pictogram'])) {
		$defaultOldPictogram = '';
		foreach ($_POST['old_pictogram'] as $key => $val)
			$defaultOldPictogram .= $_POST['old_pictogram'][$key] . '_';
		$defaultOldPictogram = $defaultOldPictogram;
	}
	elseif (isset($_POST['name']) && !isset($_POST['old_pictogram']))
		$defaultOldPictogram = '';
	
	// Mémorisation des nouveaux pictogrammes (nouvelle norme)
	if (isset($_POST['name']) && isset($_POST['new_pictogram'])) {
		$defaultNewPictogram = '';
		foreach ($_POST['new_pictogram'] as $key => $val)
			$defaultNewPictogram .= $_POST['new_pictogram'][$key] . '_';
		$defaultNewPictogram = $defaultNewPictogram;
	}
	elseif (isset($_POST['name']) && !isset($_POST['new_pictogram']))
		$defaultNewPictogram = '';
	
	// Mise en mémoire des données postées
	$defaultName			= isset($_POST['name']) 			? $_POST['name']				: $defaultName;
	$defaultSynonyms		= isset($_POST['synonyms']) 		? $_POST['synonyms'] 			: $defaultSynonyms;
	$defaultType			= isset($_POST['type'])				? $_POST['type'] 				: $defaultType;
	$defaultSynthesis		= isset($_POST['synthesis']) 		? $_POST['synthesis'] 			: $defaultSynthesis;
	$defaultCASNumber		= isset($_POST['CAS_number'])	 	? $_POST['CAS_number'] 			: $defaultCASNumber;
	$defaultECNumber		= isset($_POST['EC_number']) 		? $_POST['EC_number'] 			: $defaultECNumber;
	$defaultFormula		 	= isset($_POST['formula']) 			? $_POST['formula'] 			: $defaultFormula;
	$defaultSmile			= isset($_POST['smile']) 			? $_POST['smile'] 				: $defaultSmile;
	$defaultRPhrase			= isset($_POST['R_phrase']) 		? $_POST['R_phrase'] 			: $defaultRPhrase;
	$defaultSPhrase			= isset($_POST['S_phrase']) 		? $_POST['S_phrase'] 			: $defaultSPhrase;
	$defaultDensity			= isset($_POST['density'])			? $_POST['density'] 			: $defaultDensity;
	$defaultMolarMass		= isset($_POST['molar_mass']) 		? $_POST['molar_mass'] 			: $defaultMolarMass;
	$defaultPH				= isset($_POST['pH']) 				? $_POST['pH'] 					: $defaultPH;
	$defaultForm			= isset($_POST['form']) 			? $_POST['form'] 				: $defaultForm;
	$defaultColor 			= isset($_POST['color']) 			? $_POST['color'] 				: $defaultColor;
	$defaultOdour			= isset($_POST['odour']) 			? $_POST['odour'] 				: $defaultOdour;
	$defaultMeltingPoint	= isset($_POST['melting_point']) 	? $_POST['melting_point'] 		: $defaultMeltingPoint;
	$defaultBoilingPoint	= isset($_POST['boiling_point']) 	? $_POST['boiling_point'] 		: $defaultBoilingPoint;
	$defaultVaporPressure	= isset($_POST['vapor_pressure'])	? $_POST['vapor_pressure'] 		: $defaultVaporPressure;
	$defaultFlashPoint		= isset($_POST['flash_point']) 		? $_POST['flash_point'] 		: $defaultFlashPoint;
	$defaultThermalDec		= isset($_POST['thermal_dec']) 		? $_POST['thermal_dec'] 		: $defaultThermalDec;
	$defaultRefractiveIndex	= isset($_POST['refractive_index'])	? $_POST['refractive_index'] 	: $defaultRefractiveIndex;
	$defaultIgnitionTemp 	= isset($_POST['ignition_temp']) 	? $_POST['ignition_temp'] 		: $defaultIgnitionTemp;
	$defaultWaterSol		= isset($_POST['water_sol']) 		? $_POST['water_sol'] 			: $defaultWaterSol;
	$defaultOtherSol		= isset($_POST['other_sol']) 		? $_POST['other_sol'] 			: $defaultOtherSol;
	$defaultImage			= isset($_POST['image']) 			? $_POST['image'] 				: $defaultImage;
	$defaultDL50Oral		= isset($_POST['DL50_oral'])		? $_POST['DL50_oral'] 			: $defaultDL50Oral;
	$defaultDL50Dermal		= isset($_POST['DL50_dermal']) 		? $_POST['DL50_dermal'] 		: $defaultDL50Dermal;
	$defaultHazard			= isset($_POST['hazard']) 			? $_POST['hazard'] 				: $defaultHazard;
	$defaultCaution			= isset($_POST['caution']) 			? $_POST['caution'] 			: $defaultCaution;
	$defaultUse				= isset($_POST['use']) 				? $_POST['use'] 				: $defaultUse;
	
	//-----------------------------------------------------------------
	// On veut ajouter ou éditer une page
	//-----------------------------------------------------------------

	if ((isset($_GET['new']) && ($nbWork < $MSDSLimit)) || (isset($_GET['edit']))) {
		// Création ou modification d'une fiche de sécurité
		if (!empty($_POST['name']) && !empty($_POST['formula']) && !empty($_POST['CAS_number']) && !empty($_POST['EC_number'])) {
			// Création des fichiers
			if ((!in_array($folderId, $folderTemp)) && ($nbWork < $MSDSLimit)) {
				// Création d'un dossier dans le fichier des travaux
				// Génération de nom de page jusqu'à ce qu'on trouve un disponible
				do {
					$folderId = randomHex();
					$reqName = in_array($folderId, $folderTemp);
				} while ($reqName == true);
				
				// Création des dossiers
				mkdir($dirTemp . '/' . $folderId, '0777');					// Création de la racine de la page
				mkdir($dirTemp . '/' . $folderId . '/images', '0777');		// Création du conteneur d'image
				
				// Attribution de l'id de l'auteur
				$MSDS = fopen($dirTemp.'/'.$folderId.'/msds.txt', 'w+');
				$MSDSBases = preg_replace('#\[author\].*\[/author\]#i', '[author]' . $userId . '[/author]' , $MSDSBases);
				fputs($MSDS, $MSDSBases);
				fclose($MSDS);
			}
			
			// Création de la fiche de sécurité
			$MSDS = fopen($dirTemp.'/'.$folderId.'/msds.txt', 'w');
			$MSDSBases = preg_replace('#\[name\].*\[/name\]#i', '[name]' . $_POST['name'] . '[/name]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[synonyms\].*\[/synonyms\]#i', '[synonyms]' . $_POST['synonyms'] . '[/synonyms]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[type\].*\[/type\]#i', '[type]' . $_POST['type'] . '[/type]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[synthesis\].*\[/synthesis\]#i', '[synthesis]' . $_POST['synthesis'] . '[/synthesis]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[CAS_number\].*\[/CAS_number\]#i', '[CAS_number]' . $_POST['CAS_number'] . '[/CAS_number]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[EC_number\].*\[/EC_number\]#i', '[EC_number]' . $_POST['EC_number'] . '[/EC_number]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[formula\].*\[/formula\]#i', '[formula]' . $_POST['formula'] . '[/formula]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[smile\].*\[/smile\]#i', '[smile]' . $_POST['smile'] . '[/smile]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[R_phrase\].*\[/R_phrase\]#i', '[R_phrase]' . $_POST['R_phrase'] . '[/R_phrase]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[S_phrase\].*\[/S_phrase\]#i', '[S_phrase]' . $_POST['S_phrase'] . '[/S_phrase]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[density\].*\[/density\]#i', '[density]' . $_POST['density'] . '[/density]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[molar_mass\].*\[/molar_mass\]#i', '[molar_mass]' . $_POST['molar_mass'] . '[/molar_mass]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[pH\].*\[/pH\]#i', '[pH]' . $_POST['pH'] . '[/pH]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[form\].*\[/form\]#i', '[form]' . $_POST['form'] . '[/form]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[color\].*\[/color\]#i', '[color]' . $_POST['color'] . '[/color]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[odour\].*\[/odour\]#i', '[odour]' . $_POST['odour'] . '[/odour]' , $MSDSBases);	
			$MSDSBases = preg_replace('#\[melting_point\].*\[/melting_point\]#i', '[melting_point]' . $_POST['melting_point'] . '[/melting_point]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[boiling_point\].*\[/boiling_point\]#i', '[boiling_point]' . $_POST['boiling_point'] . '[/boiling_point]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[vapor_pressure\].*\[/vapor_pressure\]#i', '[vapor_pressure]' . $_POST['vapor_pressure'] . '[/vapor_pressure]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[flash_point\].*\[/flash_point\]#i', '[flash_point]' . $_POST['flash_point'] . '[/flash_point]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[thermal_dec\].*\[/thermal_dec\]#i', '[thermal_dec]' . $_POST['thermal_dec'] . '[/thermal_dec]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[refractive_index\].*\[/refractive_index\]#i', '[refractive_index]' . $_POST['refractive_index'] . '[/refractive_index]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[ignition_temp\].*\[/ignition_temp\]#i', '[ignition_temp]' . $_POST['ignition_temp'] . '[/ignition_temp]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[water_sol\].*\[/water_sol\]#i', '[water_sol]' . $_POST['water_sol'] . '[/water_sol]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[other_sol\].*\[/other_sol\]#i', '[other_sol]' . $_POST['other_sol'] . '[/other_sol]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[image\].*\[/image\]#i', '[image]' . $_POST['image'] . '[/image]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[DL50_oral\].*\[/DL50_oral\]#i', '[DL50_oral]' . $_POST['DL50_oral'] . '[/DL50_oral]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[DL50_dermal\].*\[/DL50_dermal\]#i', '[DL50_dermal]' . $_POST['DL50_dermal'] . '[/DL50_dermal]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[hazard\].*\[/hazard\]#i', '[hazard]' . $defaultHazard . '[/hazard]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[caution\].*\[/caution\]#i', '[caution]' . $defaultCaution . '[/caution]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[old_pictogram\].*\[/old_pictogram\]#i', '[old_pictogram]' . $defaultOldPictogram . '[/old_pictogram]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[new_pictogram\].*\[/new_pictogram\]#i', '[new_pictogram]' . $defaultNewPictogram . '[/new_pictogram]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[use\].*\[/use\]#i', '[use]' . $defaultUse . '[/use]' , $MSDSBases);
			$MSDSBases = preg_replace('#\[request\].*\[/request\]#i', '[request]works[/request]' , $MSDSBases);
			$MSDSBases = htmlentities($MSDSBases);
			fputs($MSDS, $MSDSBases);
			fclose($MSDS);
		}
		
		// On affiche le formulaire d'édition d'une fiche de sécurité
		else {
			$folderDir = './web/' . $language . '/temp/' . $userId . '/' . $folderId;
			
			// affichage des erreurs
			if (isset($_POST['name']) && isset($_POST['formula']) && isset($_POST['CAS_number']) && isset($_POST['EC_number'])) {
				// Message d'erreur
				$erroralert		= $message['alert_form_empty_field'] . '<br />';
				$alert			= $erroralert;
				// Vérification des champs de formulaire (indique les champs vides)
				if ($defaultName == '')			$alert .= $message['alert_form_empty_subject'] . '<br />';
				if ($defaultFormula == '')		$alert .= $message['alert_form_empty_type'] . '<br />';
				if ($defaultCASNumber == '')	$alert .= $message['alert_form_empty_title'] . '<br />';
				if ($defaultECNumber == '')		$alert .= $message['alert_form_empty_work'] . '<br />';
			}
			
			// Affichage du Linker
			echo '<div class="pageLinker">
				<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_msds_editor">' . $message['navigation_tree_make_msds'] . '</a>&nbsp;>&nbsp;
				<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '">' . $message['navigation_tree_make_msds_optn'] . '</a>&nbsp;>&nbsp;
				' . $message['navigation_tree_make_msds_edit'] . '
			</div>';
			
			// Affichage des informations
			echo '<div class="pageFolderInfo">
				<div class="pageImage">
					<img src="' . $userDesignPath . '/images/make_msds.png" alt="" />
				</div>
				<div class="pageText">
					' . $message['legend_msds_edit'] . '<br />
					<a href="./index.php?user_msds_editor">' . $message['action_back_to_options'] . '</a><br />
					<a href="./index.php?user_msds_editor">' . $message['action_back_to_work'] . '</a>
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
				<form method="post" id="work_page" action="./index.php?user_msds_editor&amp;id=' . $folderId . '&amp;edit">
					' . $message['legend_msds_general_data'] . '
					<table style="margin-bottom: 10px; border: 1px solid black; width: 100%">
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="name">' . $message['legend_msds_view_name'] . '</label></td>
							<td><input type="text" id="name" name="name" size="50" maxlength="100" value="' . $defaultName . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="synonyms">' . $message['legend_msds_view_synonyms'] . '</label></td>
							<td><input type="text" id="synonyms" name="synonyms" size="50" maxlength="200" value="' . $defaultSynonyms . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="type">' . $message['legend_msds_view_type'] . '</label></td>
							<td><input type="text" id="type" name="type" size="50" maxlength="100" value="' . $defaultType . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="synthesis">' . $message['legend_msds_view_synthesis'] . '</label></td>
							<td><textarea id="synthesis" name="synthesis" cols="50" rows="6">' . $defaultSynthesis . '</textarea></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="formula">' . $message['legend_msds_view_formula'] . '</label></td>
							<td><input type="text" id="formula" name="formula" size="50" maxlength="100" value="' . $defaultFormula . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="smile">' . $message['legend_msds_view_smile'] . '</label></td>
							<td><input type="text" id="smile" name="smile" size="50" maxlength="100" value="' . $defaultSmile . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="image">' . $message['legend_msds_view_image'] . '</label></td>
							<td><input type="text" id="image" name="image" size="50" maxlength="100" value="' . $defaultImage . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="use">' . $message['legend_msds_view_use'] . '</label></td>
							<td><input type="text" id="use" name="use" size="50" maxlength="100" value="' . $defaultUse . '" /></td>
						</tr>
					</table>
					' . $message['legend_msds_physical_data'] . '
					<table style="margin-bottom: 10px; border: 1px solid black; width: 100%">
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="density">' . $message['legend_msds_view_density'] . '</label></td>
							<td><input type="text" id="density" name="density" size="50" maxlength="100" value="' . $defaultDensity . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="molar_mass">' . $message['legend_msds_view_molar_mass'] . '</label></td>
							<td><input type="text" id="molar_mass" name="molar_mass" size="50" maxlength="100" value="' . $defaultMolarMass . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="pH">' . $message['legend_msds_view_pKa'] . '</label></td>
							<td><input type="text" id="pH" name="pH" size="50" maxlength="100" value="' . $defaultPH . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="form">' . $message['legend_msds_view_form'] . '</label></td>
							<td><input type="text" id="form" name="form" size="50" maxlength="100" value="' . $defaultForm . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="color">' . $message['legend_msds_view_color'] . '</label></td>
							<td><input type="text" id="color" name="color" size="50" maxlength="100" value="' . $defaultColor . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="odour">' . $message['legend_msds_view_odour'] . '</label></td>
							<td><input type="text" id="odour" name="odour" size="50" maxlength="100" value="' . $defaultOdour . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="melting_point">' . $message['legend_msds_view_melting_point'] . '</label></td>
							<td><input type="text" id="melting_point" name="melting_point" size="50" maxlength="100" value="' . $defaultMeltingPoint . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="boiling_point">' . $message['legend_msds_view_boiling_point'] . '</label></td>
							<td><input type="text" id="boiling_point" name="boiling_point" size="50" maxlength="100" value="' . $defaultBoilingPoint . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="vapor_pressure">' . $message['legend_msds_view_vapor_pressure'] . '</label></td>
							<td><input type="text" id="vapor_pressure" name="vapor_pressure" size="50" maxlength="100" value="' . $defaultVaporPressure . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="flash_point">' . $message['legend_msds_view_flash_point'] . '</label></td>
							<td><input type="text" id="flash_point" name="flash_point" size="50" maxlength="100" value="' . $defaultFlashPoint . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="thermal_dec">' . $message['legend_msds_view_thermal_dec'] . '</label></td>
							<td><input type="text" id="thermal_dec" name="thermal_dec" size="50" maxlength="100" value="' . $defaultThermalDec . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="refractive_index">' . $message['legend_msds_view_refractive_index'] . '</label></td>
							<td><input type="text" id="refractive_index" name="refractive_index" size="50" maxlength="100" value="' . $defaultRefractiveIndex . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="ignition_temp">' . $message['legend_msds_view_ignition_temp'] . '</label></td>
							<td><input type="text" id="ignition_temp" name="ignition_temp" size="50" maxlength="100" value="' . $defaultIgnitionTemp . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="water_sol">' . $message['legend_msds_view_water_sol'] . '</label></td>
							<td><input type="text" id="water_sol" name="water_sol" size="50" maxlength="100" value="' . $defaultWaterSol . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="other_sol">' . $message['legend_msds_view_other_sol'] . '</label></td>
							<td><input type="text" id="other_sol" name="other_sol" size="50" maxlength="100" value="' . $defaultOtherSol . '" /></td>
						</tr>
					</table>
					' . $message['legend_msds_security_data'] . '
					<table style="border: 1px solid black; width: 100%">
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="CAS_number">' . $message['legend_msds_view_CAS_number'] . '</label></td>
							<td><input type="text" id="CAS_number" name="CAS_number" size="50" maxlength="100" value="' . $defaultCASNumber . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="EC_number">' . $message['legend_msds_view_EC_number'] . '</label></td>
							<td><input type="text" id="EC_number" name="EC_number" size="50" maxlength="100" value="' . $defaultECNumber . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="DL50_oral">' . $message['legend_msds_view_DL50_oral'] . '</label></td>
							<td><input type="text" id="DL50_oral" name="DL50_oral" size="50" maxlength="100" value="' . $defaultDL50Oral . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="DL50_dermal">' . $message['legend_msds_view_DL50_dermal'] . '</label></td>
							<td><input type="text" id="DL50_dermal" name="DL50_dermal" size="50" maxlength="100" value="' . $defaultDL50Dermal . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="R_phrase">' . $message['legend_msds_view_R_phrase'] . '</label></td>
							<td><input type="text" id="R_phrase" name="R_phrase" size="50" maxlength="100" value="' . $defaultRPhrase . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="S_phrase">' . $message['legend_msds_view_S_phrase'] . '</label></td>
							<td><input type="text" id="S_phrase" name="S_phrase" size="50" maxlength="100" value="' . $defaultSPhrase . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="hazard">' . $message['legend_msds_view_hazard'] . '</label></td>
							<td><input type="text" id="hazard" name="hazard" size="50" maxlength="200" value="' . $defaultHazard . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><label class="pageLabel" for="caution">' . $message['legend_msds_view_caution'] . '</label></td>
							<td><input type="text" id="caution" name="caution" size="50" maxlength="200" value="' . $defaultCaution . '" /></td>
						</tr>
						<tr>
							<td style="width: 250px;"><span class="pageLabel">' . $message['legend_old_pictogram'] . '</span></td>
							<td>
								<table>
									<tr>';
										$tableOldPictogram = explode("_", $defaultOldPictogram);
										$counter=0;
										foreach ($pictoOld as $key=>$val) {
											if ($counter % $formMaxPictoWidth == 0)
												echo '</tr><tr>';
											if (in_array($pictoOld[$key], $tableOldPictogram))
												echo '<td><input checked type="checkbox" name="old_pictogram[]" size="50" maxlength="100" value="' . $pictoOld[$key] . '" id="' . $pictoOld[$key] . '" /></td>';
											else
												echo '<td><input type="checkbox" name="old_pictogram[]" size="50" maxlength="100" value="' . $pictoOld[$key] . '" id="' . $pictoOld[$key] . '" /></td>';
											echo '<td><label class="pageLabel" for="' . $pictoOld[$key] . '"><img src="' . $userDesignPath . '/images/mini_picto_old_' . $pictoOld[$key] . '.png" /></label></td>';
											$counter++;
										}
									echo '</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="width: 250px;"><span class="pageLabel">' . $message['legend_new_pictogram'] . '</span></td>
							<td>
								<table>
									<tr>';
										$tableNewPictogram = explode("_", $defaultNewPictogram);
										$counter=0;
										foreach ($pictoNew as $key=>$val) {
											if ($counter % $formMaxPictoWidth == 0)
												echo '</tr><tr>';
											if (in_array($pictoNew[$key], $tableNewPictogram))
												echo '<td><input checked type="checkbox" name="new_pictogram[]" size="50" maxlength="100" value="' . $pictoNew[$key] . '" id="' . $pictoNew[$key] . '" /></td>';
											else
												echo '<td><input type="checkbox" name="new_pictogram[]" size="50" maxlength="100" value="' . $pictoNew[$key] . '" id="' . $pictoNew[$key] . '" /></td>';
											echo '<td><label class="pageLabel" for="' . $pictoNew[$key] . '"><img src="' . $userDesignPath . '/images/mini_picto_new_' . $pictoNew[$key] . '.png" /></label></td>';
											$counter++;
										}
									echo '</tr>
								</table>
							</td>
						</tr>
					</table>
					<div class="pageButton">
						<button type="submit" class="formButton" name="submit" />
							' . $message['action_send'] . '
						</button>
						&nbsp;/&nbsp;
						<button type="reset" class="formButton" name="reset" />
							' . $message['action_reset'] . '
						</button>
					</div>
				</form>
			</div>';
		}
	}
	
	//-----------------------------------------------------------------
	// On veut supprimer une ou plusieurs fiches de sécurité
	//-----------------------------------------------------------------
	
	// On demande la suppression d'une unique fiche de sécurité
	elseif (isset($_GET['remove']) && in_array($folderId, $folderTemp)) {
		if (isset($_GET['confirm'])) {
			if(clearDir($dirTemp .'/'. $folderId)) {
				$alert .= $message['message_msds_deleted'] . ' :<br />' . $defaultName . '<br />';
			} else {
				$alert .= $message['message_msds_remove_failed'] . ' :<br />' . $defaultName . '<br />';
			}
			$alert .= '<a href="./index.php?user_msds_editor">' . $message['action_close'] . '<br /></a>';
		}
		// On demande la confirmation de la suppression de la fiche de sécurité
		else {
			$alert .= $message['query_remove_following_msds'] . '<br />' . $defaultName . '<br />
						<div class="pageButton">
							<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '&amp;remove&amp;confirm">' . $message['action_confirm_yes'] . '</a>
							&nbsp;/&nbsp;
							<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '">' . $message['action_confirm_no'] . '</a>
						</div>';
		}
	}
	
	// On demande la suppression d'une ou plusieurs fiches de sécurité
	elseif (isset($_POST['remove'])) {
		// On compte le nombre de travaux à supprimer
		$nbWorkRemove = 0;
		for ($i=0; $i<$nbWork; $i++) {
			if (isset($_POST['checkbox' . $i])) {
				$nbWorkRemove++;
			}
		}
		// On confirme la suppression
		if (isset($_POST['confirm'])) {
			$msdsAlert = '';
			$errorMsdsAlert = '';
			// On liste les fiches de sécurité sélectionnées, et on test si elles existent
			for ($i=0; $i<$nbWork; $i++) {
				if (isset($_POST['checkbox' . $i])) {
					if (in_array($_POST['checkbox' . $i], $folderTemp)) {
						$openProperties = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/msds.txt', 'r');
						if ($openProperties) {
							$defaultProperties = fgets($openProperties, 4096);
							fclose($openProperties);
							// Définition des données par défaut
							$defaultName = preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) ? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) : $defaultName;
						}
						$listeArray[] = $_POST['checkbox' . $i];
						// Effacement du répertoire
						if (clearDir($dirTemp . '/' . $_POST['checkbox' . $i]))
							$msdsAlert .= $defaultName . '<br />';
						// Erreur d'effacement
						else 
							$errorMsdsAlert .= $defaultName . '<br />';
					}
				}
			}
			// Erreur
			if ($errorMsdsAlert != '') {
				if ($nbWorkRemove > 1) $alert .= $message['message_msdss_remove_failed'] . ' :<br />' . $errorMsdsAlert;
				else $alert .= $message['message_msds_remove_failed'] . ' :<br />' . $errorMsdsAlert;
			} else {
				if ($nbWorkRemove > 1) $alert .= $message['message_msdss_deleted'] . ' :<br />' . $msdsAlert;
				else $alert .= $message['message_msds_deleted'] . ' :<br />' . $msdsAlert;
			}
			$alert .= '<a href="./index.php?user_msds_editor">' . $message['action_close']  . '<br /></a>';
		// On n'as pas encore confirmé la supression
		} else {
			// Si il y a des travaux à supprimer
			if ($nbWorkRemove != 0) {
				$alert .= '<form action="./index.php?user_msds_editor" method="post">';
				if ($nbWorkRemove > 1) $alert .= $message['query_remove_following_msdss'] . '<br />';
				else $alert .= $message['query_remove_following_msds'] . '<br />';
				for ($i=0; $i<$nbWork; $i++) {
					if (isset($_POST['checkbox' . $i])) {
						$openProperties = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/msds.txt', 'r');
						if ($openProperties) {
							$defaultProperties = fgets($openProperties, 4096);
							fclose($openProperties);
							// Définition des données par défaut
							$defaultName = preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) ? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) : $defaultName;
						}
						if (isset($_POST['checkbox' . $i])) {
							$alert .= '<input type="hidden" name="checkbox' . $i . '" value="' . $_POST['checkbox' . $i] . '" />';
							$alert .= $defaultName . '<br />';
						}
					}
				}
				$alert .= '<input type="hidden" name="confirm">
							<div class="pageButton">
								<button type="submit" class="formButton" name="remove">
									' . $message['action_confirm_yes'] . '
								</button>
								&nbsp;/&nbsp;
								<a href="./index.php?user_msds_editor">' . $message['action_confirm_no'] . '</a>
							</div>
						</form>';
			}
		}
	}
	
	//-----------------------------------------------------------------
	// On veut publier une ou plusieurs pages
	//-----------------------------------------------------------------
	
	// On demande la publication d'une unique fiche de sécurité
	elseif (isset($_GET['publish']) && in_array($folderId, $folderTemp)) {
		$openProperties = fopen($dirTemp . '/' . $folderId . '/msds.txt', 'r');
		if ($openProperties) {
			$defaultProperties = fgets($openProperties, 4096);
			fclose($openProperties);
			// Définition des données par défaut
			$defaultName = preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) ? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) : $defaultName;
		}
		// On confirme la publication
		if (isset($_GET['confirm'])) {
			// Lecture du texte de la page
			if ($userLevel > 1) {
				// Génération de nom de page jusqu'à ce qu'on trouve un disponible
				do {
					$newFolderId = randomHex();
					$reqName = in_array($newFolderId, $folderWorks);
				} while ($reqName == true);
				
				// Notation de l'état de publication
				$properties = fopen($dirTemp . '/' . $folderId . '/msds.txt', 'r+');
				$defaultProperties = fgets($properties);
				$defaultProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]published[/request]' , $defaultProperties);
				fseek($properties, 0);
				fputs($properties, $defaultProperties);
				fclose($properties);
				
				// Déplacement et suppression du dossier de travaux
				copyDir($dirTemp . '/' . $folderId, $dirWorks . '/' . $newFolderId);
				clearDir($dirTemp . '/' . $folderId);
				
				$alert .= $message['message_msds_published'] . ' :<br />' . $defaultName . '<br />';
			}
			else {
				// Demande de publication des pages
				// Marqueur de requête
				$msds = fopen($dirTemp . '/' . $folderId .'/msds.txt', 'r+');
				$getProperties = fgets($msds);
				$getProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]request[/request]' , $getProperties);
				fseek($msds, 0);
				fputs($msds, $getProperties);
				fclose($msds);
				$alert .= $message['message_msds_request'] . ' :<br />' . $defaultName . '<br />';
			}
			$alert .= '<a href="./index.php?user_msds_editor">' . $message['action_close'] . '<br /></a>';
		}
		// On demande la confirmation de la publication de la page 
		else {
			// Liste les pages qui seront publiés
			$alert .= $message['query_publish_following_msds'] . '<br />';
			$alert .= $defaultName . '<br />';
			$alert .= '<div class="pageButton">
							<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '&amp;publish&amp;confirm">' . $message['action_confirm_yes'] . '</a>
							&nbsp;/&nbsp;
							<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '">' . $message['action_confirm_no'] . '</a>
						</div>';
		}
	}
	
	// On demande la publication d'une ou plusieurs fiche de sécurité
	elseif (isset($_POST['publish'])) {
		// On compte le nombre de travaux à publier
		$nbWorkPublish = 0;
		for ($i=0; $i<$nbWork; $i++) {
			if (isset($_POST['checkbox' . $i])) {
				$nbWorkPublish++;
			}
		}
		// On confirme la publication
		if (isset($_POST['confirm'])) {
			if ($userLevel > 1) {
				if ($nbWorkPublish > 1) $alert .= $message['message_msdss_published'] . ' :<br />';
				else $alert .= $message['message_msds_published'] . ' :<br />';
			} else {
				if ($nbWorkPublish > 1) $alert .= $message['message_msdss_request'] . ' :<br />';
				else $alert .= $message['message_msds_request'] . ' :<br />';
			}
			for ($i=0; $i<$nbWork; $i++) {
				if (isset($_POST['checkbox' . $i])) {
					if (in_array($_POST['checkbox' . $i], $folderTemp)) {
						$openProperties = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/msds.txt', 'r');
						if ($openProperties) {
							$defaultProperties = fgets($openProperties, 4096);
							fclose($openProperties);
							// Définition des données par défaut
							$defaultName = preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) ? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) : $defaultName;
						}
						if ($userLevel > 1) {
							// Génération de nom de page jusqu'à ce qu'on trouve un disponible
							do {
								$newFolderId = randomHex();
								$reqName = in_array($newFolderId, $folderWorks);
							} while ($reqName == true);
							
							// Notation de l'état de publication
							$properties = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/msds.txt', 'r+');
							$defaultProperties = fgets($properties);
							$defaultProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]published[/request]' , $defaultProperties);
							fseek($properties, 0);
							fputs($properties, $defaultProperties);
							fclose($properties);
							
							// Déplacement et suppression du dossier de travaux
							copyDir($dirTemp . '/' . $_POST['checkbox' . $i], $dirWorks . '/' . $newFolderId);
							clearDir($dirTemp . '/' . $_POST['checkbox' . $i]);
						}
						else {
							// Demande de publication des pages
							$listeArray[] = $_POST['checkbox' . $i];
							// Marqueur de requête
							$msds = fopen($dirTemp . '/' . $_POST['checkbox' . $i] .'/msds.txt', 'r+');
							$getProperties = fgets($msds);
							$getProperties = preg_replace('#\[request\].*\[/request\]#i', '[request]request[/request]' , $getProperties);
							fseek($msds, 0);
							fputs($msds, $getProperties);
							fclose($msds);
						}
						$alert .= $defaultName . '<br />';
					}
				}
			}
			$alert .= '<a href="./index.php?user_msds_editor">' . $message['action_close'] . '<br /></a>';
		} else {
			if ($nbWorkPublish != 0) {
				$alert .= '<form action="./index.php?user_msds_editor" method="post">';
				if ($nbWorkPublish > 1) $alert .= $message['query_publish_following_msdss'] . '<br />';
				else $alert .= $message['query_publish_following_msds'] . '<br />';
				for ($i=0; $i<$nbWork; $i++) {
					if (isset($_POST['checkbox' . $i])) {
						$openProperties = fopen($dirTemp . '/' . $_POST['checkbox' . $i] . '/msds.txt', 'r');
						if ($openProperties) {
							$defaultProperties = fgets($openProperties, 4096);
							fclose($openProperties);
							// Définition des données par défaut
							$defaultName = preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) ? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties) : $defaultName;
						}
						
						// Liste les pages qui seront publiés
						$alert .= '<input type="hidden" name="checkbox' . $i . '" value="' . $_POST['checkbox' . $i] . '" />';
						$alert .= $defaultName . '<br />';
					}
				}
				$alert .= '<input type="hidden" name="confirm">
							<div class="pageButton">
								<button type="submit" class="formButton" name="publish">
									' . $message['action_confirm_yes'] . '
								</button>
								&nbsp;/&nbsp;
								<a href="./index.php?user_msds_editor">' . $message['action_confirm_no'] . '</a>
							</div>
						</form>';
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
	$tableFormula 	= array();
	$tableCASNumber	= array();
	$tableRequest	= array();
	
	// Boucle de liste des fichiers pour trier les données
	for ($i=0; $i<$nbWork; $i++) {
		$file = $dirTemp . '/' . $folderTemp[$i];
		
		// Lectures des données des pages
		$openProperties = fopen($file . '/msds.txt', 'r');
		if ($openProperties) {
			$defaultProperties = '';
			while (!feof($openProperties))
				$defaultProperties .= fgets($openProperties, 4096);
			fclose($openProperties);
			$tableId[$i] 		= $folderTemp[$i];
			$tableName[$i]		= preg_match('#.*\[name\](.+)\[/name\].*#i', $defaultProperties) 				? preg_replace('#.*\[name\](.+)\[/name\].*#i', '$1', $defaultProperties)							: '';
			$tableFormula[$i] 	= preg_match('#.*\[formula\](.+)\[/formula\].*#i', $defaultProperties)			? preg_replace('#.*\[formula\](.+)\[/formula\].*#i', '$1', $defaultProperties)						: '';
			$tableCASNumber[$i]	= preg_match('#.*\[CAS_number\](.+)\[/CAS_number\].*#i', $defaultProperties) 	? preg_replace('#.*\[CAS_number\](.+)\[/CAS_number\].*#i', '$1', $defaultProperties)				: '';
			$tableRequest[$i]	= preg_match('#.*\[request\](.+)\[/request\].*#i', $defaultProperties) 			? $pageRequestName[preg_replace('#.*\[request\](.+)\[/request\].*#i', '$1', $defaultProperties)]	: '';
			
			// Compteur de requêtes de publication
			if ($tableRequest[$i] == $pageRequestName['request'])
				$nbWorkRequest++;
		}
	}
	
	$tableSort = array(
		'name'			=> $tableName,
		'formula'		=> $tableFormula,
		'cas_number'	=> $tableCASNumber,
		'request'		=> $tableRequest
		
	);
	
	// On procède à un tri sur le type de données	
	$tableOrder = $tableSort[$_SESSION[$sessionPage . 'order']];
	
	// On procède à un tri alphanumérique
	if ($_SESSION[$sessionPage . 'sort'] == 'desc') arsort($tableOrder);
	else asort($tableOrder);
	
	
	//-----------------------------------------------------------------
	// On veut afficher un apperçu de la page
	//-----------------------------------------------------------------
	
	$defaultSynonyms 	= str_replace('_', '<br />', $defaultSynonyms);
	$defaultPH 			= str_replace('_', '<br />', $defaultPH);
	$defaultCaution 	= str_replace('_', '<br />', $defaultCaution);
	$defaultHazard 		= str_replace('_', '<br />', $defaultHazard);
	$defaultType		= str_replace('_', '<br />', $defaultType);
	
	$tableOldPictogram = explode("_", $defaultOldPictogram);
	$defaultOldPictogram = '';
	foreach ($tableOldPictogram as $key=>$val) {
		if ($tableOldPictogram[$key] != '')
			$defaultOldPictogram .= '<img class="msdsPictogram" src="' . $userDesignPath . '/images/picto_old_' . $tableOldPictogram[$key] . '.png" />';	
	}
	
	$tableNewPictogram = explode("_", $defaultNewPictogram);
	$defaultNewPictogram = '';
	foreach ($tableNewPictogram as $key=>$val) {
		if ($tableNewPictogram[$key] != '')
			$defaultNewPictogram .= '<img class="msdsPictogram" src="' . $userDesignPath . '/images/picto_new_' . $tableNewPictogram[$key] . '.png" />';
	}
	
	// Accès en base de données pour afficher le nom de l'auteur
	connectDb();
	$mysqlQueryAuthor = mysql_query("SELECT login FROM users WHERE user_id='".$defaultAuthor."'");
	$mysqlDataAuthor = mysql_fetch_array($mysqlQueryAuthor);
	$defaultAuthor = ($mysqlDataAuthor['login'] != '') ? $mysqlDataAuthor['login'] : 'inconnu';
	
	
	// On affiche un apperçu de la page
	if (in_array($folderId, $folderTemp) && ((!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['name']))) || (!empty($_POST['name']) && !empty($_POST['formula']) && !empty($_POST['CAS_number']) && !empty($_POST['EC_number']))) {	
		
		// Affichage du Linker
		echo '<div class="pageLinker">
			<a href="./index.php?home">' . $message['navigation_tree_home'] . '</a>&nbsp;>&nbsp;
			<a href="./index.php?user_space">' . $message['navigation_tree_user_space'] . '</a>&nbsp;>&nbsp;
			<a href="./index.php?user_msds_editor">' . $message['navigation_tree_make_msds'] . '</a>&nbsp;>&nbsp;
			' . $message['navigation_tree_make_msds_optn'] . '
		</div>';
		
		// Affichage des informations
		echo '<div class="pageFolderInfo">
			<div class="pageImage">
				<img src="' . $userDesignPath . '/images/make_works.png" alt="" />
			</div>
			<div class="pageText">
				<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '&amp;publish">' . $message['action_publish'] . '</a><br />
				<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '&amp;edit">' . $message['action_edit'] . '</a><br />
				<a href="./index.php?user_msds_editor&amp;id=' . $folderId . '&amp;remove">' . $message['action_remove'] . '</a><br />
				<a href="./index.php?user_msds_editor">' . $message['action_back_to_work'] . '</a>
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
				<table class="msdsTable" style="border: 1px solid black; width: 100%;">		
					<tr>
						<td colspan="3" class="msdsHeaderTitle">' . $defaultName . '</td>
					</tr>
					<tr>
						<td class="msdsHeaderLegend" style="width: 90px; height: 60px; vertical-align: top;">' . $message['legend_msds_view_synonyms'] . '</td>
						<td class="msdsHeaderText" style="height: 60px; vertical-align: top;">' . $defaultSynonyms . '</td>
						<td rowspan="2" class="msdsHeaderPictogram" style="text-align: right;">' . $defaultNewPictogram . '</td>
					</tr>
				</table>
				<table class="msdsTable" style="margin-top: 10px; border: 1px solid black; width: 100%;">		
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_density'] . '</td>
						<td class="msdsBodyText">' . $defaultDensity . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_molar_mass'] . '</td>
						<td class="msdsBodyText">' . $defaultMolarMass . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_pKa'] . '</td>
						<td class="msdsBodyText">' . $defaultPH . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_form'] . '</td>
						<td class="msdsBodyText">' . $defaultForm . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_color'] . '</td>
						<td class="msdsBodyText">' . $defaultColor . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_odour'] . '</td>
						<td class="msdsBodyText">' . $defaultOdour . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_melting_point'] . '</td>
						<td class="msdsBodyText">' . $defaultMeltingPoint . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_boiling_point'] . '</td>
						<td class="msdsBodyText">' . $defaultBoilingPoint . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_vapor_pressure'] . '</td>
						<td class="msdsBodyText">' . $defaultVaporPressure . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_flash_point'] . '</td>
						<td class="msdsBodyText">' . $defaultFlashPoint . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_thermal_dec'] . '</td>
						<td class="msdsBodyText">' . $defaultThermalDec . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_refractive_index'] . '</td>
						<td class="msdsBodyText">' . $defaultRefractiveIndex . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_ignition_temp'] . '</td>
						<td class="msdsBodyText">' . $defaultIgnitionTemp . '</td>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_water_sol'] . '</td>
						<td class="msdsBodyText">' . $defaultWaterSol . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_other_sol'] . '</td>
						<td class="msdsBodyText">' . $defaultOtherSol . '</td>
						<td class="msdsBodyLegend"></td>
						<td class="msdsBodyText"></td>
					</tr>
				</table>
				<table class="msdsTable" style="margin-top: 10px; border: 1px solid black; width: 100%;">		
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_CAS_number'] . '</td>
						<td class="msdsBodyText">' . $defaultCASNumber . '</td>
						<td class="msdsBodyPictogram" rowspan="4">' . $defaultOldPictogram . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_EC_number'] . '</td>
						<td class="msdsBodyText">' . $defaultECNumber . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_R_phrase'] . '</td>
						<td class="msdsBodyText">' . $defaultRPhrase . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_S_phrase'] . '</td>
						<td class="msdsBodyText">' . $defaultSPhrase . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_hazard'] . '</td>
						<td class="msdsBodyText">' . $defaultHazard . '</td>
						<td class="msdsBodyPictogram" rowspan="4">' . $defaultNewPictogram . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_caution'] . '</td>
						<td class="msdsBodyText">' . $defaultCaution . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_DL50_oral'] . '</td>
						<td class="msdsBodyText">' . $defaultDL50Oral . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_DL50_dermal'] . '</td>
						<td class="msdsBodyText">' . $defaultDL50Dermal . '</td>
					</tr>
				</table>
				<table class="msdsTable" style="margin-top: 10px; border: 1px solid black; width: 100%;">				
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_formula'] . '</td>
						<td class="msdsBodyText">' . preg_replace('#(\d+)#i', '<sub>$1</sub>',$defaultFormula) . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_smile'] . '</td>
						<td class="msdsBodyText">' . $defaultSmile . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_image'] . '</td>
						<td class="msdsBodyText">' . $defaultImage . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_use'] . '</td>
						<td class="msdsBodyText">' . $defaultUse . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_author'] . '</td>
						<td class="msdsBodyText">' . $defaultAuthor . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_type'] . '</td>
						<td class="msdsBodyText">' . $defaultType . '</td>
					</tr>
					<tr>
						<td class="msdsBodyLegend">' . $message['legend_msds_view_synthesis'] . '</td>
						<td class="msdsBodyText">' . $defaultSynthesis . '</td>
					</tr>
				</table>
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
			' . $message['navigation_tree_make_msds'] . '
		</div>';
		
		// Affichage des informations
		echo '<div class="pageFolderInfo">
			<div class="pageImage">
				<img src="' . $userDesignPath . '/images/make_msds.png" alt="" />
			</div>
			<div class="pageText">
				' . $message['legend_work_pending'] . ' : ' . $nbWork . ' / ' . $MSDSLimit . '<br />';
				if ($userLevel < 2)
					echo $message['legend_work_validation'] . ' : ' . $message['message_publish_admin'] . '<br />';
				else
					echo $message['legend_work_validation'] . ' : ' . $message['message_publish_auto'] . '<br />';
				echo $message['legend_work_validation_pending'] . ' : ' . $nbWorkRequest . ' / ' . $MSDSLimit . '<br />
			</div>
		</div>';
		
		// Affichage des alertes
		if ($alert != null) {
			echo '<div class="pageAlert">
				' . $alert . '
			</div>';
		}
		
		echo '<div class="pageList">';
		if ($nbWork < $MSDSLimit)
			echo '<a href="./index.php?user_msds_editor&amp;new">' . $message['action_msds_new'] . '</a><br />';
		else
			echo $message['alert_works_limit'];	
		echo '</div>';		
		
		// Affichage sous forme de liste
		echo '<div class="pageList">
			<form class="pageListForm" action="./index.php?user_msds_editor" method="post">
				<div>
					<table cellspacing="0px" class="pageListLegendTable">
						<tr class="pageListLegendRow">
							<td class="pageListLegendCell" style="width: 400px;">';
								if ($_SESSION[$sessionPage . 'order'] == 'name') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_msds_editor&amp;order=name&amp;sort=asc">' . $message['legend_name'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_msds_editor&amp;order=name&amp;sort=desc">' . $message['legend_name'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_msds_editor&amp;order=name&amp;sort=asc">' . $message['legend_name'] . '</a>';
								}
							echo '</td>
							<td class="pageListLegendCell" style="width: 100px">'; 
								if ($_SESSION[$sessionPage . 'order'] == 'formula') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_msds_editor&amp;order=formula&amp;sort=asc">' . $message['legend_formula'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_msds_editor&amp;order=formula&amp;sort=desc">' . $message['legend_formula'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_msds_editor&amp;order=formula&amp;sort=asc">' . $message['legend_formula'] . '</a>';
								}
							echo '</td>
							<td class="pageListLegendCell" style="width: 100px;">'; 
								if ($_SESSION[$sessionPage . 'order'] == 'cas_number') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_msds_editor&amp;order=cas_number&amp;sort=asc">' . $message['legend_CAS_number'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_msds_editor&amp;order=cas_number&amp;sort=desc">' . $message['legend_CAS_number'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_msds_editor&amp;order=cas_number&amp;sort=asc">' . $message['legend_CAS_number'] . '</a>';
								}
							echo '</td>
							<td class="pageListLegendCell">'; 
								if ($_SESSION[$sessionPage . 'order'] == 'request') {
									if ($_SESSION[$sessionPage . 'sort'] == 'desc')
										echo '<a href="index.php?user_msds_editor&amp;order=request&amp;sort=asc">' . $message['legend_request'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/down.png" title="' . $message['info_order_up'] . '" alt="" /></a>';
									else
										echo '<a href="index.php?user_msds_editor&amp;order=request&amp;sort=desc">' . $message['legend_request'] . ' <img class="legendSortButton" src="' . $userDesignPath . '/images/up.png" title="' . $message['info_order_down'] . '" alt="" /></a>';
								} else {
									echo '<a href="index.php?user_msds_editor&amp;order=request&amp;sort=asc">' . $message['legend_request'] . '</a>';
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
							<td class="pageListCell" style="width: 400px" id="table1_chk' . $i . '">
								<input type="checkbox" name="checkbox' . $i . '" id="checkbox' . $i . '" value="' . $tableId[$key] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . ');" />
								<a href="./index.php?user_msds_editor&amp;id=' . $tableId[$key] . '" onclick="checkTheBox(\'table1_chk\', ' . $i . ');">' . cutString($tableName[$key], 53, 1) . '</a>
							</td>
							<td class="pageListCell" style="width: 100px;">' . preg_replace('#(\d+)#i', '<sub>$1</sub>',$tableFormula[$key]) . '</td>
							<td class="pageListCell" style="width: 100px;">' . $tableCASNumber[$key] . '</td>
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
					<a onclick="actionCheckbox(\'table1_chk\',' . $i . ', \'2\');">' . $message['action_inverse_selection'] . '</a><br />
					<div class="pageButton">
						<button type="submit" class="formButton" name="remove">
							' . $message['action_remove_selection'] . '
						</button>
						&nbsp;/&nbsp;
						<button type="submit" class="formButton" name="publish">
							' . $message['action_publish_selection'] . '
						</button>
					</div>
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