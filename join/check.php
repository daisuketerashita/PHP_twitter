<?php
session_start();
require_once('../dbconnect.php');

if(!isset($_SESSION['join'])){
	header('Location: index.php');
	exit();
}

//登録処理
if(!empty($_POST)){
	//SQL
	$sql = "INSERT INTO members SET name=?,email=?,password=?,picture=?,created=NOW()";

	$statement = $db->prepare($sql);
	$join_data = [
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['image']
	];
	echo $ret = $statement->execute($join_data);

	//入力情報を削除
	unset($_SESSION['join']);

	header('Location: thanks.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../style.css" />
	<title>会員情報確認</title>
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>会員登録</h1>
  </div>
  <div id="content">
		<form action="" method="post">
		<input type="hidden" name="action" value="submit">
		<dl>
		<dt>ニックネーム</dt>
		<dd>
		<?php echo htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES); ?>
		</dd>
		<dt>メールアドレス</dt>
		<dd>
		<?php echo htmlspecialchars($_SESSION['join']['email'],ENT_QUOTES); ?>
		</dd>
		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>
		<dt>写真など</dt>
		<dd>
		<img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'],ENT_QUOTES); ?>" alt="" width="100" height="100">
		</dd>
		</dl>
		<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input
		type="submit" value="登録する" /></div>
		</form>
  </div>

</div>
</body>
</html>
