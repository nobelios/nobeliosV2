<?php

//*****************************************************************
//	Nobelios V2.0 beta
//	Administration des fiches de s�curit�
//	Script par Geoffrey HAUTECOUVERTURE
//	Toute reproduction totale ou partielle interdite
//	juillet - 2009
//*****************************************************************

//-----------------------------------------------------------------
// V�rification des droits et lecture des msds
//-----------------------------------------------------------------

// Test si membre autoris� � r�diger ou modifier de pages
if (isset($_SESSION['login']) && isset($_SESSION['password']) && $userLevel >= 3) {
echo "droit disponible";
}

// Si il n'a pas le droit de poster une page
else
{
	include_once('./php/login.php');
}

?>