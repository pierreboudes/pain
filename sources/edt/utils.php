<?php
function postclean($s) {
    if (isset($_POST[$s])) {
	if(get_magic_quotes_gpc()) {
	    return mysql_real_escape_string(stripslashes(($_POST[$s])));
	}
	else {
	    return mysql_real_escape_string($_POST[$s]);
	}
    }
    else return NULL;
}

?>