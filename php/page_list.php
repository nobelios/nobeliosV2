<?php

//-----------------------------------------------------------------
// Liste des pages class�es par sujet et par type
// Avec moteur de recherche l�ger
//-----------------------------------------------------------------

//-----------------------------------------------------------------
// Choix du sujet
//-----------------------------------------------------------------
if (isset($_GET['subject']) && in_array($_GET['subject'], $allowedSubject))
{
	//-----------------------------------------------------------------
	// Choix du type
	//-----------------------------------------------------------------
	if (isset($_GET['type']) && in_array($_GET['type'], $allowedType))
	{		
		// D�finition des chemins et des dossiers contenant les pages.
		$folderDir = "./web/".$language."/pages/works";
		$nb = nbFolder($folderDir);
		$folder = testDir($folderDir);
		
		// D�claration des tables de donn�es
		$tableId = array();
		$tableTitle = array();
		$tableReference = array();		
		
		//-----------------------------------------------------------------
		// G�n�ration d'un tableau avec les liens (boucle)
		//-----------------------------------------------------------------
		for ($i=0; $i<$nb; $i++) {
			if (in_array($folder[$i], $folder))
			{
				// Lecture des propri�t�s de la page
				$openProperties = fopen($folderDir.'/'.$folder[$i].'/properties.txt', 'r');
				if ($openProperties)
				{
					$defaultProperties = fgets($openProperties, 4096);
					fclose($openProperties);
					
					$defaultSubject		= preg_match('#.*\[subject\](.+)\[/subject\].*#i', $defaultProperties)		? preg_replace('#.*\[subject\](.+)\[/subject\].*#i', '$1', $defaultProperties) 		: '';
					$defaultType		= preg_match('#.*\[type\](.+)\[/type\].*#i', $defaultProperties)			? preg_replace('#.*\[type\](.+)\[/type\].*#i', '$1', $defaultProperties) 			: '';
					$defaultTitle		= preg_match('#.*\[title\](.+)\[/title\].*#i', $defaultProperties)			? preg_replace('#.*\[title\](.+)\[/title\].*#i', '$1', $defaultProperties) 			: '';
					$defaultReference	= preg_match('#.*\[reference\](.+)\[/reference\].*#i', $defaultProperties) 	? preg_replace('#.*\[reference\](.+)\[/reference\].*#i', '$1', $defaultProperties) 	: 'none';
					
					if (isset($_GET['from']) && ($_GET['from'] == 'title'))
						$from = $defaultTitle;
					else
						$from = $defaultReference;
					
					// Tableau contenant les propri�t�es des pages
					if (($_GET['subject'] == $defaultSubject) && ($_GET['type'] == $defaultType)) {
						// Recherche parmis les titres
						if ((isset($_GET['search']) && preg_match('#' . htmlentities($_GET['search']) . '#i', $from)) || !isset($_GET['search'])) {
							$tableSort[$folder[$i]] = strtolower($defaultTitle);
							$tableTitle[$folder[$i]] = strtolower($defaultTitle);
							$tableReference[$folder[$i]] = $defaultReference;
						}
					}
				}
			}
		}
		
		// D�claration d'un tableau multiple avec les titres et les r�f�rences.
		$tableSort = array(
			'title'		=> $tableTitle,
			'reference'	=> $tableReference
		);
		
		//-----------------------------------------------------------------
		// G�n�ration d'un tableau avec les liens (boucle)
		//-----------------------------------------------------------------
		
		// On proc�de � un tri sur le type de donn�es	
		if (isset($_GET['order']) && ($_GET['order'] == 'reference')) {
			$order = $_GET['order'];
			$tableOrder = $tableSort[$order];
		} else {
			$order = 'title';
			$tableOrder = $tableSort[$order];
		}
		
		// On proc�de � un tri alphanum�rique
		if (isset($_GET['desc'])) {
			$sort = 'desc';
			arsort($tableOrder);
		} else {
			$sort = 'asc';
			asort($tableOrder);
		}
		
		//-----------------------------------------------------------------
		// Affichage des param�tres de recherche en cours
		//-----------------------------------------------------------------
		if (!empty($_GET['search']))
			$search = $_GET['search'];
		else
			$search = '';
		
		echo '
		<div class="pageLinker">
			<a href="./index.php?home">Accueil</a> > 
			<a href="./index.php?page_list">Pages</a> > 
			<a href="./index.php?page_list&amp;subject='.$_GET['subject'].'">'.ucfirst($_GET['subject']).'</a> > 
			'.ucfirst($_GET['type']).'<br />
		</div>
		<div class="pageFolderInfo"">
			<div class="pageImage">
				<a href="./index.php?page_list&amp;subject='.$_GET['subject'].'"><img class="imageButton" src="'.$userDesignPath.'/'.$_GET['subject'].'.jpg" /></a>
			</div>
			<div class="pageImage">
				<a href="./index.php?page_list&amp;subject='.$_GET['subject'].'&amp;type='.$_GET['type'].'"><img class="imageButton" src="'.$userDesignPath.'/'.$_GET['type'].'.jpg" /></a><br />
			</div>
			<div class="pageText">
				Moteur de recherche rapide : <br />
				'.count($tableTitle).' resulats correspondent � votre recherche.<br />
				<form method="get" action="./index.php">
					<input type="hidden" name="page_list" />
					<input type="hidden" name="subject" value="'.$_GET['subject'].'" />
					<input type="hidden" name="type" value="'.$_GET['type'].'" />
					<input type="hidden" name="order" value="'.$order.'" />
					<input type="hidden" name="'.$sort.'" />
					<input type="text" name="search" value="'.$search.'" size="40" maxlength="100" />
					<select name="from">
						<option value="title">titre</option>
						<option value="reference">r�f�rence</option>
					</select>
					<input type="submit" value="Recherche" />
				</form>
				'./* Afficher que si l'utilisateur est connect� */'<a href="">Afficher uniquement mes travaux</a>
			</div>
		</div>';
		
		
		//-----------------------------------------------------------------
		// Affichage des r�sulats de recherche en cours
		//-----------------------------------------------------------------
		
		// Definition du num�ro de page
		if (isset($_GET['page']) && ($_GET['page'])>0) $numPage = floor($_GET['page']); // Le num�ro de page est envoy� par l'adresse
		else $numPage = 1; // Par d�faut on affiche la premi�re page
		
		echo '<div class="pageList">';
		
		// Liste des num�ros de page
		if ($pageWiew == 'number') {
			$nbPage = ceil(count($tableTitle)/$nbResultByPage);
			echo '<br />page: | '; 
			for ($i=1; $i<=$nbPage; $i++)
				echo '<a href="./index.php?page_list&amp;subject='.$_GET['subject'].'&amp;type='.$_GET['type'].'&amp;order='.$order.'&amp;page='.$i.'&amp;'.$sort.'">'.$i.'</a> | ';
			echo '<br /><br />';
		}
		
		// G�n�ration d'un tableau de liens (avec r�cup�ration des donn�es de recherche et de classement)
		echo '<table cellspacing="0px" class="pageListLegendTable">
				<tr class="pageListLegendRow">
					<td class="pageListLegendCell">
						Titre 
						
					</td>
					<td class="pageListLegendCell">
						R�f�rence 
					</td>
				</tr>';
		
		// On liste les liens que l'on veut afficher
		if (count($tableOrder) != 0)
		{
			$i=0; // Raz du compteur
			foreach ($tableOrder as $key=>$val) {
				if ($i>=(($numPage-1)*$nbResultByPage) && $i<(($numPage-1)*$nbResultByPage+$nbResultByPage)) {
					if (($i%2) == 0) { 
						echo '
						<tr class="pageListRowColor1">
							<td class="pageListCell"><a href="./index.php?page_viewer&amp;id='.$key.'">'.ucfirst($tableTitle[$key]).'</a></td>
							<td class="pageListCell"><a href="./index.php?page_viewer&amp;id='.$key.'">'.$tableReference[$key].'</a></td>
						</tr>';
					} else {
						echo '
						<tr class="pageListRowColor2">
							<td class="pageListCell"><a href="./index.php?page_viewer&amp;id='.$key.'">'.ucfirst($tableTitle[$key]).'</a></td>
							<td class="pageListCell"><a href="./index.php?page_viewer&amp;id='.$key.'">'.$tableReference[$key].'</a></td>
						</tr>';
					}
				}
				$i++;
			}
		}
		else
		{
			echo '
				<tr class="pageListRowColor1">
					<td class="pageListCell">Il n\'y a aucune page dans ce dossier.</td>
					<td class="pageListCell"></td>
				</tr>';
		}
		
		echo '</table>';
		
		// Liste des num�ros de page
		if ($pageWiew == 'number') {
			$nbPage = ceil(count($tableTitle)/$nbResultByPage);
			echo '<br />page: | '; 
			for ($i=1; $i<=$nbPage; $i++)
				echo '<a href="./index.php?page_list&amp;subject='.$_GET['subject'].'&amp;type='.$_GET['type'].'&amp;order='.$order.'&amp;page='.$i.'&amp;'.$sort.'">'.$i.'</a> | ';
		}
		echo '</div>';
	}
	// Par d�faut si aucun type de donn�e n'est d�finit on affiche une liste avec tout les types de donn�es disponibles.
	else
	{
		echo '
		<div class="pageLinker">
			<a href="./index.php?home">Accueil</a> > 
			<a href="./index.php?page_list">Pages</a> > 
			'.ucfirst($_GET['subject']).'
		</div>
		<div class="pageFolderInfo">
			<div class="pageImage">
				<img src="'.$userDesignPath.'/'.$_GET['subject'].'.jpg" />
			</div>
			<div class="pageText">
				Vous consulter la rubrique '.$_GET['subject'].'
			</div>
		</div>
		<div class="hrefDiv">
		<div style="margin-top: 15px;">';
			foreach ($allowedType as $val)
			{
				echo '<a href="./index.php?page_list&amp;subject='.$_GET['subject'].'&amp;type='.$val.'"><img src="'.$userDesignPath.'/'.$val.'.jpg" /><span> consulter la partie '.$val.'</span></a><br />';
			}
			echo '</div>
		</div>';
	}
}
// Par d�faut si aucun sujet n'est d�finit on affiche une liste avec tout les sujets disponibles.
else
{
	echo '
	<div class="pageLinker">
		<a href="./index.php?home">Accueil</a> > 
		Pages
	</div>
	<div class="hrefDiv" >';
	foreach ($allowedSubject as $val)
	{
		echo '<a href="./index.php?page_list&amp;subject='.$val.'"><img src="'.$userDesignPath.'/'.$val.'.jpg" /><span> consulter la partie '.$val.'</span></a><br />';
	}
	echo '</div>';
}

?>