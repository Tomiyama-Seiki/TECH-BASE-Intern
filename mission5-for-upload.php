<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>mission5-1</title>
    </head>
    <body>
        <!-- 投稿フォーム -->
        今日の夜ご飯は？<br>
        <br>
        -- 投稿 --
        <form method = "POST">
            名前：　　　<input type = "text", name = "name"><br>
            コメント：　<input type = "text", name = "comment"><br>
            パスワード：<input type = "password", name = "password"> 
            <input type = "submit" name = "submit">
        </form>
        <br>
        <!-- 削除フォーム -->
        -- 削除 -- 
        <form method = "POST">
            投稿番号：　<input type = "number", name = "deletenum", placeholder = "番号を選択"><br>
            パスワード：<input type = "password", name = "deletepass">
            <input type = "submit" name = "delete", value = "削除">
        </form>
        <br>
        <!-- 編集フォーム -->
        -- 編集 --
        <form method = "POST">
            投稿番号：　<input type = "number", name = "editnum", placeholder = "番号を選択"><br>
            名前：　　　<input type = "text", name = "editname"><br>
            コメント：　<input type = "text", name = "editcom"><br>
            パスワード：<input type = "password", name = "editpass">
            <input type = "submit", name = "edit", value = 編集>
        </form> 
        <br>
        
        <?php
        // 新規投稿がある時　→　データベースへ接続　→　データベースへデータ入力　→　表示
        // 編集希望がある時　→　データベースへ接続　→　データベースから編集したいデータを呼び出す　
        // →　編集する　→　データベースへ新たに入力される(更新がされる)　→　表示
        // 削除希望がある時　→　データベースへ接続　→　データベースから削除したいデータを呼び出す
        // →　削除する　→　データベースへ新たに入力される(更新がされる)　→　表示


        // データベースへの接続
        $dsn='データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        // データベースの作成
        $sql = "CREATE TABLE IF NOT EXISTS tb_mission5"
        ." ("
        ."id INT AUTO_INCREMENT PRIMARY KEY," // id 
        ."name char(32)," // 名前
        ."comment TEXT," // コメント
        ."password VARCHAR(100)," //パスワード
        ."created_at DATETIME" //投稿の日時
        .")";
        $stmt = $pdo -> query($sql);
        

        // 新規投稿の処理
        if(isset($_POST["submit"])){ //"submit"は入力フォームの名前
            if(isset($_POST["name"],$_POST["comment"],$_POST["password"])){
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $pass = $_POST["password"];
            $date = date("y/m/d H:i:s");
            // データベースへ接続
            $dsn ='データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            // データを入力(INSERT文)
            $sql = $pdo -> prepare("INSERT INTO tb_mission5 (name, comment, password, created_at) VALUES (:name, :comment, :password, :created_at)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
            $sql -> bindParam(':created_at', $date, PDO::PARAM_STR);
            $sql -> execute();
            }

        
        
        // 削除の処理
        } elseif(isset($_POST["delete"])){
            if($_POST["deletenum"]>=1){
            // データベースへ接続
            $dsn ='データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            // データを削除(DELETE文)
            // 入力されたパスワードとデータベースに保存されているパスワードが一致する場合に削除する
            $sql = 'SELECT * FROM tb_mission5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchALL();
                foreach ($results as $row){
                    if($_POST["deletepass"] == $row["password"]){
                        $id = $_POST["deletenum"];
                        $sql = 'delete from tb_mission5 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }

        // 編集の処理
        } elseif(isset($_POST["edit"])){
            if($_POST["editnum"]>=1){
            // データベースへ接続
            $dsn ='データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            // データを編集(UPDATE文)
            // 入力されたパスワードとデータベースに保存されているパスワードが一致する場合に編集する
            $sql = 'SELECT * FROM tb_mission5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchALL();
                foreach ($results as $row){
                    if($_POST["editpass"] == $row["password"]){
                        $id = $_POST["editnum"]; // 変更したい投稿番号
                        $editname = $_POST["editname"]; // 変更したい名前
                        $editcom = $_POST["editcom"]; //変更したいコメント
                        $date = date("y/m/d H:i:s");
                        $sql = 'UPDATE tb_mission5 SET name=:name,comment=:comment, created_at=:created_at WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $editname, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $editcom, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt -> bindParam(':created_at', $date, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }
            }
        }
        ?>
        -- 表示 --
        <br>
        <?php
        // データベースへ接続
        $dsn ='データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        // 入力したデータレコードを抽出し、表示する(SELECT文) 
        $sql = 'SELECT * FROM tb_mission5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchALL();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].', ';
            echo $row['name'].', ';
            echo $row['comment'].', ';
            echo $row['created_at'];
            '<br>';
        echo "<hr>";
        }
        
        ?>
    </body>
</html>