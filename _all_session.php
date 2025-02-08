<?php 
echo "Session Save Path: " . ini_get( 'session.save_path');
$allSessions = [];
$sessionNames = scandir(session_save_path());

echo "<pre>"; print_r($sessionNames);
echo "<pre>"; print_r($_SERVER);


