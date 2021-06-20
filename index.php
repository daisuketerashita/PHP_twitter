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
            $_POST['message'],
        );

        $message = $db->prepare($sql);
        $message->execute($array);

        header('Location: index.php');
        exit();
    }
}

//投稿を取得する
$sql = "SELECT m.name,m.picture,p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC";
$posts = $db->query($sql);

//返信の場合
if(isset($_GET['res'])){
    //SQL
    $sql = 'SELECT m.name,m.picture,p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC';

    $response = $db->prepare($sql);
    $response->execute(array($_GET['res']));

    $table = $response->fetch();
    $message = '@'.$table['name'].' '.$table['message'];
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
			<dt><?php echo htmlspecialchars($member['name']); ?>さん、メッセージをどうぞ</dt>
		<dd>
		<textarea name="message" cols="50" rows="5"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></textarea>
		<input type="hidden" name="reply_post_id" value="<?php echo htmlspecialchars($_GET['res'], ENT_QUOTES); ?>" />
		</dd>
		</dl>
		<div>
		<input type="submit" value="投稿する" />
		</div>
		</form>

		<?php
		foreach ($posts as $post):
		?>
		<div class="msg">
			<img src="member_picture/<?php echo htmlspecialchars($post['picture'], ENT_QUOTES, 'UTF-8'); ?>" width="48" height="48" alt="<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>" />
			<p><?php echo htmlspecialchars($post['message'], ENT_QUOTES);?><span class="name">（<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>）</span>
			[<a href="index.php?res=<?php echo htmlspecialchars($post['id'], ENT_QUOTES); ?>">Re</a>]</p>
			<p class="day"><a href="view.php?id=<?php echo htmlspecialchars($post['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($post['created'], ENT_QUOTES); ?></a>
			</p>
		</div>
		<?php
		endforeach;
		?>
  </div>

</div>
</body>

</html>