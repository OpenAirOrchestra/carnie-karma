<?php

/*
 * Renders a workshop karma report for a user.
 */       
class carnieKarmaWorkshopsView {
 
	/*
 	 * Renders a workshop karma report for a user.
	 */
	function render($users_id, $workshops) {

                echo "<h2>TODO: Workshop Participation Karma For User " . $user_id . "</h2>";

		echo "<ul>";
		foreach ($workshops as $workshops) {
			echo "<li> workshop karma summary </li>";
		}
		echo "</ul>";
	}
}
?>
