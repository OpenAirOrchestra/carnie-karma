<?php

/*
 * Renders a list of users, each linking to
 * a karma report for that user.
 */       
class carnieKarmaUsersView {
 
	/*
	 * Renders a list of users, each linking to a karma report
	 * for that user.
	 */
	function render($users) {

		$result = "<h2>Participants</h2>";

        $karma_list_nonce = wp_create_nonce('karma_list_nonce');


		$result .= "<UL>";
		

	
		foreach ($users as $user) {
			if ($user['ID'] != 1) {
				$result .= "<LI>";
				$result .= "
				<form method=\"post\" action=\"" . $_SERVER["REQUEST_URI"] . "\">
				<input type=\"hidden\" name=\"karma_list_nonce\" value=\"" .  $karma_list_nonce . "\" />
                <input type=\"hidden\" name=\"user_id\" value=\"" .  $user['ID'] . "\" />
				";

				$user_info = get_userdata($user['ID']);
				if ($user_info->first_name || $user_info->last_name) {
					$result .= $user_info->first_name . ' ' . $user_info->last_name;
					$result .= ' (' . $user['user_login'] . ')';
				} else {
					$result .= $user['display_name'];
					$result .= ' (' . $user['user_login'] . ')';
				}
				
				$result .= "
				<input type=\"submit\" value=\"Details\" />
			</form>
				";

		$result .= "</LI>";
			}
		}

		$result .= "</UL>";

		return $result;
	}
}
?>
