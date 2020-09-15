<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
</head>

<body>
    
    <?php

        //4-1/DB接続設定
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


        //フォームに入力された内容を変数に代入、日付
        $name = @$_POST["name"];
        $comment = @$_POST["comment"];
        $delete = @$_POST["delete"];
        $edit = @$_POST["edit"];
        $date = date("Y/m/d H:i:s");
        $password = @$_POST["password"];


        //4-2/CREATE文/テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS tb_6"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATE,"
        . "password char(32)"
        .");";
        $stmt = $pdo->query($sql);

        
        //投稿機能
        if(!empty($_POST["name"]) && ($_POST["comment"]) && ($_POST["password"])){
            
                if(empty($_POST["editNumber"])){//編集番号が入力されていなければ新規投稿

                    //4-5/INSERT文/データ入力（データレーコードの挿入）
                    $sql = $pdo -> prepare("INSERT INTO tb_6 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $sql -> execute();

                }else{//入力されていれば書き換える（編集機能）
                    $editNumber = $_POST["editNumber"];
                    //4-7/UPDATE文/入力されているデータレコードの内容を編集
                    //bindParamの引数（:nameなど）は4-2でどんな名前のカラムを設定したかで変える必要がある。
                    if($row['id'] == $editNumber){
                        $sql = 'UPDATE tb_6 SET name=:name,comment=:comment';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->execute();
                        //SELECT文
                        $sql = 'SELECT * FROM tb_6 WHERE id=:id';
                    }

                }
        //何かが入力されていないときはエラーメッセージを出す！（未処理）
        }
        
        
        //削除機能
        if(!empty($_POST["delete"])){
            
            //ファイルを読み込み/データレコードを抽出
            $sql = 'SELECT * FROM tb_6';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            
            foreach($results as $row){
                if($_POST["password2"] == $row['password']){//削除フォームに入力されたパスワードと投稿時のパスワードが一致すれば
                        $id = $delete;
                        $sql = 'delete from tb_6 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                }else{
                    echo "*正しいパスワードを入力してください";
                }
            }
        }
        
        //編集選択機能
        if(!empty($_POST["edit"])){
            
            //編集のパウワードフォームに入力されていれば
            if(!empty($_POST["password3"])){

                //編集フォームに入力されたパスワードと投稿時のパスワードが一致すれば
                if($_POST["password3"] == $row['password']){
                    $hensyu = $_POST["edit"];
                    $sql = 'SELECT * FROM tb_6';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    foreach($results as $row){
                        if($hensyu == $row['id']){
                            $editnamae = $row['name'];
                            $editname = $row['comment'];
                        }
                    }
                }else{
                    echo "*正しいパスワードを入力してください";
                }

            }else{
                echo "*パスワードを入力してください";
            }
            
        }
    ?>
    
    
    <!--送信フォーム各種-->
    <form action="" method="post">
        
        <!--名前フォーム-->
        名前　　　<input type="text" name="name" value="<?php if(isset($editnamae)){echo $editnamae;}?>">
        <br>
        
        <!--コメントフォーム-->
        コメント　<input type="text" name="comment" value="<?php if(isset($editname)){echo $editname;}?>">
        <br>
        
        <!--パスワードフォーム/名前・コメント-->
        パスワード<input type="password" name="password" placeholder="パスワード">
        
        <!--編集選択後に出現するフォーム-->
        <?php if(!empty($hensyu)): ?>
                編集番号　<input type="text" name="editNumber" value="<?php echo $hensyu; ?>">
        <?php endif; ?>
        　<input type="submit" name="submit">　<br><br>
        
        <!--削除フォーム-->
        削除　　　<input type="text" name="delete" placeholder="削除対象番号">
        <br>
        <!--パスワードフォーム/削除-->
        パスワード<input type="password" name="password2" placeholder="パスワード">
        
        　<input type="submit" name="submit" value="削除"><!--削除ボタン-->
        <br><br>
        
        <!--編集フォーム-->
        編集　　　<input type="text" name="edit" placeholder="編集対象番号">
        <br>
        <!--パスワードフォーム/編集-->
        パスワード<input type="password" name="password3" placeholder="パスワード">
        
        　<input type="submit" name="submit" value="編集"><!--編集ボタン-->
   
    </form>
    <br><br>
    
    
    <!--投稿内容を表示する処理-->
    <?php
    //4-6/SELECT文/入力したデータレコードを抽出し表示する/（保留）
    //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要があります。
    $sql = 'SELECT * FROM tb_6';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'<>';
        echo $row['name'].'<>';
        echo $row['comment'].'<>';
        echo $row['date'].'<br>';
        echo "<hr>";
    }

    ?>
    
    
</body>
</html>