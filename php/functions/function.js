function checkAll(form,booleen,num_table) {
	for (i=0, n=form.elements.length; i<n; i++) {
		if (form.elements[i].id.indexOf('table'+num_table) != -1)
			form.elements[i].checked = booleen;
	}
}

function checkTheBox(form,box,num_table) {
	if (form.elements['table'+num_table+'_chk'+box].checked)
		form.elements['table'+num_table+'_chk'+box].checked = false;
	else
		form.elements['table'+num_table+'_chk'+box].checked = true;
}

function hideDiv(checkboxId, divId) {
	if (document.getElementById(checkboxId).checked)
		document.getElementById(divId).style.display = 'block';
	else
		document.getElementById(divId).style.display = 'none';
}

// id = id du bloc (<div>, <p> ...) contenant les checkbox
// action = '0' pour tout décocher
// action = '1' pour tout cocher
// action = '2' pour inverser la sélection	
function actionCheckbox(id, nb_elements, action) {
	var blnEtat=null;
	for (i=0; i<nb_elements ; i++) {
		var Chckbox = document.getElementById(id+i).firstChild;
		while (Chckbox!=null) {
			if (Chckbox.nodeName=="INPUT") {
				if (Chckbox.getAttribute("type")=="checkbox") {
					blnEtat = (action=='0') ? false : (action=='1') ? true : (document.getElementById(Chckbox.getAttribute("id")).checked) ? false : true;
					document.getElementById(Chckbox.getAttribute("id")).checked=blnEtat;
				}
			}
			Chckbox = Chckbox.nextSibling;
		}
	}
}

function checkTheBox(id, i) {
	var blnEtat=null;
	var Chckbox = document.getElementById(id+i).firstChild;
	while (Chckbox!=null) {
		if (Chckbox.nodeName=="INPUT") {
			if (Chckbox.getAttribute("type")=="checkbox") {
				blnEtat = (document.getElementById(Chckbox.getAttribute("id")).checked) ? false : true;
				document.getElementById(Chckbox.getAttribute("id")).checked=blnEtat;
			}
		}
		Chckbox = Chckbox.nextSibling;
	}
}