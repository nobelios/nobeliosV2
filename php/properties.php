<?php
// Nobelios V2.0
// Dernière modification le : 15/05/2009
// Blackout, toute copie pour usage non personnelle interdite

$domainName					= "http://localhost/nobelios v2.0";
$language					= "fr";
$full						= true;
$pageWiew					= "number";
$userDesign					= "normal";
$userDesignPath				= "./design/" . $userDesign;					// Chemin d'accès au design depuis l'accueil
$nbResultByPage 			= 20; 											// Nombre de resultats affichées par pages
$formMaxPictoWidth			= 5;											// Nombre mawimum de pictogrammes en largeur dans les formulaires
$allowedSubject				= array("chemistry", "physics", "electronics", "astronomy");
$allowedType				= array("lesson", "technical", "manipulation", "equipment", "data");

// définition du type de fichier
$filesExtType		= array(
							// Images
							"png" 	=> "image",			// Portable Network Graphic
							"gif"	=> "image",			// Graphics Interchange Format
							"bmp"	=> "image", 		// Bitmap
							"jpg"	=> "image", 		// Joint Photographic Group
							"jpeg"	=> "image", 		// Joint Photographic Experts Group
							// Audio
							"mp3"	=> "audio",			// MPEG-1/2 Audio Layer 3
							"wav"	=> "audio",			// WAVEform audio format
							"wma"	=> "audio",			// Windows Media Audio
							// Vidéos
							"flv"	=> "video", 		// Flash Video
							"avi"	=> "video", 		// Audio Video Interleave
							"mkv"	=> "video", 		// Matroska
							"wmv"	=> "video", 		// Windows Media Video
							"mp4"	=> "video", 		// MPEG-4 (Moving Picture Experts Group 4)
							"mpg"	=> "video", 		// Moving Picture Group
							"mpeg"	=> "video", 		// Moving Picture Experts Group
							"mov"	=> "video",			// QuickTime Movie
							// Archives
							"zip"	=> "archive",		// Zip Archive
							"7z"	=> "archive",		// 7 zip
							"rar"	=> "archive",		// Roshal Archive
							// Documents et textes
							"doc"	=> "document",		// Document
							"odt"	=> "document", 		// Opendocument Text
							"ods"	=> "spreadsheet",	// Opendocument Spreadsheet
							"xls"	=> "spreadsheet",	// Document Microsoft Excel before 2007
							"txt"	=> "text",			// Text
							"pdf"	=> "pdf"	 		// Portable Document Format							
						);

// définition du nom des extention de fichier
$filesExtDef		= array(
							// Images
							"png" 	=> "Image PNG (Portable Network Graphic)",				// Portable Network Graphic
							"gif"	=> "Image GIF (Graphics Interchange Format)",			// Graphics Interchange Format
							"bmp"	=> "Image BMP (Bitmap)", 								// Bitmap
							"jpg"	=> "Image JPG (Joint Photographic Group)", 				// Joint Photographic Group
							"jpeg"	=> "Image JPEG (Joint Photographic Experts Group)", 	// Joint Photographic Experts Group
							// Audio
							"mp3"	=> "Son MP3 (MPEG-1/2 Audio Layer 3)",					// MPEG-1/2 Audio Layer 3
							"wav"	=> "Son WAV (WAVEform audio format)",					// WAVEform audio format
							"wma"	=> "Son WMA (Windows Media Audio)",						// Windows Media Audio
							// Vidéos
							"flv"	=> "Vidéo Flash (Flash Video)", 						// Flash Video
							"avi"	=> "Vidéo AVI (Audio Video Interleave)", 				// Audio Video Interleave
							"mkv"	=> "Vidéo MKV (Matroska)", 								// Matroska
							"wmv"	=> "Vidéo WMV (Windows Media Video)", 					// Windows Media Video
							"mp4"	=> "Vidéo MP4 (Moving Picture Experts Group 4)", 		// MPEG-4 (Moving Picture Experts Group 4)
							"mpg"	=> "Vidéo MPG (Moving Picture Group)", 					// Moving Picture Group
							"mpeg"	=> "Vidéo MPEG (Moving Picture Experts Group)", 		// Moving Picture Experts Group
							"mov"	=> "Vidéo MOV (QuickTime Movie)",						// QuickTime Movie
							// Archives
							"zip"	=> "Archive ZIP (Zip Archive)",							// Zip Archive
							"7z"	=> "Archive 7ZIP (7-zip Archive)",						// 7-zip Archive
							"rar"	=> "Archive RAR (Roshal Archive)",						// Roshal Archive
							// Documents et textes
							"doc"	=> "Fichier DOC (Word)",								// Document
							"odt"	=> "Fichier ODT (Opendocument Text)", 					// Opendocument Text
							"ods"	=> "Fichier ODS (Opendocument Spreadsheet)",			// Opendocument Spreadsheet
							"xls"	=> "Fichier XLS (Excel)",								// Document Microsoft Excel befor 2007
							"txt"	=> "Fichier TXT (Text)",								// Text
							"pdf"	=> "Fichier PDF (Portable Document Format)"	 			// Portable Document Format							
						);
