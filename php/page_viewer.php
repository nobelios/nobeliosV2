<?php

// Lecture des données

	$dirPages		= './web/'.$language.'/pages'; // Chemin d'accès aux pages
	$folderPages	= testDir($dirPages);

	//-----------------------------------------------------------------
	// Définition l'id de la page
	//-----------------------------------------------------------------

	// Définition par l'adresse
	if (isset($_GET['id']))
		$folderId = $_GET['id'];
	else
		$folderId = '';
	
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
	
	// Définition par lecture de données existantes
	if (in_array($folderId, $folderPages))
	{
		// Lecture des propriétés de la page
		$openProperties = fopen($dirPages.'/'.$folderId.'/properties.txt', 'r');
		if ($openProperties)
		{
			$defaultProperties = fgets($openProperties, 4096);
			fclose($openProperties);
			
			$defaultSubject		= preg_match('#.*\[subject\](.+)\[/subject\].*#i', $defaultProperties) 		? preg_replace('#.*\[subject\](.+)\[/subject\].*#i', '$1', $defaultProperties)		: $defaultSubject;
			$defaultType		= preg_match('#.*\[type\](.+)\[/type\].*#i', $defaultProperties) 			? preg_replace('#.*\[type\](.+)\[/type\].*#i', '$1', $defaultProperties) 			: $defaultType;
			$defaultTitle		= preg_match('#.*\[title\](.+)\[/title\].*#i', $defaultProperties)			? preg_replace('#.*\[title\](.+)\[/title\].*#i', '$1', $defaultProperties) 			: $defaultTitle;
			$defaultPointer		= preg_match('#.*\[pointer\](.+)\[/pointer\].*#i', $defaultProperties) 		? preg_replace('#.*\[pointer\](.+)\[/pointer\].*#i', '$1', $defaultProperties) 		: $defaultPointer;
			$defaultTag			= preg_match('#.*\[tag\](.+)\[/tag\].*#i', $defaultProperties) 				? preg_replace('#.*\[tag\](.+)\[/tag\].*#i', '$1', $defaultProperties) 				: $defaultTag;
			$defaultReference	= preg_match('#.*\[reference\](.+)\[/reference\].*#i', $defaultProperties) 	? preg_replace('#.*\[reference\](.+)\[/reference\].*#i', '$1', $defaultProperties) 	: $defaultReference;
			$defaultSmile 		= preg_match('#.*\[smile\](.+)\[/smile\].*#i', $defaultProperties) 			? preg_replace('#.*\[smile\](.+)\[/smile\].*#i', '$1', $defaultProperties) 			: $defaultSmile;
			$defaultRequisite	= preg_match('#.*\[requisite\](.+)\[/requisite\].*#i', $defaultProperties) 	? preg_replace('#.*\[requisite\](.+)\[/requisite\].*#i', '$1', $defaultProperties) 	: $defaultRequisite;
			$defaultAuthor		= preg_match('#.*\[author\](.+)\[/author\].*#i', $defaultProperties) 		? preg_replace('#.*\[author\](.+)\[/author\].*#i', '$1', $defaultProperties)		: $defaultSubject;
		}
		
		// Lecture du texte de la page
		$openWork = fopen($dirPages.'/'.$folderId.'/work.txt', 'r');
		if ($openWork)
		{
			$defaultWork = '';
			while (!feof($openWork))
			{
				$defaultWork .= fgets($openWork, 4096);
			}
			fclose($openWork);
		}
	}
	
	echo '
	<div class="pageLinker">
		<a href="./index.php?home">Accueil</a> > 
		<a href="./index.php?page_list">Pages</a> > 
		<a href="./index.php?page_list&amp;subject='.$defaultSubject.'">'.ucfirst($defaultSubject).'</a> > 
		<a href="./index.php?page_list&amp;subject='.$defaultSubject.'&amp;type='.$defaultType.'">'.ucfirst($defaultType).'</a> > 
		'.ucfirst($defaultTitle).'<br />
	</div>
	<div class="pageFolderInfo">
		<div class="pageImage">
			<a href="./index.php?page_list&amp;subject='.$defaultSubject.'"><img class="imageButton" src="'.$userDesignPath.'/'.$defaultSubject.'.jpg" /></a>
		</div>
		<div class="pageImage">
			<a href="./index.php?page_list&amp;subject='.$defaultSubject.'&amp;type='.$defaultType.'"><img class="imageButton" src="'.$userDesignPath.'/'.$defaultType.'.jpg" /></a><br />
		</div>
		<div class="pageText">
			<table cellspacing="0px" style="border: 0px none;">
				<tr>
					<td style="border: 0px none; text-align: right;">Référence: </td>
					<td style="border: 0px none; padding-left: 5px;">'.$defaultReference.'</td>
				</tr>
				<tr>
					<td style="border: 0px none; text-align: right;">Tags:</td>
					<td style="border: 0px none; padding-left: 5px;">'.$defaultTag.'</td>
				</tr>
				<tr>
					<td style="border: 0px none; text-align: right;">Matériel requis: </td>
					<td style="border: 0px none; padding-left: 5px;">'.$defaultRequisite.'</td>
				</tr>
				<tr>
					<td style="border: 0px none; text-align: right;">Formule smile: </td>
					<td style="border: 0px none; padding-left: 5px;">'.$defaultSmile.'</td>
				</tr>
				<tr>
					<td style="border: 0px none; text-align: right;">Auteur: </td>
					<td style="border: 0px none; padding-left: 5px;">'.$defaultAuthor.'</td>
				</tr>
			</table>
		</div>
	</div>
	<div>
		<h2 style="text-align: center">'.$defaultTitle.'</h2>
		'.parse($defaultWork).'
	</div>';
?>