function insertTag(startTag, endTag, textareaId, tagType)
{
	var field  = document.getElementById(textareaId); 
	var scroll = field.scrollTop;
	field.focus();

	/* === Partie 1 : on récupère la séclection === */
	if (window.ActiveXObject) {
		var textRange = document.selection.createRange();
		var currentSelection = textRange.text;
	} else {
		var startSelection   = field.value.substring(0, field.selectionStart);
		var currentSelection = field.value.substring(field.selectionStart, field.selectionEnd);
		var endSelection = field.value.substring(field.selectionEnd);
	}

	/* === Partie 2 : on analyse le tagType === */
	if (tagType) {
		switch (tagType) {
			
			//-----------------------------------------------------------------
			// On veut insérer une liste
			//-----------------------------------------------------------------
			case "list":
				currentSelection = "";
				sample = "";
				startTag = startTag.concat("\n");
				var i = 0;
				do {
					if (i==0) var nbElement = "Il n'y a aucun élément dans la liste:\n\n";
					else if (i==1) var nbElement = "Il y a 1 élément dans la liste:\n\n";
					else var nbElement = "Il n'y a " + i + " éléments dans la liste:\n\n";
					var saisie = prompt(nbElement  + sample + "\nCliquez sur ok pour ajouter un élément ou annuler pour terminer la liste", "");
					if ((saisie!=null) && (saisie!="")){
						++i;
						var valeur = "<item>" + saisie + "</item>\n";
						sample = sample.concat(i + ".\t" + saisie + "\n");
						currentSelection = currentSelection.concat(valeur);	}
				} while (saisie!=null)
				if (currentSelection=="") {
					startTag = "";
					endTag = "";
				}
				break;
				
			//-----------------------------------------------------------------
			// On veut insérer un lien
			//-----------------------------------------------------------------
			case "link":
				startTag = "<link>";
				if (currentSelection) { // Il y a une sélection
					if (currentSelection.indexOf("http://") == 0 || currentSelection.indexOf("https://") == 0 || currentSelection.indexOf("ftp://") == 0 || currentSelection.indexOf("www.") == 0) {
						// La sélection semble être un lien. On demande alors le libelle 
						var label = prompt("Entrez le libellé du lien.\n(cliquez sur annuler pour ne pas utiliser de libellé") || "";
						startTag = "<link url=\"" + currentSelection + "\">";
						currentSelection = label;
					} else {
						// La sélection n'est pas un lien, donc c'est le libellé. On demande alors l'URL
						var URL = prompt("Entrez l'url du lien.");
						startTag = "<link url=\"" + URL + "\">";
					}
				} else { // Pas de sélection, donc on demande l'URL et le libellé
					var URL = prompt("Entrez l'url du lien.") || "";
					var label = prompt("Entrez le libellé du lien.\n(cliquez sur annuler pour ne pas utiliser de libellé") || "";
					startTag = "<link url=\"" + URL + "\">";
					currentSelection = label;
				}
				break;
			
			//-----------------------------------------------------------------
			// On veut insérer une image
			//-----------------------------------------------------------------
			case "addImage":
				window.open("./editech/index.php?images&path=" + startTag,"images","resizable=yes, directories=no, location=no, menubar=no, status=no, scrollbars=yes, height=450, width=690");
				startTag = "";
				endTag = "";
				break;
				
			//-----------------------------------------------------------------
			// On veut insérer un fichier
			//-----------------------------------------------------------------
			case "addFile":
				window.open("./editech/index.php?files&path=" + startTag,"files","resizable=yes, directories=no, location=no, menubar=no, status=no, scrollbars=yes, height=450, width=690");
				startTag = "";
				endTag = "";
				break;
				
			//-----------------------------------------------------------------
			// On veut insérer une vidéo
			//-----------------------------------------------------------------
			case "addFlash":
				window.open("./editech/index.php?videos&path=" + startTag,"flash","resizable=yes, directories=no, location=no, menubar=no, status=no, scrollbars=yes, height=450, width=690");
				startTag = "";
				endTag = "";
				break;
				
			//-----------------------------------------------------------------
			// On veut insérer un tableau de MSDS
			//-----------------------------------------------------------------
			case "addMSDS":
				window.open("./editech/index.php?msds&path=" + startTag,"msds","resizable=yes, directories=no, location=no, menubar=no, status=no, scrollbars=yes, height=450, width=690");
				startTag = "";
				endTag = "";
				break;
		}
	}
	
	/* === Partie 3 : on insérer le tout === */
	if (window.ActiveXObject) {
		textRange.text = startTag + currentSelection + endTag;
		textRange.moveStart("character", -endTag.length - currentSelection.length);
		textRange.moveEnd("character", -endTag.length);
		textRange.select(); 
	} else {
		field.value = startSelection + startTag + currentSelection + endTag + endSelection;
		field.focus();
		field.setSelectionRange(startSelection.length + startTag.length, startSelection.length + startTag.length + currentSelection.length);
	} 
	
	field.scrollTop = scroll; 
}

