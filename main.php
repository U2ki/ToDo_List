<!DOCTYPE html>
<html lang="ja">
<head>
<title>MainPage</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="style.css">
</head>

    <header>
            <p>ToDo Chicken</p>
    <nav>
        <ul>
            <li><a href="./logout.php">Log out</li></a>
        </ul>
    </nav>
    </header>

<body>

<?php

	session_start();

	if(!isset($_SESSION["MAIL"])){
        $no_login_url = "login.php";
        header("Location: {$no_login_url}");
        exit;
    }

	//データベース
	$dsn = 'mysql:dbname=*******;host=localhost';
	$d_user = '*******';
	$password = '*******';
	$pdo = new PDO($dsn, $d_user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

	//テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS testtodo"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "mail char(32),"
	. "todo char(32),"
	. "comment TEXT,"
	. "year INT(4) NOT NULL DEFAULT '2019',"
	. "month INT(2) NOT NULL DEFAULT '1',"
	. "day INT(2) NOT NULL DEFAULT '1',"
	. "oclock INT(2) NOT NULL DEFAULT '0'"
	.");";
	$stmt = $pdo->query($sql);

	$errorMessage = "";

	$point = 0;

	//データベースへの登録
	//フォーム内が空でない場合に以下を実行する
	if (isset($_POST["todo"],$_POST["comment"])) {

	$todo = ($_POST["todo"]);
	$comment = ($_POST["comment"]);

	if(empty($todo)){
		$errorMessage = "ToDoが入力されていません。";
	}else if(!empty($todo)){

	$year = $_POST["year"];
	$month = $_POST["month"];
	$day = $_POST["day"];
	$oclock = $_POST["oclock"];
	$mail = $_SESSION["MAIL"];

  	//テーブルにデータを入力
	$sql = $pdo -> prepare("INSERT INTO testtodo (year,month,day,oclock,todo, comment,mail) VALUES (:year, :month, :day, :oclock, :todo, :comment, :mail)");

	$sql -> bindParam(':year', $year, PDO::PARAM_INT);
	$sql -> bindParam(':month', $month, PDO::PARAM_INT);
	$sql -> bindParam(':day', $day, PDO::PARAM_INT);
	$sql -> bindParam(':oclock', $oclock, PDO::PARAM_INT);
	$sql -> bindParam(':todo', $todo, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':mail', $mail, PDO::PARAM_STR);

	$sql -> execute();
    
}
}

//削除
if (isset($_POST["del_id"])){

		//入力データの受け取りを変数に代入
		$del_id = ($_POST["del_id"]);//削除したい番号
		$ans = ($_POST["answer"]);

	//passwdがないときはecho、あるときは新規投稿・編集投稿
	if(empty($del_id)){
		$errorMessage = "削除実行に失敗しました。";
	}else if(!empty($del_id)){

	//削除実行
	$id = $del_id;
	$sql = 'delete from testtodo where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$errorMessage = "削除しました。";
	$stmt->execute();
	}
	}

//断念
if (isset($_POST["miss_id"])){

		//入力データの受け取りを変数に代入
		$miss_id = ($_POST["miss_id"]);//削除したい番号
		$ans = ($_POST["answer"]);
		$mail = $_SESSION["MAIL"];

	//passwdがないときはecho、あるときは新規投稿・編集投稿
	if(empty($miss_id)){
		$errorMessage = "断念実行に失敗しました。";
	}else if(!empty($miss_id)){

		if($ans == 1){

		$sql = 'SELECT * FROM teslog ';
		$stmt = $pdo -> query($sql);
		$result = $stmt -> fetchAll(); 
		
		foreach ($result as $row) {
			if($row['mail'] == $mail){
				$point = $row['point'];
			}
		}

			$point = $point - 1;

	//アップデート
	
	$sql = 'update teslog set point=:point where mail=:mail';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':point', $point, PDO::PARAM_INT);
	$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
	$stmt->execute();
	}

	//削除実行
	$id = $miss_id;
	$sql = 'delete from testtodo where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$errorMessage = "次は頑張りましょう！";
	$stmt->execute();
	}
	}


//完了
if (isset($_POST["com_id"])){

		//入力データの受け取りを変数に代入
		$com_id = ($_POST["com_id"]);//削除したい番号
		$ans = ($_POST["answer"]);
		$mail = $_SESSION["MAIL"];

	//passwdがないときはecho、あるときは新規投稿・編集投稿
	if(empty($com_id)){
		$errorMessage = "完了実行に失敗しました。";
	}else if(!empty($com_id)){

		if($ans == 2){

		
		$sql = 'SELECT * FROM teslog ';
		$stmt = $pdo -> query($sql);
		$result = $stmt -> fetchAll(); 
		
		foreach ($result as $row) {
			if($row['mail'] == $mail){
				$point = $row['point'];
			}
		}
		
		$point = $point + 1;
			
	$sql = 'update teslog set point=:point where mail=:mail';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':point', $point, PDO::PARAM_INT);
	$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
	$stmt->execute();
	}

	//削除実行
	$id = $com_id;
	$sql = 'delete from testtodo where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$errorMessage = "お疲れ様です！";
	$stmt->execute();
	}
	}

	?>

<h1></h1>

