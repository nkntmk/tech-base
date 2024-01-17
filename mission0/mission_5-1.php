<?php
#DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

#テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tb5_1"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date TIMESTAMP NOT NULL,"
    . "pass CHAR(32)"
    .");";
    $stmt = $pdo->query($sql);

if($_SERVER["REQUEST_METHOD"]==="POST"){
#変数設定
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $pass_a=$_POST["pass_a"];
    $pass_b=$_POST["pass_b"];
    $pass_c=$_POST["pass_c"];
    $delete_num=$_POST["delete_num"];
    $edit_num=$_POST["edit_num"];
    $edit_NO=$_POST["edit_NO"];
    $date=date("Y/m/d H:i:s");
    
#編集番号設定
    if(!empty($edit_num) && !empty($pass_c)){
        $id=$edit_num;
        $sql='SELECT * FROM tb5_1 WHERE id=:id';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        
        foreach ($results as $row){
            if($row['pass']!=$pass_c){
                echo "パスワードが違います。";
                
            }elseif($row['pass']==$pass_c){
                $edit_number=$row['id'];
                $edit_name=$row['name'];
                $edit_comment=$row['comment'];
            }
        }
    }
    
    #編集 
    if(!empty($edit_NO)){
        $new_name=$_POST["name"];
        $new_comment=$_POST["comment"];
        $new_pass=$_POST["pass_a"];
        
        if(!empty($new_name) && !empty($new_comment) && !empty($new_pass)){
            $id=$edit_NO;
            
            $sql='SELECT * FROM tb5_1 WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();
            $results = $stmt->fetchAll();
    
            foreach ($results as $row){
                $pass=$row['pass'];
            
                if($new_pass!=$pass){
                    echo "パスワードが違います。";
                
                }elseif($new_pass==$pass){
                    $sql='UPDATE tb5_1 SET name=:new_name,comment=:new_comment WHERE id=:id';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':new_name',$new_name,PDO::PARAM_STR);
                    $stmt->bindParam(':new_comment', $new_comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
    
    }else{
    
    #新規投稿
        if(!empty($name) && !empty($comment) && !empty($pass_a)){
            $sql="INSERT INTO tb5_1 (name,comment,pass,date) VALUES(:name,:comment,:pass_a,now())";
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':name',$name,PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':pass_a', $pass_a, PDO::PARAM_STR);
            $stmt->execute();
    
    #削除
        }elseif(!empty($delete_num) && !empty($pass_b)){
            $id=$delete_num;
            $sql='SELECT * FROM tb5_1 WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            foreach ($results as $row){
                $pass=$row['pass'];
            
                if($pass_b!=$pass){
                    echo "パスワードが違います。";
                }elseif($pass_b==$pass){
                    $sql='delete from tb5_1 where id=:id';
                    $stmt=$pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_3-5</title>
    </head>
    <body>
        <form action="" method="post">
            
            <label for="name">名前：</label>
            <input type="text" name="name"
                   value=<?php
                            if(!empty($edit_name)){
                                echo $edit_name;
                            }
                         ?>>
            <br> 
            <label for="comment">コメント：</label>
            <input type="text" name="comment"
                   value=<?php 
                            if(!empty($edit_comment)){
                                echo $edit_comment;
                            }
                         ?>>
            <br>
            <label for="pass_a">パスワード：</label>
            <input type="text" name="pass_a">
            <br>
            
            <input type="submit" name="submit"><br>
            
            <br>
            
            <label for="delete_num">削除番号：</label>
            <input type="number" name="delete_num">
            <br>
            <label for="pass_b">パスワード：</label>
            <input type="text" name="pass_b">
            <br>            
            <input type="submit" name="delete" value="削除">
            <br>
            
            <br>
            
            <label for="edit_num">編集番号：</label>
            <input type="number" name="edit_num">
            <br>
            <label for="pass_c">パスワード：</label>
            <input type="text" name="pass_c">
            <br>
            
            <input type="submit" name="edit" value="編集">
            <br>
            
            <br>
            
            <label for="edit_NO"></label>
            <input type="hidden" name="edit_NO" 
                   value=<?php
                            if(!empty($edit_number)){
                                echo $edit_number;
                            }
                         ?>>  
            <br>
            
            <br>
            
        </form>
    </body>
</html>

<?php
#表示
$sql='SELECT * FROM tb5_1';
$stmt=$pdo->query($sql);
$results=$stmt->fetchAll();
foreach($results as $row){
    echo $row['id']."<>";
    echo $row['name']."<>";
    echo $row['comment']."<>";
    echo $row['date']."<>";
    echo $row['pass'];
    echo "<br>";
echo "<hr>";
}
?>


