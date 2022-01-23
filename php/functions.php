<?php


// Connexion à  la base de données
function connectDb() {
	mysql_connect("localhost", "root", ""); // Connexion à  MySQL
	mysql_select_db("nobelios"); // Sélection de la base nobelios
}

// Connexion à  la base de données
//function connectDb() {
//	mysql_connect("db1184.1and1.fr", "dbo222457697", "BhpUVbd7"); // Connexion à  MySQL
//	mysql_select_db("db222457697"); // Sélection de la base nobelios
//}

//-----------------------------------------------------------------
//	Fonction sur les fichiers et les dossiers 
//-----------------------------------------------------------------

// Compression d'un fichier unique
function zip($currentDir, $path, $name) {
	$zip = new ZipArchive();
	$res = $zip->open($name . '.zip', ZipArchive::CREATE);
	if ($res === true) {
		if (is_dir($currentDir))
			$zip->addEmptyDir($path);
		else
			$zip->addFile($currentDir, $path);
		$zip->close();
	}
}

// Compression d'un ensemble de fichier (utilise la fonction zip)
function ZipFile($currentDir, $path, $name) {
	$dir = opendir($currentDir);
	if (!$dir) return;
	while(false !== ($file = readdir($dir))) {
		if (( $file != '.' ) && ( $file != '..' )) {
			zip($currentDir . '/' . $file, $path . '/' . $file, $name);
			if (is_dir($currentDir . '/' . $file))
				ZipFile($currentDir . '/' . $file, $path . '/' . $file, $name);
		}
	}
}

// Ecriture des noms sous les miniatures
function cutString($string, $nbChar, $nbLine, $splitWord = true) {
	// Découpage primaire de la chaine avec insertion de saut de ligne
	$string = wordwrap($string, $nbChar, '<br />', $splitWord);
	$chunks = explode('<br />', $string);
	$ttLine = count($chunks)<$nbLine ? count($chunks) : $nbLine;
	$sendString = '';
	$stringEnd = '';
	for ($i=0; $i<count($chunks); $i++) {
		if ($i < ($ttLine-1))
			$sendString = $sendString . $chunks[$i] . '<br />';
		else
			$stringEnd = $stringEnd . $chunks[$i];
	}
	if (strlen($stringEnd) > $nbChar) {
		$strinEndTable = str_split($stringEnd);
		$stringEnd = '';
		if (count($strinEndTable) > $nbChar) {
			for ($i=0; $i<$nbChar; $i++) {
				if ($i < $nbChar-3)
					$stringEnd = $stringEnd . $strinEndTable[$i];
				else
					$stringEnd = $stringEnd . '.';
			}
		}
	}
	$sendString = $sendString . $stringEnd;
	return $sendString;
}


// Fonction de lecture de dossier
function testDir($dir)
{
	$folder = array();
	if (file_exists($dir) && $currentDir = opendir($dir)) {
		while (false !== ($file = readdir($currentDir))) {
			if ($file != "." && $file != "..") {
				$folder[] = $file ;
			}
		}
		closedir($currentDir);
	}
	return $folder;
}

// Déplace tout un répertoire
function copyDir($currentDir, $newDir) {
	$dir = opendir($currentDir);
	mkdir($newDir, '0777');
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($currentDir.'/'.$file) ) {
				copyDir($currentDir.'/'.$file, $newDir.'/'.$file);
			}
			else {
				copy($currentDir.'/'.$file, $newDir.'/'.$file);
			}
		}
	}
	closedir($dir);
}

// Supprimer tout un répertoire
function clearDir($dir) {
	$currentDir = opendir($dir);
	if (!$currentDir) return;
	while($file = readdir($currentDir)) {
		if ($file == '.' || $file == '..') continue;
			if (is_dir($dir."/".$file)) {
				$r = clearDir($dir."/".$file);
				if (!$r) return false;
			} else {
				$r = unlink($dir."/".$file);
				if (!$r) return false;
			}
	}
	closedir($currentDir);
	$r=rmdir($dir);
	if (!$r) return false;
		return true;
}

//-----------------------------------------------------------------
//	Compteurs
//-----------------------------------------------------------------

// Compter un nombre de dossiers sans les sous-dossiers
function nbFolder($dir) {
	$handle = opendir($dir);
	$nb = 0;
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				$nb ++;
			}
		}
	closedir($handle);
	return $nb;
}

