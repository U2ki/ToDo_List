<?php

// セッション管理開始
session_start();//コンピュータのサーバー側に一時的にデータを保存する

    //データベース
    $dsn = 'mysql:dbname=**********;host=localhost;charset=utf8';
    $d_user = '**********';
    $password = '**********';
    $pdo = new PDO($dsn, $d_user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["confirmation"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["mail"])) {  // emptyは値が空のとき
        $errorMessage = 'メールアドレスが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    //両方あったとき
    if (!empty($_POST["mail"]) && !empty($_POST["password"])) {
        // 入力したメールアドレス・パスワードを格納
        $mail = $_POST["mail"];
        $password = $_POST["password"];


    // 3. エラー処理
    try {

        $stmt = $pdo->prepare('SELECT * FROM teslog WHERE mail = ?');
        $stmt->execute(array($mail));

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                    session_regenerate_id(true);

                    // 入力したIDのユーザー名を取得
                    $id = $row['id'];
                    $sql = "SELECT * FROM teslog WHERE id = $id";  //入力したIDからユーザー名を取得
                    $stmt = $pdo->query($sql);
                    foreach ($stmt as $row) {
                        $row['mail'];  // ユーザー名
                        $row['password'];
                    }

                    $_SESSION["MAIL"] = $row['mail'];
                          
            $flag = 1;

            $sql = 'update teslog set flag=:flag where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':flag', $flag, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

                    header("Location: main.php");  // メイン画面へ遷移
                    exit();  // 処理終了
             } else {
                    // 認証失敗
                    $errorMessage = 'メールアドレスあるいはパスワードに誤りがあります。';
                    }
            }       
    } catch(Exception $e){
    $errorMessage = $e->getMessage();
    }
    }
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Confirmation</title>
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
        <h1>ログイン(確認フォーム)</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
             
              <div><p style="color:#ff0000;">
                <?php echo $errorMessage; ?></p></div>

                <label for="mail">
                <input type="email" style="ime-mode: disabled;" class="log_box" id="mail" name="mail" placeholder="メールアドレス" >
                </label>

                <br>

                <label for="password">
                <input type="password" style="ime-mode: disabled;" class="log_box" name="password" value="" placeholder="パスワード">
                </label>

                <br>
                <p>※こちらのボタンを押すと登録が完全に終わり、利用できます</p>
                <input type="submit" id="login" name="confirmation" value="ログイン(確認)">
        </form>
    </div>
    </body>
</html>