<?php

$db = new database();
if(isset($_GET['room']))    {
    $db->room_make($_GET['room']);
} else if(isset($_GET['content'])) {
    $db->content_send($_GET['content'], $_GET['room_no']);
} else if(isset($_GET['delete_room_no']))   {
    $db->room_delete($_GET['delete_room_no']);
} else if(isset($_GET['chatting_room_no']))  {
    $db->chatting_window($_GET['chatting_room_no']);
}

class database
{
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "autoset";
    const DB_NAME = "chatting";
    public $db_con;

    function __construct()
    {
        $this->db_con = mysqli_connect(self::HOST, self::USER, self::PASSWORD, self::DB_NAME);
    }

    function login_exception($id, $pw)
    {
        $loginNum = 0;
        $sql = "SELECT * FROM user_info";
        $result = $this->db_con->query($sql);
        while($row = $result->fetch_array())
        {
            if ($row[0] == $id) {
                $loginNum++;
                if ($row[1] == $pw) {
                    $loginNum++;
                }
            }
        }
        switch ($loginNum) {
            case 2:
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['user'] = $id;
                echo "<script>alert('환영합니다.');
                    location.href = 'home.php';</script>";
                break;
            case 1:
                echo "<script>alert('잘못된 패스워드입니다.');
                    location.href = 'login.html';</script>";
                break;
            case 0:
                echo "<script>alert('잘못된 아이디입니다.');
                     location.href = 'login.html';</script>";
                break;
        }
    }


    function home()
    {

        // 방번호, 방이름, 개설날짜
        $sql = "select * from chat_list";
        $result = $this->db_con->query($sql);

        // list가 있을 때
            echo "<table>";
            while($row = $result->fetch_array())
            {
                $room_no = $row[0];
                $room_name = $row[1];
                $room_user = $room_name . "_user";
                $launch_date = $row[2];
                // 방번호에 따른 방장, 맴버수
                $sql = "SELECT userid FROM $room_user WHERE room_no = \"$room_no\"";
                $result_user = $this->db_con->query($sql);
                // 해당 테이블이 비어있지 않으면
                    if($result_user)   {
                        $row_user = $result_user->fetch_array();
                        $room_leader = $row_user[0];
                        $room_member = $result_user->num_rows;
                        echo "<tr>
                        <td>$room_no</td>
                        <td><a href=\"chatting_window.php?room_no=$row[0]\">$room_name</a></td>
                        <td>$room_leader</td>
                        <td>$room_member</td>
                        <td>$launch_date</td>
                  </tr>
                ";
                    }
            }
            echo "</table>";
        echo "<script>setInterval(function() { location.reload();}, 500)</script>";
    }


    function room_make($room_name)
    {
        $sql = "INSERT INTO chat_list (room_no, room_name, launch_date) VALUES (NULL, \"$room_name\", NOW());";
        $this->db_con->query($sql);
        $room_user = $room_name . "_user";
        $userid = $_SESSION['user'];
        $sql = "create table $room_user
                (room_no int(10) not null,
                 id int(10) auto_increment,
                 userid VARCHAR(50) not null,
                  PRIMARY KEY (id));";
        $this->db_con->query($sql);
        $sql = "SELECT room_no FROM chat_list WHERE room_name = \"$room_name\"";
        $result = $this->db_con->query($sql);
        $row = $result->fetch_array();
        $room_no = $row[0];
        $sql = "INSERT INTO $room_user (room_no, id, userid) VALUES ($room_no, NULL, \"$userid\")";
        $this->db_con->query($sql);
        $sql = "create table $room_name
                (room_no int(10) not null,
                 userid VARCHAR(50) not null,
                 content text);";
        $this->db_con->query($sql);
        $sql = "INSERT INTO $room_name (room_no, userid, content) VALUES ($room_no, \"$userid\", \"입장하셨습니다.\")";
        $this->db_con->query($sql);
        echo "<script>location.href = 'chatting_window.php?room_no=$room_no';</script>";
    }

    function chatting_window($room_no)
    {
        $sql = "select room_name from chat_list where room_no = $room_no";
        $result = $this->db_con->query($sql);
            $row = $result->fetch_array();
            $room_name = $row[0];
            $room_user = $room_name."_user";
            $user = $_SESSION['user'];
            $sql = "select userid from $room_user";
            $user_same = 0;
            $result = $this->db_con->query($sql);
            while($row = $result->fetch_array())
            {
                if($row[0] == $user)    {
                    $user_same++;
                }
            }
            if($user_same == 0) {
                $sql = "INSERT INTO $room_user (room_no, id, userid) VALUES ($room_no, NULL, \"$user\")";
                $this->db_con->query($sql);
                $sql = "INSERT INTO $room_name (room_no, userid, content) VALUES ($room_no, \"$user\", \"입장하셨습니다.\")";
                $this->db_con->query($sql);
            }
            $sql = "select userid, content from $room_name;";
            $result_content = $this->db_con->query($sql);
            while($row_content = $result_content->fetch_array())
            {
                echo $row_content[0] . ">" . $row_content[1] . "<br>";
            }
            echo "<script>setInterval(function() { location.reload();}, 500)</script>";
    }

    function content_send($content, $room_no)
    {
        $sql = "select room_name from chat_list where room_no = $room_no";
        $result = $this->db_con->query($sql);
        $row = $result->fetch_array();
        $room_name = $row[0];
        $user = $_SESSION['user'];
        $sql = "INSERT INTO $room_name (room_no, userid, content) VALUES ($room_no, \"$user\", \"$content\")";
        $this->db_con->query($sql);
        echo "<script>location.href = 'chatting_window.php?room_no=$room_no';</script>";
    }

    function room_delete($room_no)
    {
        $sql = "select room_name from chat_list where room_no = $room_no";
        $result = $this->db_con->query($sql);
        $row = $result->fetch_array();
        $room_name = $row[0];
        $user_table = $room_name . "_user";
        $user = $_SESSION['user'];
        $sql = "delete from $user_table where userid = \"$user\"";
        $this->db_con->query($sql);
        $sql = "select * from $user_table";
        $result = $this->db_con->query($sql);
        if ($result->num_rows == 0) {
            $sql = "delete from chat_list where room_no = $room_no";
            $this->db_con->query($sql);
            $sql = "DROP TABLE $user_table";
            $this->db_con->query($sql);
            $sql = "DROP TABLE $room_name";
            $this->db_con->query($sql);
        }
        echo "<script>location.href=\"home.php\";</script>";
    }
}


