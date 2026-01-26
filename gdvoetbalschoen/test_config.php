<?php
require_once('phpcode/config.php');
echo "base_url = '$base_url'<br>";
echo "is_local = " . ($is_local ? 'ja' : 'nee') . "<br>";
echo "HTTP_HOST = " . $_SERVER['HTTP_HOST'] . "<br>";
