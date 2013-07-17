<?php

$fp = stream_socket_client("tcp://portail.cevif.univ-paris13.fr:443", $errno, $errstr, 2);
if (!$fp) {
	    echo "$errstr ($errno)<br />\n";
} else {
	echo 'ok';

   fclose($fp);  

}

?>