<div class="boxA">
<div class="box1">
<form method="POST" action="">

	 <div class="msr_pulldown_02">

	<div class="flex">
	<div>
	<select name="year">
	<option value="2019" selected>2019年</option>
	<option value="2020">2020年</option>
	<option value="2021">2021年</option>
	<option value="2022">2022年</option>
	<option value="2023">2023年</option>
	</select>
	</div>
	<div>
	<select name="month">
	<option value="1" selected>1月</option>
	<option value="2">2月</option>
	<option value="3">3月</option>
	<option value="4">4月</option>
	<option value="5">5月</option>
	<option value="6">6月</option>
	<option value="7">7月</option>
	<option value="8">8月</option>
	<option value="9">9月</option>
	<option value="10">10月</option>
	<option value="11">11月</option>
	<option value="12">12月</option>
	</select>
	</div>
	<div>
	<select name="day">
	<option value="1" selected>1日</option>
	<option value="2">2日</option>
	<option value="3">3日</option>
	<option value="4">4日</option>
	<option value="5">5日</option>
	<option value="6">6日</option>
	<option value="7">7日</option>
	<option value="8">8日</option>
	<option value="9">9日</option>
	<option value="10">10日</option>
	<option value="11">11日</option>
	<option value="12">12日</option>
	<option value="13">13日</option>
	<option value="14">14日</option>
	<option value="15">15日</option>
	<option value="16">16日</option>
	<option value="17">17日</option>
	<option value="18">18日</option>
	<option value="19">19日</option>
	<option value="20">20日</option>
	<option value="21">21日</option>
	<option value="22">22日</option>
	<option value="23">23日</option>
	<option value="24">24日</option>
	<option value="25">25日</option>
	<option value="26">26日</option>
	<option value="27">27日</option>
	<option value="28">28日</option>
	<option value="29">29日</option>
	<option value="30">30日</option>
	<option value="31">31日</option>
	</select>
	</div>
	<div>
	<select name="oclock">
	<option value="00" selected>00:00 まで</option>
	<option value="06">06:00 まで</option>
	<option value="09">09:00 まで</option>
	<option value="12">12:00 まで</option>
	<option value="15">15:00 まで</option>
	<option value="18">18:00 まで</option>
	<option value="21">21:00 まで</option>
	</select>
	</div>
	</div>

	</div>

	<br>
	<br>
	 <div>
	 	<p style="float: left; color:#ff0000;">
        <?php echo $errorMessage; ?></p></div>
     <div class="msr_text_02">
	<input type="text" name="todo" maxlength='30' placeholder="ToDo(30文字以内)">
	</div>
	<br>
	<br>
	<div class="msr_textarea_02">
	<textarea name="comment" placeholder="内容"></textarea>
	</div>
	<!--<input type="text" name="comment" placeholder="内容">-->

	<br>
	<br>
	<td>
	<p class="msr_sendbtn_02">
      <input type="submit" value="送信">
    </p>
    <p class="msr_sendbtn_02">
      <input type="reset" value="リセット">
    </p>
    </td>
</form>
</div>
<div class="box2">
	<p>完了ポイント：<?php 
		
		$sql = 'SELECT * FROM teslog ';
		$stmt = $pdo -> query($sql);
		$result = $stmt -> fetchAll(); 
		
		foreach ($result as $row) {
			if($row['mail'] == $_SESSION["MAIL"]){
				$point = $row['point'];
				$chickn = $row['chickn'];
			}
		}
		echo $point; ?>p</p>
		
	<?php 
	//画像の判定
	if($point < 10) $pic_num = 1;
	else if($point < 25) $pic_num = 2; 
	else if($point < 40) $pic_num = 3;
	else if($point < 60) $pic_num = 4;
	else if($point < 80) $pic_num = 5;
	else if($point < 100) $pic_num = 6;
	else if($point == 100) { 
		$pic_num = 7;
		//pointを0に更新
		$mail = $_SESSION["MAIL"];
		$point = 0;
		$chickn ++;
		$sql = 'update teslog set point=:point, chickn=:chickn where mail=:mail';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':point', $point, PDO::PARAM_INT);
		$stmt->bindParam(':chickn', $chickn, PDO::PARAM_INT);
		$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
		$stmt->execute();
		}
	?>
	<img src="img/<?php echo $pic_num; ?>.png" width="90" height="100">
	<p>チキン獲得数：<?php echo $chickn; ?>個</p>
</div>
</div>

<!--todoの表示-->
<!--PHP-->
<br><br><br><br>
<hr  class="style2">
<br><br>

<?php
//表示の順番を変える
$sql = 'SELECT id, mail, todo, comment, year, month, day, oclock FROM testtodo ORDER BY year,month,day,oclock';
$stmt = $pdo->prepare($sql);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	if($row['mail'] == $_SESSION["MAIL"]){
	?>
		<div class="boxA">
			<div class="box_1">
				<p>
				<?php
				echo $row['year']."\t/\t";
				echo $row['month']."\t/\t";
				echo $row['day']."\t/\t";
				echo $row['oclock'].":00 〆<br>";
				?>
				</p>
				<p class="moji">
				<?php 
				//$rowの中にはテーブルのカラム名が入る
				echo $row['todo']."\t";?>
				</p>

	</div>
		<form name="" method="POST" action="">
			<div class="box_2">
				<input type="hidden" name="answer" value="0">
				<input type="hidden" name="del_id" value=" <?=$row['id']?> ">
				<input type="submit" value="削除" class="button_del">
			</div>
		</form>
		<form name="" method="POST" action="">
			<div class="box_3">
				<input type="hidden" name="answer" value="1">
				<input type="hidden" name="miss_id" value=" <?=$row['id']?> ">
				<input type="submit" value="断念" class="button_miss">
			</div>
		</form>
		<form name="" method="POST" action="">
			<div class="box_4">
				<input type="hidden" name="answer" value="2">
				<input type="hidden" name="com_id" value=" <?=$row['id']?> ">
				<input type="submit" value="完了" class="button_com">
			</div>
		</form>
	</div>
	<p>
	<?php	
	echo $row['comment']."<br>";?></p><hr class="style9">
	<?php
	}
}
?>


</body>
</html>