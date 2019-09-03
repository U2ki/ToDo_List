<?php

// セッション管理開始
session_start();//コンピュータのサーバー側に一時的にデータを保存する   

    //データベース
    $dsn = 'mysql:dbname=*******;host=localhost;charset=utf8';
    $d_user = '*******';
    $password = '**********';
    $pdo = new PDO($dsn, $d_user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


// エラーメッセージの初期化
$errorMessage = '';

// ログインボタンが押された場合
if (isset($_POST["login"])) {
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

        $count = 0;

    //flag
    $sql = 'SELECT * FROM teslog';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if($row['mail'] == $mail)
        $flag = $row['flag'];
        if($flag == 1) $count = 1;
    }
    if($count != 1) {
        $errorMessage = '登録されていません';
    }else{

    //メアドの重複とパスワードの桁数チェック
    function cheak($id,$count){
        if($count > 0){
            throw new Exception('メールアドレスは既に使用されています。');
        }
        if ($id < 8) {
            throw new Exception('パスワードは8桁以上で入力してください。'); 
        }
    }



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
                    }
                    $_SESSION["MAIL"] = $row['mail'];
                    header("Location: main.php");  // メイン画面へ遷移
                    exit();  // 処理終了
             } else {
                    // 認証失敗
                    $errorMessage = 'メールアドレスあるいはパスワードに誤りがあります。';
                    }
            } else {
                // 4. 認証成功なら、セッションIDを新規に発行する
                // 該当データなし
                $errorMessage = 'メールアドレスあるいはパスワードに誤りがあります。';
                    }        
    } catch(Exception $e){
    $errorMessage = $e->getMessage();
    }
    }
    }
    }
?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Login</title>
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
        <h1>ログイン</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
             
              <div><p style="color:#ff0000;">
                <?php echo $errorMessage; ?></p></div>

                <label for="mail">
                <input type="email" style="ime-mode: disabled;" class="log_box" id="mail" name="mail" placeholder="メールアドレス" value="<?php if (!empty($_POST["mail"])) {echo htmlspecialchars($_POST["mail"], ENT_QUOTES);} ?>">
                </label>

                <br>

                <label for="password">
                <input type="password" style="ime-mode: disabled;" class="log_box" name="password" value="" placeholder="パスワード">
                </label>


                <br>
                <input type="submit" id="login" name="login" value="ログイン">
        </form>
        <br>
        <br>
        <br>
        <hr>
        <br>
        <p>まだ登録してない方はこちらから</p>
        <form action="signup.php">
                <input id="sign_up" type="submit" value="新規登録">
        </form>
    </div>
    </body>
</html>
