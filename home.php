<?php
if(!isset($_SESSION))
{
    session_start();
}
include "database.php";
$db = new database();
$db->home();

?>
<button onclick="location.href='make_room.html'">방 만들기</button>
