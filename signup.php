<?php

// セッション開始
session_start();

    //データベース
    $dsn = 'mysql:dbname=*******;host=localhost;charset=utf8';
    $d_user = '*******';
    $password = '*******';
    $pdo = new PDO($dsn, $d_user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS teslog"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "mail char(32),"
    . "password char(100),"
    . "flag TINYINT(1) NOT NULL DEFAULT '0',"
    . "point INT(3) NOT NULL DEFAULT '0',"
    . "chickn INT(5) NOT NULL DEFAULT '0'"
    .");";


    $stmt = $pdo->query($sql);

    // エラーメッセージ、登録完了メッセージの初期化
    $errorMessage = "";
    $signUpMessage = "";


    // 新規登録ボタンが押された場合
    if (isset($_POST["signup"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["mail"])) {  // 値が空のとき
        $errorMessage = 'メールアドレスが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    } 

    if (!empty($_POST["mail"]) && !empty($_POST["password"])) {
        // 入力したユーザIDとパスワードを格納
        $mail = $_POST["mail"];
        $password = $_POST["password"];

    $flag = 0;

    //すでに登録してあるか判定
    $sql = 'SELECT * FROM teslog';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if($row['mail'] == $mail)
        $flag = $row['flag'];
    }
    if($flag == 1) {
        $errorMessage = 'すでに登録されているメールアドレスです';
    }else{

        // 3. エラー処理
        try {
        
            $stmt = $pdo->prepare("INSERT INTO teslog(mail, password) VALUES (?, ?)");

            $stmt->execute(array($mail, password_hash($password, PASSWORD_DEFAULT)));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

            $signUpMessage = '登録が完了しました。';  // ログイン時に使用するIDとパスワード
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            // echo $e->getMessage();
        }


    //mail送信
    $to_mail = $mail;

    require 'src/Exception.php';
    require 'src/PHPMailer.php';
    require 'src/SMTP.php';
    require 'setting.php';

    // PHPMailerのインスタンス生成
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
    $mail->SMTPAuth = true;
    $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
    $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
    $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
    $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
    $mail->Port = SMTP_PORT; // 接続するTCPポート

    $urltoken = hash('sha256',uniqid(rand(),1));
    $url = "**********************************"."?urltoken=".$urltoken;

    // メール内容設定
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "base64";
    $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
    $mail->addAddress('****************@gmail.com', '受信者さん'); //受信者（送信先）を追加する
//    $mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
//    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
//    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
    $mail->Subject = MAIL_SUBJECT; // メールタイトル
    $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
    $body = '<'.$to_mail.' 様>'.'以下のURLより会員登録してください。<br>'. $url;

    $mail->Body  = $body; // メール本文

    // メール送信の実行
    if(!$mail->send()) {
        $errorMessage = 'メッセージの送信に失敗しました';
        $errorMessage = 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $signUpMessage = 'メールを送信しました';
    }

    
    }
    }
    }


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>SignUp</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>

    <header>
            <p>ToDo Chicken</p>
    <nav>
        <ul>
            <li><a href="./signup.php">SignUp</li></a>
            <li><a href="./login.php">LogIn</li></a>
        </ul>
    </nav>
    </header>

    <body>
        <div class="box">
        <h1>新規登録</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
     
             <div><p style="color:#ff0000;">
                <?php echo $errorMessage; ?></p></div>
            <div><p style="color:#ff0000;">
                <?php echo $signUpMessage; ?></p></div>

                <label for="mail">
                <input type="email" style="ime-mode: disabled;" class="log_box" name="mail" placeholder="あなたのメールアドレス">
                </label>

                <br>

                <label for="password">
                <input type="password" style="ime-mode: disabled;" class="log_box" name="password" value="" placeholder="パスワード">
                </label>

                <br>

                <input type="submit" id="login" name="signup" value="新規登録">
        </form>
    </div>
    </body>
</html>