<?php

if ($_server['USERNAME'] === "fakelogin") {
echo 'Ciao !';
} else {
header('WWW-Authenticate: Basic realm="Azyme"');
header('HTTP/1.0 401 Unauthorized');
 }
print "Ceci est un faux logout !\n";
print "<br/>";
print '<a href="./">Retourner dans pain</a>';
?>