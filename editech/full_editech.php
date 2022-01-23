<script type="text/javascript" src="./editech/full_editech.js"></script>

<div style="padding: 0px; margin: 5px">
	<div>
		<p class="editechButton" style="padding: 0px; margin: 0px 0px 0px 0px; height:22px">
			<!-- Balises de formatage de texte -->
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/bold.png" onclick="insertTag('&lt;bold&gt;','&lt;/bold&gt;','textarea'); return false;" title="bold" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/italic.png" onclick="insertTag('&lt;italic&gt;','&lt;/italic&gt;','textarea'); return false;" title="italic" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/underline.png" onclick="insertTag('&lt;underline&gt;','&lt;/underline&gt;','textarea'); return false;" title="underline" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/strike.png" onclick="insertTag('&lt;strike&gt;','&lt;/strike&gt;','textarea'); return false;" title="strike" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/sub.png" onclick="insertTag('&lt;sub&gt;','&lt;/sub&gt;','textarea'); return false;" title="sub" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/sup.png" onclick="insertTag('&lt;sup&gt;','&lt;/sup&gt;','textarea'); return false;" title="sup" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/center.png" onclick="insertTag('&lt;center&gt;','&lt;/center&gt;','textarea'); return false;" title="center" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/title1.png" onclick="insertTag('&lt;title1&gt;','&lt;/title1&gt;','textarea'); return false;" title="title 1" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/title2.png" onclick="insertTag('&lt;title2&gt;','&lt;/title2&gt;','textarea'); return false;" title="title 2" alt="" />
			
			<!-- Balises d'insertions -->
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/list.png" onclick="insertTag('&lt;list&gt;','&lt;/list&gt;','textarea', 'list'); return false;" title="list" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/listnum.png" onclick="insertTag('&lt;listnum&gt;','&lt;/listnum&gt;','textarea', 'list'); return false;" title="listnum" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/table.png" onclick="insertTag('&lt;table&gt;','&lt;/table&gt;','textarea', 'table'); return false;" title="table" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/link.png" onclick="insertTag('&lt;link&gt;','&lt;/link&gt;','textarea', 'link'); return false;" title="link" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/code.png" onclick="insertTag('&lt;code type=&quot;asm&quot;&gt;','&lt;/code&gt;','textarea'); return false;" title="code" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/image.png" onclick="insertTag('<?php echo $folderDir; ?>/images','','textarea','addImage'); return false;" title="image" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/file.png" onclick="insertTag('<?php echo $folderDir; ?>/files','','textarea','addFile'); return false;" title="file" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/video.png" onclick="insertTag('<?php echo $folderDir; ?>/videos','','textarea','addFlash'); return false;" title="video et son" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/picto.png" onclick="insertTag('<?php echo $folderDir; ?>','','textarea','addMSDS'); return false;" title="picto" alt="" />
			
			<!-- Balises d'avertissements -->
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/hazard.png" onclick="insertTag('&lt;hazard&gt;','&lt;/hazard&gt;','textarea'); return false;" title="hazard" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/warning.png" onclick="insertTag('&lt;warning&gt;','&lt;/warning&gt;','textarea'); return false;" title="warning" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/query.png" onclick="insertTag('&lt;query&gt;','&lt;/query&gt;','textarea'); return false;" title="query" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/answer.png" onclick="insertTag('&lt;answer&gt;','&lt;/answer&gt;','textarea'); return false;" title="answer" alt="" />
			<img class="imageButton" src="./design/normal/editech/<?php echo $language; ?>/information.png" onclick="insertTag('&lt;information&gt;','&lt;/information&gt;','textarea'); return false;" title="information" alt="" />
		</p>
		<!--
		<select onchange="insertTag('&lt;taille valeur=&quot;' + this.options[this.selectedIndex].value + '&quot;&gt;', '&lt;/taille&gt;', 'textarea'); return false;">
			<option value="none" class="selected" selected="selected">Message</option>
			<option value="danger">danger</option>
			<option value="attention">attention</option>
			<option value="important">important</option>
			<option value="question">question</option>
			<option value="reponse">reponse</option>
		</select>
		-->
	</div>
	<div>
		<input name="previsualisation" type="checkbox" id="previsualisation" value="previsualisation" onclick="hideDiv('previsualisation', 'previewDiv'); preview(textarea, 'previewDiv');" />
		<label for="previsualisation">Pr&eacute;visualisation automatique</label>
	</div>
	<div>
		<textarea  onkeyup="preview(this, 'previewDiv');" onselect="preview(this, 'previewDiv');" style="height:400px; width:99%" name="work" id="textarea"><?php echo $defaultWork; ?></textarea>
	</div>
	<div id="previewDiv" style="background-color: #FFFFFF; border: 1px solid black; filter:alpha(opacity=80);-moz-opacity:0.80; opacity: 0.80; height:400px; width:99%; overflow:auto; text-align:justify; display: none;"></div>
</div>