<?php
if($_GET['id'] == null || $_GET['pwd'] == null) {
    echo "<script>
                   alert('아이디와 패스워드를 입력하시오');
                   location.href = 'login.html';
           </script>";
} else  {
    include "database.php";
    $db = new database();
    $db->login_exception($_GET['id'], $_GET['pwd']);
}