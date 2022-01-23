<?php

//=================================================================
// Espace utilisateur
//=================================================================

//-----------------------------------------------------------------
// Accueil de l'espace utilisateur
//-----------------------------------------------------------------

echo '<div>
	<h2>Mon compte</h2>
	<div>affichage membre rapide</div>
	<div class="settingsMenuDiv">
		<a href="./index.php?user_profil"><img src="' . $userDesignPath  . '/images/user_profil.png" /><span> Modifier mon profil</span></a>
		<a href="./index.php?user_avatars"><img src="' . $userDesignPath . '/images/user_avatars.png" /><span> Mes avatars</span></a>
		<a href="./index.php?user_files"><img src="' . $userDesignPath . '/images/user_files.png" /><span> Mes fichiers</span></a>
		<a href="./index.php?user_pages_editor"><img src="' . $userDesignPath . '/images/user_pages_editor.png" /><span> Proposer une page</span></a>
		<a href="./index.php?user_msds_editor"><img src="' . $userDesignPath . '/images/user_pages_editor.png" /><span> Proposer une msds</span></a>
	</div>
</div>';

/*

		
		<a href="./index.php?user_page"><img src="' . $userDesignPath . '/images/user_page.png" /><span> Modifier ma page perso</span></a>
		<a href="./index.php?user_preferences"><img src="' . $userDesignPath . '/images/user_preferences.png" /><span> Mes préférences</span></a>
		<a href="./index.php?user_messages"><img src="' . $userDesignPath . '/images/user_messages.png" /><span> Mes messages</span></a>
		<a href="./index.php?user_friends"><img src="' . $userDesignPath . '/images/user_friends.png" /><span> Mes amis</span></a>
		<a href="./index.php?user_stats"><img src="' . $userDesignPath . '/images/user_stats.png" /><span> Mes statistiques</span></a>
		<a href="./index.php?user_make_news"><img src="' . $userDesignPath . '/images/user_make_news.png" /><span> Proposer une news</span></a>
		<a href="./index.php?user_news"><img src="' . $userDesignPath . '/images/user_news.png" /><span> Mes news</span></a>
		<a href="./index.php?user_works"><img src="' . $userDesignPath . '/images/user_works.png" /><span> Mes pages</span></a>
		<a href="./index.php?user_images"><img src="' . $userDesignPath . '/images/user_images.png" /><span> Mes images</span></a>
		<a href="./index.php?user_movies"><img src="' . $userDesignPath . '/images/user_movies.png" /><span> Mes vidéos</span></a>
		


*/


//-----------------------------------------------------------------
// Copies de sauvegarde de l'utilisateur
//-----------------------------------------------------------------

// Sauvegarde du compte utilisateur
// zipFile('web/users/' . $userId, $userId, 'Nobelios_user_' . $userId .  '_backup');

// Sauvegarde des travaux en cours de l'utilisateur
// zipFile('web/users/' . $userId, $userId, 'Nobelios_user_' . $userId .  '_backup');

?>