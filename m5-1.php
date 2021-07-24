<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>m5-1</title>
    <link rel="stylesheet" href="m5-1.css">
</head>
<body>
    <?php
    // m4-1 DB接続設定
    $dsn = "データベース名"; // データベース名
    $user = "ユーザー名"; // ユーザー名
    $password = "パスワード"; // パスワード
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    // m4-2 テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS m5_1" // 新しくm5_1というテーブル作成する
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY," // 投稿番号
    . "name char(32)," // 名前
    . "comment TEXT," // コメント
    . "date TIMESTAMP," // 投稿日時
    . "password varchar(50)" // パスワード
    .");";
    $stmt = $pdo->query($sql);
    ?>
    
    <div class="postForm">
        <!-- 投稿フォーム -->
        <form action="" method="post" class="postForm__post">
            <?php
            // 編集用データを変数に代入(空)
            $editNumber = "";
            $editName = "";
            $editComment = "";
            // 編集ボタンが押された場合
            if(!empty($_POST["edit"])){
                // m4-6 入力したデータレコードを抽出
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($row["id"] == $_POST["editNumber"] && $row["password"] == $_POST["editPassword"]){
                        $editNumber = $row['id'];
                        $editName = $row['name'];;
                        $editComment = $row['comment'];
                    }
                }
            }
            ?>
            <p class="formTitle">投稿フォーム</p>
            <input type="hidden" name="editPost" placeholder="編集番号" value="<?php echo $editNumber; ?>">
            <input type="text" name="name" placeholder="名前" value="<?php echo $editName; ?>"><br>
            <textarea type="text" name="comment" placeholder="コメント" rows="3" cols="50"><?php echo $editComment; ?></textarea><br>
            <input type="password" name="postPassword" placeholder="パスワード" value="">
            <input type="submit" name="post" value="投稿"><br>
            <?php
            // 投稿ボタンを押した場合
            if(!empty($_POST["post"])){
                // 名前、コメント、パスワードが入力されている場合
                if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["postPassword"])){
                    // 編集番号が入力されている場合、編集投稿
                    if(!empty($_POST["editPost"])){
                        // m4-6 入力したデータレコードを抽出
                        $sql = 'SELECT * FROM m5_1';
                        $stmt = $pdo->query($sql);
                        $results = $stmt->fetchAll();
                        foreach ($results as $row){
                            // 入力したパスワードと保存されているパスワードが一致した場合
                            if($_POST["postPassword"] == $row["password"]){
                                $id = $_POST["editPost"]; // 変更する投稿番号
                                $name = $_POST["name"]; // 変更内容（名前）
                                $comment = $_POST["comment"]; // 変更内容（コメント）
                                $sql = 'UPDATE m5_1 SET name=:name,comment=:comment,date=CURRENT_TIMESTAMP WHERE id=:id';
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                                $stmt->execute();
                            }
                        }
                        echo "編集が完了しました";
                    }
                    // 編集番号が入力されていない場合、新規投稿
                    else{
                        // m4-5 データ入力
                        $sql = $pdo -> prepare("INSERT INTO m5_1 (name, comment, date, password) VALUES (:name, :comment, CURRENT_TIMESTAMP, :password)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                        $name = $_POST["name"]; // 名前のpost送信を変数に代入
                        $comment = $_POST["comment"]; // コメントのpost送信を変数に代入
                        $password = $_POST["postPassword"]; // パスワードのpost送信を変数に代入
                        $sql -> execute();
                        echo "<p>投稿が完了しました</p>";
                    }
                }
                // 
                else{
                    echo "<p>必要事項が入力されていないため、<br>投稿することができません</p>";
                }
            }
            // 編集ボタンが押された場合
            elseif(!empty($_POST["edit"])){
                // m4-6 入力したデータレコードを抽出
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($row["id"] == $_POST["editNumber"] && $row["password"] == $_POST["editPassword"]){
                        echo "<p>内容を編集し、設定されているパスワードを入力後、<br>投稿ボタンを押してください</p>";
                    }
                }
            }
            // ボタンが押されていない場合
            else{
                echo "<p>名前とコメントを入力後、<br>パスワードを設定して投稿してください</p>";
            }
            ?>
        </form><br>
            
        <!-- 削除フォーム -->
        <form action="" method="post" class="postForm__delete">
            <p class="formTitle">削除フォーム</p>
            <input type="text" name="deleteNumber" placeholder="削除番号" value=""><br>
            <input type="password" name="deletePassword" placeholder="パスワード" value="">
            <input type="submit" name="delete" value="削除"><br>
            <?php
            // 削除ボタンを押した場合
            if(!empty($_POST["delete"])){
                // m4-6 入力したデータレコードを抽出
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    // 入力したパスワードと保存されているパスワードが一致した場合
                    if($row["password"] == $_POST["deletePassword"]){
                        // m4-8 入力したデータレコードを削除
                        $id = $_POST["deleteNumber"];
                        $sql = 'delete from m5_1 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        echo "<p>削除が完了しました</p>";
                    }
                    else{
                        echo "<p>入力内容に誤りがあるため、削除できません</p>";
                    }
                    break;
                    
                }
            }
            // ボタンが押されていない場合
            else{
                echo "<p>削除したい投稿番号を入力後、<br>パスワードを入力し、削除ボタンを押してください。</p>";
            }
            ?>
        </form><br>
            
        <!-- 編集フォーム -->
        <form action="" method="post" class="postForm__edit">
            <p class="formTitle">編集フォーム</p>
            <input type="text" name="editNumber" placeholder="編集番号" value=""><br>
            <input type="password" name="editPassword" placeholder="パスワード" value="">
            <input type="submit" name="edit" value="編集">
            <?php
            // 編集ボタンが押された場合
            if(!empty($_POST["edit"])){
                // m4-6 入力したデータレコードを抽出
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($row["id"] == $_POST["editNumber"] && $row["password"] == $_POST["editPassword"]){
                        echo "<p>編集が可能です。<br>投稿フォームで編集を行なってください。</p>";
                    }
                    else{
                        echo "<p>入力内容に誤りがあるため、編集できません</p>";
                    }
                    break;
                }
            }
            //　ボタンが押されていない場合
            else{
                echo "<p>編集したい投稿番号を入力後、<br>パスワードを入力し、編集ボタンを押してください。</p>";
            }
            ?>
        </form><br>
    </div>
    <div class="postedContent">
        <h1>みんなの投稿</h1><hr>
        <?php
        // m4-6 入力したデータレコードを抽出し、表示する
        $sql = 'SELECT * FROM m5_1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id']."\n".$row['name']."\n".$row['date']."<br>".$row['comment']."<hr>";
        }
        ?>
    </div>
</body>
</html>