// ERREUR DE FONCTION ! HEURE FAUSSE
// Retourne la date et l'heure de la dernière modification en fonction de la date sur le serveur
function lastEdit($dir) {
	$time = filemtime($dir); // timestamp de la dernière modification
	$elapsedTime = (time() - $time); // temps écoulé en secondes
	if ($elapsedTime < 60) echo "Il y a " .$elapsedTime." secondes";
	elseif ($elapsedTime < (60 * 60)) echo "Il y a ".round($elapsedTime/60)." minutes";
	elseif ($elapsedTime < (24 * 60 * 60)) echo "Aujourd'hui à  ".date('H\:i\:s', $time);
	elseif ($elapsedTime < (2 * 24 * 60 * 60)) echo "Hier à  ". date('H\:i\:s', $time);
	else echo "Le ".date('d/m/Y', $time)." à  ".date('H\:i\:s', $time);
}

// Retourne la taille d'un fichier avec conversion d'unitées
function sizeOfFile($dir) {
	$size = filesize($dir);
	if ($size >= 1000000) $size = round($size/1000000, 2).' Mo';
	else $size = round($size/1000).' Ko';
	return $size;
}

// Renvoi la taille d'un fichier et de son contenu en octets
function folderSize($dir) {
	$currentDir = opendir($dir);
	$folderSize = 0; // initialisation à  0 octets
	if (!$currentDir) return;
	while($file = readdir($currentDir)) {
		if ($file == '.' || $file == '..') continue;
			if (is_dir($dir."/".$file)) {
			} else {
				$folderSize += filesize($dir.'/'.$file);
			}
	}
	closedir($currentDir);
	return $folderSize;
}


//-----------------------------------------------------------------
//	Générateur de chaine et décodeurs
//-----------------------------------------------------------------

// Générateur de chaine hexadecimal sur 32 bits
function randomHex() {
	$carHexa = 16;
	$hexa = "0123456789ABCDEF";
	$string = "";
	srand((double)microtime()*1000000);
	for($i=0; $i<$carHexa; $i++) {
		$string .= $hexa[rand(0, $carHexa - 1)%strlen($hexa)]; // carHexa - 1 car le 0 compte pour 1
	}
	return $string;
}

// Fonction faisant appel à GeSHi
function geshi($matches) {
	$languages_autorises = array("php", "html", "css", "javascript", "c", "cpp", "java", "asm", "python"); // Tableau listant les langages autorisés
	if (in_array($matches[1], $languages_autorises)) {
		if ($matches[1] == "html") { 
			$matches[1] = "html4strict"; $language = "html";
		} else { 
			$language = $matches[1];
		}
		$geshi = new GeSHi(html_entity_decode(trim($matches[2])), $matches[1]); // Création de l'objet GeShi, on décode les caractéres que htmlspecialchars à  fait et on supprime les espaces au début et fin de chaà®ne
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS); // , "x" permet de mettre en gras le nombre tout les "x" valeures
		//$geshi->set_line_style('background: #FFFFFF;', 'background: #FFFFFF;'); // permet de choisir des couleurs de fond pour les lignes de code
		return '<strong>Code : '.strtoupper($language).'</strong><br />'.$geshi->parse_code(); // On retourne le code coloré
	} else {
		$geshi = new GeSHi(html_entity_decode(trim($matches[2])), 'html'); // Création de l'objet GeShi, on décode les caractéres que htmlspecialchars à  fait et on supprime les espaces au début et fin de chaà®ne
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS); // , "x" permet de mettre en gras le nombre tout les "x" valeures
		//s$geshi->set_line_style('background: #FFFFFF;', 'background: #FFFFFF;'); // permet de choisir des couleurs de fond pour les lignes de code
		return '<strong>Code : AUTHER</strong><br />'.$geshi->parse_code(); // On retourne le code coloré
	}
}

