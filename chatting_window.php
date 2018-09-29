<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<iframe src="database.php?chatting_room_no=<? echo $_GET['room_no']?>" frameborder="0" width="500" height="500"></iframe>
<form action='database.php' method='get'>
    <input type='submit'>
    <input type='text' name='content'>
    <input type='hidden' name = 'room_no' value=<? echo $_GET['room_no']?> >
</form>
<button onclick='location.href="database.php?delete_room_no=<? echo $_GET['room_no']?>"'>나가기</button>
<button onclick='location.href="home.php"'>home으로</button>
</body>
</html>