function preview(textareaId, previewDiv) {
	var field = textareaId.value;
	if (document.getElementById('previsualisation').checked && field) {
		var smiliesName = new Array(':magicien:', ':colere:', ':diable:', ':ange:', ':ninja:', '&gt;_&lt;', ':pirate:', ':zorro:', ':honte:', ':soleil:', ':\'\\(', ':waw:', ':\\)', ':D', ';\\)', ':p', ':lol:', ':euh:', ':\\(', ':o', ':colere2:', 'o_O', '\\^\\^', ':\\-°');
		var smiliesUrl  = new Array('magicien.png', 'angry.gif', 'diable.png', 'ange.png', 'ninja.png', 'pinch.png', 'pirate.png', 'zorro.png', 'rouge.png', 'soleil.png', 'pleure.png', 'waw.png', 'smile.png', 'heureux.png', 'clin.png', 'langue.png', 'rire.gif', 'unsure.gif', 'triste.png', 'huh.png', 'mechant.png', 'blink.gif', 'hihi.png', 'siffle.png');
		var smiliesPath = "http://www.siteduzero.com/Templates/images/smilies/";
		
		field = field.replace(/&/g, '&amp;');
		field = field.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		
		// Listes
		field = field.replace(/[\r|\n]*&lt;list&gt;([\s\S]*?)[\r|\n]*&lt;\/list&gt;[\r|\n]?/g, '<ul>$1</ul>');
		field = field.replace(/[\r|\n]*&lt;listnum&gt;([\s\S]*?)[\r|\n]*&lt;\/listnum&gt;[\r|\n]?/g, '<ol>$1</ol>');
		field = field.replace(/[\r|\n]*&lt;item&gt;([\s\S]*?)&lt;\/item&gt;[\r|\n]*/g, '<li>$1</li>');
		
		// Tableaux
		field = field.replace(/&lt;table center&gt;([\s\S]*?)&lt;\/table&gt;[\r|\n]?/g, '<table class="editechTableCenter">$1</table>');
		field = field.replace(/&lt;table&gt;([\s\S]*?)&lt;\/table&gt;[\r|\n]?/g, '<table class="editechTable">$1</table>');
		field = field.replace(/[\r|\n]*&lt;head&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/head&gt;[\r|\n]*/g, '<tr class="editechHead">$1</tr>');
		field = field.replace(/[\r|\n]*&lt;line&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/line&gt;[\r|\n]*/g, '<tr class="editechLine">$1</tr>');
		field = field.replace(/[\r|\n]*&lt;cell col="([\s\S]*?)" row="([\s\S]*?)"&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/cell&gt;[\r|\n]*/g, '<td colspan="$1" rowspan="$2" style="border: 1px solid black;">$3</td>');
		
		// Formatage
		field = field.replace(/&lt;bold&gt;([\s\S]*?)&lt;\/bold&gt;/g, '<strong>$1</strong>');
		field = field.replace(/&lt;italic&gt;([\s\S]*?)&lt;\/italic&gt;/g, '<em>$1</em>');
		field = field.replace(/&lt;underline&gt;([\s\S]*?)&lt;\/underline&gt;/g, '<span class="underline">$1</span>');
		field = field.replace(/&lt;strike&gt;([\s\S]*?)&lt;\/strike&gt;/g, '<span class="strike">$1</span>');
		field = field.replace(/&lt;sub&gt;([\s\S]*?)&lt;\/sub&gt;/g, '<sub>$1</sub>');
		field = field.replace(/&lt;sup&gt;([\s\S]*?)&lt;\/sup&gt;/g, '<sup>$1</sup>');
		field = field.replace(/&lt;title1&gt;([\s\S]*?)&lt;\/title1&gt;[\r|\n]*/g, '<h2 class="editechTitle1">$1</h2><br />');
		field = field.replace(/&lt;title2&gt;([\s\S]*?)&lt;\/title2&gt;[\r|\n]*/g, '<h3 class="editechTitle2">$1</h3><br />');
		field = field.replace(/&lt;link url="([\s\S.@]*?)"&gt;&lt;\/link&gt;/g, '<a href="$1">$1</a>');
		field = field.replace(/&lt;link url="([\s\S.@]*?)"&gt;([\s\S]*?)&lt;\/link&gt;/g, '<a href="$1">$2</a>');
		field = field.replace(/&lt;center&gt;([\s\S]*?)&lt;\/center&gt;/g, '<div class="editechCenter">$1</div>');
		
		// Images, vidéos
		field = field.replace(/[\r|\n]?&lt;image url="([\s\S]*?)" title="([\s\S]*?)"&gt;/g, '<img src="$1" title="$2" alt="image" />');
		field = field.replace(/[\r|\n]?&lt;imagemini url="([\s\S]*?)" title="([\s\S]*?)"&gt;/g, '<a href="$1"><img class="editechImageMini" src="$1" title="$2" alt="image" /></a>');
		field = field.replace(/[\r|\n]?&lt;video url="([\s\S]*?)"&gt;/g, '<object width="480px" height="360px" type="application/x-shockwave-flash" data="./flash/player.swf"><param name="movie" value="./flash/player.swf" /><param name="flashvars" value="file=$1" /></object>');
		field = field.replace(/[\r|\n]?&lt;sound url="([\s\S]*?)"&gt;/g, '<object width="350px" height="20px" type="application/x-shockwave-flash" data="./flash/player.swf"><param name="movie" value="./flash/player.swf" /><param name="flashvars" value="file=$1" /></object>');
		
		// Zones de texte
		field = field.replace(/[\r|\n]?&lt;hazard&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/hazard&gt;/g, '<p class="editechHazard">$1</p>');
		field = field.replace(/[\r|\n]?&lt;warning&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/warning&gt;/g, '<p class="editechWarning">$1</p>');
		field = field.replace(/[\r|\n]?&lt;information&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/information&gt;/g, '<p class="editechInformation">$1</p>');
		field = field.replace(/[\r|\n]?&lt;query&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/query&gt;/g, '<p class="editechQuery">$1</p>');
		field = field.replace(/[\r|\n]?&lt;answer&gt;[\r|\n]*([\s\S]*?)[\r|\n]*&lt;\/answer&gt;/g, '<p class="editechAnswer">$1</p>');
		
		
		field = field.replace(/\n/g, '<br />').replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
		
		for (var i=0, c=smiliesName.length; i<c; i++) {
			field = field.replace(new RegExp(" " + smiliesName[i] + " ", "g"), "&nbsp;<img src=\"" + smiliesPath + smiliesUrl[i] + "\" alt=\"" + smiliesUrl[i] + "\" />&nbsp;");
		}
		
		document.getElementById(previewDiv).innerHTML = field;
	}
}

function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try	{
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest();
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}

function view(textareaId, viewDiv) {
	var content = encodeURIComponent(document.getElementById(textareaId).value);
	var xhr = getXMLHttpRequest();
	
	if (xhr && xhr.readyState != 0)	{
		xhr.abort();
		delete xhr;
	}
	
	xhr.onreadystatechange = function()	{
		if (xhr.readyState == 4 && xhr.status == 200) {
			document.getElementById(viewDiv).innerHTML = xhr.responseText;
		} else if (xhr.readyState == 3) {
			document.getElementById(viewDiv).innerHTML = "<div style=\"text-align: center;\">Chargement en cours...</div>";
		}
	}
	
	xhr.open("POST", "view.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("string=" + content);
}