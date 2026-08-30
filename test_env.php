<?php
echo "DB_HOST: " . getenv('DB_HOST') . "<br>";
echo "DB_PORT: " . getenv('DB_PORT') . "<br>";
echo "DB_NAME: " . getenv('DB_NAME') . "<br>";
echo "DB_USER: " . getenv('DB_USER') . "<br>";
echo "DB_PASS: " . (getenv('DB_PASS') ? 'Đã set' : 'Chưa set') . "<br>";
?>
