<?php
require_once('../dbconnect.php');
session_start();

//入力の確認
if(!empty($_POST)){
    //エラー項目の確認
    if($_POST['name'] == ""){
        $error['name'] = 'blank';
    }
    if($_POST['email'] == ""){
        $error['email'] = 'blank';
    }
    if($_POST['password'] == ""){
        $error['password'] = 'blank';
    }
    if(strlen($_POST['password']) < 4){
        $error['password'] = 'length';
    }

    //画像のチェック
    $fileName = $_FILES['image']['name'];
    if(!empty($fileName)){
        $ext = substr($fileName,-3);
        if($ext != 'jpg' && $ext != 'gif'){
            $error['image'] = 'type';
        }
    }

    //重複アカウントのチェック
    if(empty($error)){
        //SQLで同一のメールアドレスのカウントチェック
        $sql = "SELECT COUNT(*) AS cnt FROM members WHERE email=?";
        $member = $db->prepare($sql);
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        
        if($record['cnt'] > 0){
            $error['email'] = 'duplicate';
        }
    }
    
    //エラーがない場合
    if(empty($error)){
        //画像をアップロードする
        $image = date('YmdHis').$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);

        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}

//書き直し
if($_GET['action'] == 'rewrite'){
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css" />
    <title>会員登録</title>
</head>
<body>
    <div id="wrap">
        <div id="head">
            <h1>会員登録</h1>
        </div><!-- /#head -->
        <div id="content">
            <p>次のフォームに必要事項をご記入ください。</p>
            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <dt>ニックネーム<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="name" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['name'],ENT_QUOTES); ?>">
                        <?php if($error['name'] == 'blank'): ?>
                        <p class="error">*ニックネームを入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>メールアドレス<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'],ENT_QUOTES); ?>">
                        <?php if($error['email'] == 'blank'): ?>
                        <p class="error">*メールアドレスを入力してください</p>
                        <?php elseif($error['email'] == 'duplicate'): ?>
                        <p class="error">*指定されたメールアドレスはすでに登録されています</p>
                        <?php endif; ?>
                    </dd>
                    <dt>パスワード<span class="required">必須</span></dt>
                    <dd>
                        <input type="password" name="password" size="10" maxlength="20" value="<?php echo htmlspecialchars($_POST['password'],ENT_QUOTES); ?>">
                        <?php if($error['password'] == 'blank'): ?>
                        <p class="error">*パスワードを入力してください</p>
                        <?php elseif($error['password'] == 'length'): ?>
                        <p class="error">*パスワードは4文字以上で入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <input type="file" name="image" size="35">
                        <?php if($error['image'] == 'type'): ?>
                        <p class="error">※「jpg」か「gif」の画像を指定してください</p>
                        <?php elseif(!empty($error)): ?>
                        <p class="error">※画像を改めて指定してください</p>
                        <?php endif; ?>
                    </dd>
                </dl>
                <div><input type="submit" value="入力内容を確認する"></div>
            </form>
        </div><!-- /#content -->
    </div><!-- /#wrap -->
</body>
</html>