$allowedSubject		= array("chemistry", "physics", "electronics", "astronomy");
$allowedType		= array("lesson", "technical", "manipulation", "equipment", "data");

$pageSubjectName		= array(
							"chemistry"		=> "Chimie",
							"physics"		=> "Physique",
							"electronics"	=> "Electronique",
							"astronomy"		=> "Astronomie"
						);
						
$pageTypeName			= array(
							"lesson"		=> "Cours",
							"technical"		=> "Techniques",
							"manipulation"	=> "Manipulations",
							"equipment"		=> "Equipement",
							"data"			=> "Données"
						);
						
$pageRequestName		= array(
							"works"			=> "<font color=\"green\">en travaux</font>",
							"refused"		=> "<font color=\"red\">refusé</font>",
							"request"		=> "<font color=\"yellow\">demande</font>",
							"published"		=> "<font color=\"red\">publié</font>"
						);

$allowedAvatarExt	= array("jpg", "jpeg", "gif", "png");
$allowedImgExt		= array("jpg", "jpeg", "gif", "png");
$allowedVideoExt	= array("mp3", "flv");
$allowedFileExt		= array("png", "gif", "bmp", "jpg", "jpeg", "mp3", "wav", "wma", "flv", "avi", "mkv", "wmv", "mp4", "mpg", "mpeg", "mov", "zip", "7z", "rar", "doc", "odt", "ods", "xls", "txt", "pdf");
$propertiesBases	= "[subject][/subject][type][/type][title][/title][pointer][/pointer][tag][/tag][reference][/reference][smile][/smile][requisite][/requisite][author][/author][request][/request]";
$MSDSBases			= "[name][/name][synonyms][/synonyms][type][/type][synthesis][/synthesis][CAS_number][/CAS_number][EC_number][/EC_number][formula][/formula][smile][/smile][R_phrase][/R_phrase][S_phrase][/S_phrase][density][/density][molar_mass][/molar_mass][pH][/pH][form][/form][color][/color][odour][/odour][melting_point][/melting_point][boiling_point][/boiling_point][vapor_pressure][/vapor_pressure][flash_point][/flash_point][thermal_dec][/thermal_dec][refractive_index][/refractive_index][ignition_temp][/ignition_temp][water_sol][/water_sol][other_sol][/other_sol][image][/image][DL50_oral][/DL50_oral][DL50_dermal][/DL50_dermal][hazard][/hazard][caution][/caution][old_pictogram][/old_pictogram][new_pictogram][/new_pictogram][use][/use][author][/author][request][/request]";
$pictoOld			= array("O","F","F+","E","C","Xi","Xn","T","T+","N");
$pictoNew			= array("CB","IN","EX","CR","GZ","MU","TO","DA","EN");


//$informationsBases	= "[login][/login][email][/email][avatar][/avatar][signature][/signature][design][/design][id][/id][registration][/registration][language][/language][studies][/studies][name][/name][forname][/forname][birth][/birth][location][/location]";

?>