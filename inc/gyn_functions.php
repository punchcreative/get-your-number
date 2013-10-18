<?php
/**
 * Functions used by the plugin get-your-number
*/

function generate_unique_number() {
	$number = mt_rand(2,75);
	return $number;
}
?>