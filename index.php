<?php
//ログイン状態をチェックする
session_start();
require_once('./dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    //ログインしている
    $_SESSION['time'] = time();

    //SQL
    $sql = "SELECT * FROM members WHERE id=?";

    $members = $db->prepare($sql);
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
}else{
    //ログインしていない
    header('Location: login.php');
    exit();
}


//投稿されたメッセージを記録する
if(!empty($_POST)){
    if($_POST['message'] !== ''){
        //SQL
        $sql = "INSERT INTO posts SET member_id=?,message=?,created=NOW()";
        //配列
        $array = array(
            $member['id'],
            $_POST['message']
        );

        $message = $db->prepare($sql);
        $message->execute($array);

        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Twitter風掲示板</title>
</head>
<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
    <form action="" method="post">
        <dl>
            <dt><?php echo htmlspecialchars($member['name'],ENT_QUOTES); ?>さん、メッセージをどうぞ</dt>
            <dd>
                <textarea name="message" id="" cols="50" rows="10"></textarea>
            </dd>
        </dl>
        <div>
            <input type="submit" value="投稿する">
        </div>
    </form>
  </div>
</div>
</body>
</html>