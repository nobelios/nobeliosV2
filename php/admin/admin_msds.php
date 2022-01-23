<?php

//*****************************************************************
//	Nobelios V2.0 beta
//	Administration des fiches de sécurité
//	Script par Geoffrey HAUTECOUVERTURE
//	Toute reproduction totale ou partielle interdite
//	juillet - 2009
//*****************************************************************

//-----------------------------------------------------------------
// Vérification des droits et lecture des msds
//-----------------------------------------------------------------

// Test si membre autorisé à rédiger ou modifier de pages
if (isset($_SESSION['login']) && isset($_SESSION['password']) && $userLevel >= 3) {
echo "droit disponible";
}

// Si il n'a pas le droit de poster une page
else
{
	include_once('./php/login.php');
}

?>