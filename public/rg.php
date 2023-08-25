<?php
// print backtrace, getting rid of repeated absolute path on each file
$e = new Exception();
print_r(str_replace('/home/httpd/html/dev.fetishonly.com/public_html/public/index.php', '', $e->getTraceAsString()));
?>