// Parsage de la page
function parse($content) {
	
	$editech = array
	(  
		// Listes
		'`[\r|\n]*?&lt;list&gt;(.+)&lt;/list&gt;[\r|\n]?`isU',
		'`[\r|\n]*?&lt;listnum&gt;(.+)&lt;/listnum&gt;[\r|\n]?`isU',
		'`[\r|\n]*?&lt;item&gt;[\r|\n]*?(.+)[\r|\n]*?&lt;/item&gt;[\r|\n]*?`isU',
		
		// Tableaux
		'`&lt;[\r|\n]?table center&gt;[\r|\n]*(.+)[\r|\n]*?&lt;\/table&gt;`isU',
		'`&lt;[\r|\n]?table&gt;[\r|\n]*?(.+)[\r|\n]*?&lt;/table&gt;`isU',
		'`[\r|\n]*?&lt;head&gt;[\r|\n]*?(.+)[\r|\n]*?&lt;/head&gt;[\r|\n]*?`isU',
		'`[\r|\n]*?&lt;line&gt;[\r|\n]*?(.+)[\r|\n]*?&lt;/line&gt;[\r|\n]*?`isU',
		'`[\r|\n]*?&lt;cell col=&quot;(.*)&quot; row=&quot;(.*)&quot;&gt;[\r|\n]*?(.+)[\r|\n]*?&lt;/cell&gt;[\r|\n]*?`isU',
		
		// Formatage
		'`&lt;bold&gt;(.+)[\r|\n]*&lt;/bold&gt;`isU',
		'`&lt;italic&gt;(.+)&lt;/italic&gt;`isU',
		'`&lt;underline&gt;(.+)&lt;/underline&gt;`isU',
		'`&lt;strike&gt;(.+)&lt;/strike&gt;`isU',
		'`&lt;sub&gt;(.+)&lt;/sub&gt;`isU',
		'`&lt;sup&gt;(.+)&lt;/sup&gt;`isU',
		'`&lt;title1&gt;(.+)&lt;/title1&gt;[\r|\n]*?`isU',
		'`&lt;title2&gt;(.+)&lt;/title2&gt;[\r|\n]*?`isU',
		'`&lt;link url=[http://]?&quot;(.+)&quot;&gt;&lt;/link&gt;`iU',
		'`&lt;link url=[http://]?&quot;(.+)&quot;&gt;(.+)&lt;/link&gt;`iU',
		'`[\r|\n]?&lt;center&gt;(.+)&lt;/center&gt;`isU',
		
		// Images, vidéos
		'`[\r|\n]?&lt;image url=&quot;(.+)&quot; title=&quot;(.*)&quot;&gt;`iU',
		'`[\r|\n]?&lt;imagemini url=&quot;(.+)&quot; title=&quot;(.*)&quot;&gt;[\r|\n]?`iU',
		'`[\r|\n]?&lt;video url=&quot;(.+)&quot;&gt;`iU',
		'`[\r|\n]?&lt;sound url=&quot;(.+)&quot;&gt;`iU',
		
		// Zones de texte
		'`[\r|\n]?&lt;hazard&gt;[\r|\n]*?(.+)[\r|\n]*?&lt;/hazard&gt;`isU',
		'`[\r|\n]?&lt;warning&gt;[\r|\n]*?(.+)&lt;/warning&gt;`isU',
		'`[\r|\n]?&lt;information&gt;[\r|\n]*?(.+)&lt;/information&gt;`isU',
		'`[\r|\n]?&lt;query&gt;[\r|\n]*?(.+)&lt;/query&gt;`isU',
		'`[\r|\n]?&lt;answer&gt;[\r|\n]*?(.+)&lt;/answer&gt;`isU',
	);
	
	$html = array
	(  
		// Listes
		'<ul class="editechUl">$1</ul>',
		'<ol class="editechOl">$1</ol>',
		'<li class="editechLi">$1</li>',
		
		// Tableaux
		'<table class="editechTableCenter">$1</table>',
		'<table class="editechTable">$1</table>',
		'<tr class="editechHead">$1</tr>',
		'<tr class="editechLine">$1</tr>',
		'<td colspan="$1" rowspan="$2" class="editechCell">$3</td>',
		
		// Formatage
		'<strong>$1</strong>',
		'<em>$1</em>',
		'<span class="editechUnderline">$1</span>',
		'<span class="editechStrike">$1</span>',
		'<sub>$1</sub>',
		'<sup>$1</sup>',
		'<h2 class="editechTitle1">$1</h2><br />',
		'<h3 class="editechTitle2">$1</h3><br />',
		'<a href="http://$1">$1</a>',
		'<a href="http://$1">$2</a>',
		'<br /><div class="editechCenter">$1</div>',
		
		// Images, vidéos
		'<img src="$1" title="$2" alt="$2" />',
		'<a href="$1"><img class="editechImageMini" src="$1" title="$2" alt="image" /></a>',
		'<object width="480px" height="360px" type="application/x-shockwave-flash" data="./flash/player.swf"><param name="movie" value="./flash/player.swf" /><param name="flashvars" value="file=$1" /></object>',
		'<object width="350px" height="20px" type="application/x-shockwave-flash" data="./flash/player.swf"><param name="movie" value="./flash/player.swf" /><param name="flashvars" value="file=$1" /></object>',
		
		// Zones de texte
		'<p class="editechHazard">$1</p>',
		'<p class="editechWarning">$1</p>',
		'<p class="editechInformation">$1</p>',
		'<p class="editechQuery">$1</p>',
		'<p class="editechAnswer">$1</p>'
	);
	
	$content = htmlentities($content);
	$content = preg_replace($editech, $html, $content);
	$content = preg_replace_callback('`&lt;code type=&quot;(.+)&quot;&gt;(.+)&lt;/code&gt;`isU', 'geshi', $content);
	
	// Retours à  la ligne
	$content = preg_replace('`\n`isU', '<br />', $content);
	
	return $content;
}
	
?>