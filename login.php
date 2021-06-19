<?php
require_once('./dbconnect.php');

session_start();

//Cookieにログイン情報が保存されていた場合は、各情報を代入する
if($_COOKIE['email'] != ''){
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];
    $_POST['save'] = 'on';
}

if(!empty($_POST)){
    //ログイン処理
    if($_POST['email'] !== '' && $_POST['password'] !== ''){
        //SQL
        $sql = "SELECT * FROM members WHERE email=? AND password=?";

        $login = $db->prepare($sql);
        $array = array(
            $_POST['email'],
            sha1($_POST['password']),
        );
        $login->execute($array);
        $member = $login->fetch();

        if($member){
            //ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            //ログイン情報をCookieに保存する
            if ($_POST['save'] == 'on') {
				setcookie('email', $_POST['email'], time()+60*60*24*14);
				setcookie('password', $_POST['password'], time()+60*60*24*14);
			}

            header('Location: index.php');
            exit();
        }else{
            //ログイン失敗
            $error['login'] = 'failed';
        }
    }else{
        $error['login'] = 'blank';
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
    <title>ログイン</title>
</head>
	<div id="wrap">
		<div id="head">
			<h1>ログインする</h1>
		</div>
		<div id="content">
			<div id="lead">
				<p>メールアドレスとパスワードを記入してログインしてください。</p>
				<p>入会手続きがまだの方はこちらからどうぞ。</p>
				<p>&raquo;<a href="join/">入会手続きをする</a></p>
			</div>
            <form action="" method="post">
                <dl>
                    <dt>メールアドレス</dt>
                    <dd>
                        <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'],ENT_QUOTES); ?>">
                        <?php if($error['login'] == 'blank'): ?>
                        <p class="error">※メールアドレスとパスワードをご記入ください</p>
                        <?php elseif($error['login'] == 'failed'): ?>
                        <p class="error">※ログインに失敗しました。正しくご記入下さい</p>
                        <?php endif; ?>
                    </dd>
                    <dt>パスワード</dt>
                    <dd>
                        <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password'],ENT_QUOTES); ?>">
                    </dd>
                    <dt>ログイン情報の記録</dt>
                    <dd>
                        <input type="checkbox" id="save" name="save" value="on"><label for="save">次回からは自動的にログインする</label>
                    </dd>
                </dl>
                <div><input type="submit" value="ログインする"></div>
            </form>
        </div>
    </div>
</body>
</html>