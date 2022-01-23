<?php

?>

<script language="javascript" type="text/javascript">

function insertTag(startTag, endTag, textareaId, tagType)
{
	var field  = document.getElementById(textareaId); 
	var scroll = field.scrollTop;
	field.focus();

	/* === Partie 1 : on récupère la sélection === */
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
			// On veut insérer une liste à puces
			//-----------------------------------------------------------------
			case "liste":
				currentSelection = "";
				sample = "";
				startTag = startTag.concat("\n");
				var i = 0;
				do{
					if (i==0) var nbElement = "Il n'y a aucun élément dans la liste:\n\n";
					else if (i==1) var nbElement = "Il y a 1 élément dans la liste:\n\n";
					else var nbElement = "Il n'y a " + i + " éléments dans la liste:\n\n";
					var saisie = prompt(nbElement  + sample + "\nCliquez sur ok pour ajouter un élement ou annuler pour terminer la liste", "");
					if ((saisie!=null) && (saisie!="")){
						++i;
						var valeur = "<li>" + saisie + "</li>\n";
						sample = sample.concat("- " + saisie + "\n");
						currentSelection = currentSelection.concat(valeur);	}
				} while (saisie!=null)
				if (currentSelection=="") {
					startTag = "";
					endTag = "";
				}
				break;
				
			//-----------------------------------------------------------------
			// On veut insérer une liste numérotée
			//-----------------------------------------------------------------
			case "listeNum":
				currentSelection = "";
				sample = "";
				startTag = startTag.concat("\n");
				var i = 0;
				do{
					if (i==0) var nbElement = "Il n'y a aucun élément dans la liste:\n\n";
					else if (i==1) var nbElement = "Il y a 1 élément dans la liste:\n\n";
					else var nbElement = "Il n'y a " + i + " éléments dans la liste:\n\n";
					var saisie = prompt(nbElement  + sample + "\nCliquez sur ok pour ajouter un élement ou annuler pour terminer la liste", "");
					if ((saisie!=null) && (saisie!="")){
						++i;
						var valeur = "<li>" + saisie + "</li>\n";
						sample = sample.concat(i + ".\t" + saisie + "\n");
						currentSelection = currentSelection.concat(valeur);	}
				} while (saisie!=null)
				if (currentSelection=="") {
					startTag = "";
					endTag = "";
				}
				break;
				
			//-----------------------------------------------------------------
			// On veut insérer une image
			//-----------------------------------------------------------------
			case "addImages":
				window.open("./Neditor/images.php?id=" + startTag,"images","directories=no, location=no, menubar=no, status=no, scrollbars=yes, height=400, width=650");
				startTag = ""
				endTag = "";
				break;
		}
	}
	
	/* === Partie 3 : on insère le tout === */
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
		var smiliesName = new Array(':magicien:', ':colere:', ':diable:', ':ange:', ':ninja:', '&gt;_&lt;', ':pirate:', ':zorro:', ':honte:', ':soleil:', ':\'\\(', ':waw:', ':\\)', ':D', ';\\)', ':p', ':lol:', ':euh:', ':\\(', ':o', ':colere2:', 'o_O', '\\^\\^', ':\\-Â°');
		var smiliesUrl  = new Array('magicien.png', 'angry.gif', 'diable.png', 'ange.png', 'ninja.png', 'pinch.png', 'pirate.png', 'zorro.png', 'rouge.png', 'soleil.png', 'pleure.png', 'waw.png', 'smile.png', 'heureux.png', 'clin.png', 'langue.png', 'rire.gif', 'unsure.gif', 'triste.png', 'huh.png', 'mechant.png', 'blink.gif', 'hihi.png', 'siffle.png');
		var smiliesPath = "http://www.siteduzero.com/Templates/images/smilies/";
		
		field = field.replace(/&/g, '&amp;');
		field = field.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		field = field.replace(/\n/g, '<br />').replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
		
		field = field.replace(/&lt;ul&gt;([\s\S]*?)&lt;\/ul&gt;/g, '<ul>$1</ul>');
		field = field.replace(/&lt;ol&gt;([\s\S]*?)&lt;\/ol&gt;/g, '<ol>$1</ol>');
		field = field.replace(/&lt;li&gt;([\s\S]*?)&lt;\/li&gt;/g, '<li>$1</li>');
		field = field.replace(/&lt;gras&gt;([\s\S]*?)&lt;\/gras&gt;/g, '<strong>$1</strong>');
		field = field.replace(/&lt;italique&gt;([\s\S]*?)&lt;\/italique&gt;/g, '<em>$1</em>');
		field = field.replace(/&lt;lien&gt;([\s\S]*?)&lt;\/lien&gt;/g, '<a href="$1">$1</a>');
		field = field.replace(/&lt;lien url="([\s\S]*?)"&gt;([\s\S]*?)&lt;\/lien&gt;/g, '<a href="$1" title="$2">$2</a>');
		field = field.replace(/&lt;image&gt;([\s\S]*?)&lt;\/image&gt;/g, '<img src="$1" alt="Image" />');
		field = field.replace(/&lt;citation nom=\"(.*?)\"&gt;([\s\S]*?)&lt;\/citation&gt;/g, '<br /><span class="citation">Citation : $1</span><div class="citation2">$2</div>');
		field = field.replace(/&lt;citation lien=\"(.*?)\"&gt;([\s\S]*?)&lt;\/citation&gt;/g, '<br /><span class="citation"><a href="$1">Citation</a></span><div class="citation2">$2</div>');
		field = field.replace(/&lt;citation nom=\"(.*?)\" lien=\"(.*?)\"&gt;([\s\S]*?)&lt;\/citation&gt;/g, '<br /><span class="citation"><a href="$2">Citation : $1</a></span><div class="citation2">$3</div>');
		field = field.replace(/&lt;citation lien=\"(.*?)\" nom=\"(.*?)\"&gt;([\s\S]*?)&lt;\/citation&gt;/g, '<br /><span class="citation"><a href="$1">Citation : $2</a></span><div class="citation2">$3</div>');
		field = field.replace(/&lt;citation&gt;([\s\S]*?)&lt;\/citation&gt;/g, '<br /><span class="citation">Citation</span><div class="citation2">$1</div>');
		field = field.replace(/&lt;taille valeur=\"(.*?)\"&gt;([\s\S]*?)&lt;\/taille&gt;/g, '<span class="$1">$2</span>');
		
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

</script>

<div>
	<p>
		<input type="button" value="G" onclick="insertTag('&lt;gras&gt;','&lt;/gras&gt;','textarea'); return false;" />
		<input type="button" value="I" onclick="insertTag('&lt;em&gt;';,'&lt;/em&gt;';,'textarea'); return false;" />
		<input type="button" value="Liste" onclick="insertTag('&lt;ul&gt;';,'&lt;/ul&gt;';,'textarea','liste'); return false;" />
		<input type="button" value="Listenum" onclick="insertTag('&lt;ol&gt;','&lt;/ol&gt;';,'textarea','listeNum'); return false;" />
		<input type="button" value="Image" onclick="insertTag('<?php echo $dirImages;?>','','textarea','addImages'); return false;" />
		<select onchange="insertTag('&lt;taille valeur=&quot;' + this.options[this.selectedIndex].value + '&quot;&gt;', '&lt;/taille&gt;', 'textarea'); return false;">
			<option value="none" class="selected" selected="selected">Taille</option>
			<option value="ttpetit">Très très petit</option>
			<option value="tpetit">Très petit</option>
			<option value="petit">Petit</option>
			<option value="gros">Gros</option>
			<option value="tgros">Très gros</option>
			<option value="ttgros">Très très gros</option>
		</select>
		<img src="http://users.teledisnet.be/web/mde28256/smiley/smile.gif" alt="smiley" />
		<img src="http://users.teledisnet.be/web/mde28256/smiley/unsure2.gif" alt="smiley" />
	</p>
	<p>
		<input name="previsualisation" type="checkbox" id="previsualisation" value="previsualisation" />
		<label for="previsualisation">Pr&eacute;visualisation automatique</label>
	</p>
</div>
	<textarea rows="30" cols="80" name="work" id="textarea"><?php echo $defaultWork; ?></textarea><br />
<div id="previewDiv">
	<p>
		<input type="button" value="Visualiser" onclick="view('textarea','viewDiv'); return false;" />
	</p>
</div>
<div id="viewDiv">
<textarea onkeyup="preview(this, 'previewDiv');" onselect="preview(this, 'previewDiv');" id="textarea" cols="150" rows="10"></textarea>

</div>
<?php


?>