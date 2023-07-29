<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    
    <style>
        .example{
        text-align: center;
        border: 1px solid #999;
        padding: 10px;
        
        margin-top:10px;
        }
    </style>
    
    <style>
        .example2{
        text-align: left;
        border: 1px solid #999;
        padding: 10px;
        background: #fff9cc;
        margin-top:10px;
        }
    </style>
    
    <!------------------一番下への部分--------------------------------------------------->
    <div class="example">
        <button style="width:200px;height:50px" id="bottompage">一番下へ</button>
    </div>
    
    <script type="text/javascript"> 
        document.querySelector("#bottompage").addEventListener("click", () => {
            window.scrollTo(0, document.body.scrollHeight);
        })
        
        
    </script>
     <!------------------一番下への部分--------------------------------------------------->
    
    
    
    
    <?php
        
        
        
        //------------------------DB接続設定------------------------------------------------------
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザ名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //----------------------------------------------------------------------------------------
        
        //-------------テーブルそのものを削除---------------
        //$sql = 'DROP TABLE IF EXISTS mainTable';
        //$stmt = $pdo->query($sql);
        //--------------------------------------------------
        
        //------------------tableの作成-------------------
        
        $mainTable = "mainTable";//テーブルの名前を指定
        
        $sql = "CREATE TABLE IF NOT EXISTS ".$mainTable
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "postNumber INT,"
        . "name char(32),"
        . "date TEXT,"
        . "password TEXT,"
        . "comment TEXT"
        .");";
        $stmt = $pdo->query($sql);
        //------------------------------------------------
        
        
        //------------------------テーブル自体を表示--------------
        //$sql ='SHOW TABLES';
        //$result = $pdo -> query('SHOW TABLES');
        
        //foreach ($result as $row){
       //     echo $row[0];
        //    echo '<br>';
       // }
       // echo "<hr>";
        //--------------------------------------------------------------
        
        
        
        
        
        
        //-------------削除した投稿を復元-------------
        //$data = GetDataFromSQL($mainTable,$pdo);
        //$postNumber = GetLatestPostNumber($data);
        //for($i = 1 ;$i <= $postNumber;$i++){
        //    RestorePost($mainTable,$pdo,$i);
        //}
        //--------------------------------------------
        

        
        
        
        
        
        //---------各種変数の宣言-------------
        $postNumber = 0;
        $name;
        $nameDefault = "名無し";
        $comment;
        $separator = "<>";
        $indention = "<br>";
        $date = date("Y/m/d H:i:s");
        $deletePostNumber;
        $deleteFlag = -1;
        $editedPhrase = " (編集済み)";
        $password;
        
        $passwordDelete;
        $passwordEditing;
        $passwordEdited;
        
        //フラグ関連
        $hiddenFlag = false;
        $editFlag = false;
        $saveFlagName = false;
        $saveFlagComment = false;
        $saveFlagPassword = false;
        
        //アラート関連
        $alertFlag = false;
        $alertPhrase = null;
        //-----------------------------------
        
        
        
        //データ取得
        $data = GetDataFromSQL($mainTable,$pdo);
        
        
        //最新の投稿番号を取得
        $postNumber = GetLatestPostNumber($data) + 1;
        
        
        //--------------複数の入力フォームに入力があった場合の例外処理-------------------
        if( (!empty($_POST["editPostNumber"]) && !empty($_POST["deletePostNumber"])) ||
            (!empty($_POST["editPostNumber"]) && !empty($_POST["comment"])) ||
            (!empty($_POST["deletePostNumber"]) && !empty($_POST["comment"])) ){
            $alertFlag = true;
            $alertPhrase = $alertPhrase."<br>【投稿・削除・編集は同時に行えません.】<br>";
            //echo "<br>投稿・削除・編集は同時に行えません.<br>";
            //echo "<br><span style='color: red'>投稿・削除・編集は同時に行えません.</span><br>";//赤色で表示
        }
        //-------------------------------------------------------------------------------
        
        //-----------------------------------------コメントが送信された時の処理---------------------------------------------------------------
        elseif( empty($_POST["editingPostNumber"]) && !empty($_POST["submit"])){// !empty( $_POST["name"] ) && !empty( $_POST["comment"] ) && !empty( $_POST["password"] ) && empty( $_POST["editingPostNumber"] )){
            
            //フォームから読み込み
            if( !empty($_POST["name"]) ){
                $name = $_POST["name"];
                $saveFlagName = true;
            }else{
                $name = $nameDefault;
                $saveFlagName = true;
            }
            //フォームから読み込み
            if( !empty($_POST["comment"]) ){
                $comment = $_POST["comment"];
                $saveFlagComment = true;
            }else{
                
                $alertFlag = true;
                $alertPhrase = $alertPhrase."<br>【コメントを入力してください】<br>";
                
                //echo "<br>コメントを入力してください<br>";
                $saveFlagComment = false;
            }
            //フォームから読み込み
            if( !empty($_POST["password"]) ){
                $password = $_POST["password"];
                $saveFlagPassword = true;
            }else{
                
                $alertFlag = true;
                $alertPhrase = $alertPhrase."<br>【パスワードを入力してください】<br>";
                
                //echo "<br>パスワードを入力してください<br>";
                $saveFlagPassword = false;
            }
            
            if($saveFlagName&&$saveFlagComment&&$saveFlagPassword){
                
                $alertFlag = true;
                $alertPhrase = $alertPhrase."<br>コメント【 ".$comment." 】を受け付けました！ ありがとう【 ".$name." 】さん！"." パスワード【".$password."】<br>";
                
                //echo "<br>コメント【 ".$comment." 】を受け付けました！ ありがとう【 ".$name." 】さん！"." パスワード【".$password."】<br>";
                
                //SQLに書き込み
                InsertData($mainTable,$pdo,$postNumber,$name,$date,$password,$comment);
                $saveFlagName = false;
                $saveFlagPassword = false;
                $saveFlagComment = false;
            }
            
        }
        //------------------------------------------------------------------------------------------------------------------------------------
        
        //---------------------削除---------------------------------------------------
        elseif(!empty( $_POST["deletePostNumber"] )){
            $deletePostNumber = $_POST["deletePostNumber"];
            $passwordDelete = $_POST["passwordDelete"];
            
            //データ取得
            $data = GetDataFromSQL($mainTable,$pdo);
            
            if($deletePostNumber <= GetLatestPostNumber($data) && GetPostNumber($data,$deletePostNumber) > 0){
                if(GetPassword($data,$deletePostNumber) == $passwordDelete){
                    
                    DeletePost($mainTable,$pdo,$deletePostNumber);
                    $alertFlag = true;
                    $alertPhrase = $alertPhrase."<br>投稿番号【".$deletePostNumber."】の投稿を消去しました.<br>";
                    //echo "<br>投稿番号【".$postNumber."】の投稿を消去しました.<br>";
                }else{
                    $alertFlag = true;
                    $alertPhrase = $alertPhrase."<br>パスワードが正しくありません<br>";
                    //echo "<br>パスワードが正しくありません<br>";
                }
                
            }else{
                $alertFlag = true;
                $alertPhrase = $alertPhrase."<br>投稿番号【".$deletePostNumber."】は存在しません．<br>";
                //echo "<br>投稿番号【".$deletePostNumber."】は存在しません．<br>";
            }
            
        }
        //----------------------------------------------------------------------------
        
        //------------------------------編集---------------------------------------------
        elseif(!empty( $_POST["editPostNumber"] ) ){
            
            //編集開始
            $editFlag = true;
            
            //formから値を受け取る
            $editPostNumber = $_POST["editPostNumber"];
            
            //データ取得
            $data = GetDataFromSQL($mainTable,$pdo);
            
            
            if(GetPostNumber($data,$editPostNumber) > 0){
                
                //ここでpasswordを受け取る処理
                $passwordEditing = $_POST["passwordEditing"];
                
                if(GetPassword($data,$editPostNumber) == $passwordEditing){
                    $alertFlag = true;
                    $alertPhrase = $alertPhrase."<br>投稿番号【".$editPostNumber."】を編集中...";
                    //echo "<br>投稿番号【".$editPostNumber."】を編集中...";
                
                    
                
                
                    //編集フォームに指定のコメントを持ってくる
                    $editingComment = GetComment($data,$editPostNumber);
                    //$editingComment = str_replace($editedPhrase,"",$editingComment);//(編集済み)を取り除く
                    
                    $editingPostNumber = $editPostNumber;
                    $editingName = GetName($data,$editPostNumber);
                    
                    $passwordEditing = GetPassword($data,$editPostNumber);
                    
                    
                    //------------------tableの作成-------------------
        
                    $editArchive = "editArchive";//テーブルの名前を指定
                    
                    $sql = "CREATE TABLE IF NOT EXISTS ".$editArchive
                    ." ("
                    . "id INT AUTO_INCREMENT PRIMARY KEY,"
                    . "postNumber INT,"
                    . "name char(32),"
                    . "date TEXT,"
                    . "password TEXT,"
                    . "comment TEXT"
                    .");";
                    $stmt = $pdo->query($sql);
                    //------------------------------------------------
                    
                    //SQLに書き込み(編集前のデータをアーカイブとして残しておく)
                    InsertData($editArchive,$pdo,$editingPostNumber,$editingName,date("Y/m/d H:i:s"),$passwordEditing,$editingComment);
                    
                    
                    //不必要なフォームを隠す
                    $hiddenFlag = true;
                    
                }else{
                    $editingComment = null;
                    $editingPostNumber = null;
                    $editingName = "名無し";
                    $passwordEditing = null;
                    
                    $alertFlag = true;
                    $alertPhrase = $alertPhrase."<br>パスワードが正しくありません<br>";
                    //echo "<br>パスワードが正しくありません<br>";
                }
            }else{
                 $editingComment = null;
                 $editingPostNumber = null;
                 $editingName = "名無し";
                 $passwordEditing = null;
                 
                 $alertFlag = true;
                 $alertPhrase = $alertPhrase."<br>投稿番号【".$editPostNumber."】は存在しません．<br>";
                 //echo "<br>投稿番号【".$editPostNumber."】は存在しません．<br>";
            }
            
            
        }
        if(!empty( $_POST["comment"] ) && !empty($_POST["editingPostNumber"])){
            
            //formから値を受け取る
            $editedComment = $_POST["comment"];
            $editingPostNumber = $_POST["editingPostNumber"];
            $editedName = trim($_POST["name"]);//累積する空白を取り除く
            $passwordEdited = $_POST["password"];
            
            
            
            //データ取得
            $data = GetDataFromSQL($mainTable,$pdo);
            
            
            //-----------------------編集--------------------------
            $id = $editingPostNumber;
            $sql = 'UPDATE '.$mainTable.' SET postNumber=:postNumber, name=:name, date=:date, password=:password, comment=:comment WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':postNumber', $editingPostNumber, PDO::PARAM_STR);
            $stmt->bindParam(':name', $editedName, PDO::PARAM_STR);
            $date = date("Y/m/d H:i:s").$editedPhrase;//（編集済み）を付け足す
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordEdited, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $editedComment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            //-----------------------------------------------------
            
            $alertFlag = true;
            $alertPhrase = $alertPhrase."<br>投稿番号【".$editingPostNumber."】を編集しました！<br>";
            //echo "<br>投稿番号【".$editingPostNumber."】を編集しました！<br>";
            //編集終了
            $editFlag = false;
        }
        
        //-------------------------------------------------------------------------------
        
        
        
        
        
        
    ?>
    
    <?php
        
        
        
        //投稿番号0の投稿
        FirstPost($pdo);
        //ファイル内部の表示
        Display($mainTable,$pdo);
        
        //アラートの表示
        if($alertFlag){
            echo $alertPhrase;
            $alertFlag = false;
        }
    ?>
    
    
    <style>
        <!--送信ボタン-->
        msr_sendbtn_04 {
          margin:0 0 10px;
          position: relative;
          height: 50px;
          width: 150px;
        }
        .msr_sendbtn_04 input[type=submit] {
          background: #000000;
          background-position: 10px;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
          -webkit-box-sizing: border-box;
          border: none;
          color: #FFFFFF;
          cursor: pointer;
          font-size: 14px;
          transition: 0.2s ease-in-out;
          -o-transition: 0.2s ease-in-out;
          -moz-transition: 0.2s ease-in-out;
          -webkit-transition: 0.2s ease-in-out;
          height: 50px;
          width: 150px;
        }
        .msr_sendbtn_04:before {
          border: 6px solid transparent;
          border-left: 6px solid #FFFFFF;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
          -webkit-box-sizing: border-box;
          content: "";
          display: inline-block;
          height: 12px;
          position: absolute;
          top: 20px;
          left: 20px;
          vertical-align: middle;
          width: 6px;
          z-index: 10;
        }
        .msr_sendbtn_04 input[type=submit]:hover {
          opacity: 0.6;
        }
        .msr_sendbtn_04_disabled input[type=submit]{
          background: #FFFFFF;
          border: 5px solid #DDDDDD;
          color: #DDDDDD;
          cursor: default;
        }
        .msr_sendbtn_04_disabled:before {
          border-left: 6px solid #DDDDDD;
          content: "";
        }
        .msr_sendbtn_04_disabled input[type=submit]:hover {
          opacity: 1;
        }
        <!--送信ボタン-->
        
        
        <!--テキスト-->
        .msr_text_04 {
          padding-bottom: 20px;
          width: 460px;
        }
        .msr_text_04 label {
          display: block;
          font-size: 14px;
          padding-bottom: 5px;
        }
        .msr_text_04 input[type=text] {
          border: 5px solid #000000;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
          -webkit-box-sizing: border-box;
          color: #000000;
          font-size: 13px;
          padding: 10px;
          height: 40px;
          width: 200px;
        }
        .msr_text_04 input[type=number] {
          border: 5px solid #000000;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
          -webkit-box-sizing: border-box;
          color: #000000;
          font-size: 13px;
          padding: 10px;
          height: 40px;
          width: 100px;
        }
        <!--テキスト-->
        
        <!--テキスエリア-->
        .msr_textarea_04 {
          padding-bottom: 10px;
          width: 460px;
        }
        .msr_textarea_04 label {
          display: block;
          font-size: 14px;
          padding-bottom: 5px;
        }
        .msr_textarea_04 textarea {
          border: 5px solid #000000;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
          -webkit-box-sizing: border-box;
          color: #000000;
          font-size: 20px;
          padding: 10px;
          height: 150px;
          width: 400px;
        }
        <!--テキスエリア-->
    </style>
    
    
    

    
    <form action="" method="post" id="bottompage">
        
        <input type="hidden" name="editingPostNumber" value ="<?php if($editFlag){ echo $editingPostNumber;}?>" ><br><!--type = "hidden"-->
        <div class="msr_text_04">
            
            <input type="text" name="name" placeholder="名前" value="<?php if($editFlag){ echo $editingName;}elseif($saveFlagName){echo $name;}//else{echo "名無し";}?>">
            <!--<input type="text" name="comment" placeholder="コメント" style="width: 500px; height: 100px;" value ="<?php //if($editFlag){echo $editingComment;}elseif($saveFlagComment){echo $comment;}?>"><br>-->
            
            <p class="msr_textarea_04">
                <textarea type="text" name="comment" placeholder="コメント" cols="50" rows="10" wrap="hard"><?php if($editFlag){echo $editingComment;}elseif($saveFlagComment){echo $comment;}?></textarea>
            </p>
            <input type="text" name="password" placeholder="パスワードを設定" value="<?php if($editFlag){echo $passwordEditing;}elseif($saveFlagPassword){echo $password;}?>">
            <p class="msr_sendbtn_04"><input type="submit" name="submit" value="送信"></p><!--submit指定で「送信ボタン」を生成-->
            
        </div>
        
        
        <?php echo "<br>"//if(!$hiddenFlag){echo "<hr>";}else{echo "<br>";}?>
        
        
        <div class="msr_text_04">
            
            <input type="<?php if($hiddenFlag){echo "hidden";}else{echo "number";}?>" name="deletePostNumber" placeholder="削除番号" min="1">
            <input type="<?php if($hiddenFlag){echo "hidden";}else{echo "text";}?>" name="passwordDelete" placeholder="パスワードを入力" >
            <p class="msr_sendbtn_04"><input type="<?php if($hiddenFlag){echo "hidden";}else{echo "submit";}?>" name="submitDelete" value="削除申請"></p>
        </div>
        
        <?php echo "<br>"//if(!$hiddenFlag){echo "<hr>";}?>
        
        
        <div class="msr_text_04">
            
            <input type="<?php if($hiddenFlag){echo "hidden";}else{echo "number";}?>" name="editPostNumber" placeholder="編集番号"min="1" >
            <input type="<?php if($hiddenFlag){echo "hidden";}else{echo "text";}?>" name="passwordEditing" placeholder="パスワードを入力">
            <p class="msr_sendbtn_04"><input type="<?php if($hiddenFlag){echo "hidden";}else{echo "submit";}?>" name="submitEdit" value="編集申請"></p>
        </div>
        
        <?php echo "<br>"//if(!$hiddenFlag){echo "<hr><br>";}?>
        
    </form>
    
    <!------------------一番上への部分--------------------------------------------------->
    <div class="example">
        <button style="width:200px;height:50px" id="toppage">一番上へ</button>
    </div>
    
    <script type="text/javascript"> 
        document.querySelector("#toppage").addEventListener("click", () => {
            window.scrollTo(0,0);
        })
    </script>
    <!------------------一番上への部分--------------------------------------------------->
    
  
    
    <!--ここから関数-->
    <?php
        
        //投稿番号とインデックスを一致させるため投稿番号0を入れる．sqlに要らないかも
        function FirstPost($pdo){
            
            
            //------------------tableの作成-------------------
            $tableName = "firstPost";//テーブルの名前を指定
            
            $sql = "CREATE TABLE IF NOT EXISTS ".$tableName
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "postNumber INT,"
            . "name char(32),"
            . "date TEXT,"
            . "password TEXT,"
            . "comment TEXT"
            .");";
            $stmt = $pdo->query($sql);
            //------------------------------------------------
            
            
            //-----------------------データを書き込む---------------------------
            $postNumber = 0;
            $name = "管理人";
            $date = date("Y/m/d H:i:s");
            $password = "ThisIsMine!";
            $comment = "掲示板へようこそ！
                        <h2>決まり事</h2><ul>
                        <li>投稿・削除・編集は同時にできません</li>
                        <li>コメントにパスワードを入力しない</li>
                        </ul>";
            if(empty($pdo->query('SELECT * FROM '.$tableName)->fetchAll())){
                $sql = $pdo -> prepare("INSERT INTO ".$tableName." (postNumber, name, date, password, comment) VALUES (:postNumber, :name, :date, :password, :comment)");
                $sql -> bindParam(':postNumber', $postNumber, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> execute();
            }
            
            //---------------------------------------------------------------------------------------------------------
            
            Display($tableName,$pdo);
        }

        
        //SQLからデータを取得
        function GetDataFromSQL($tableName,$pdo){
            
            $sql = 'SELECT * FROM '.$tableName;
            $stmt = $pdo->query($sql);
            return $pdo->query('SELECT * FROM '.$tableName)->fetchAll();
        }
       
       
       //指定したテーブルにデータを挿入
       function InsertData($tableName,$pdo,$postNumber,$name,$date,$password,$comment){
           
           $sql = $pdo -> prepare("INSERT INTO ".$tableName." (postNumber, name, date, password, comment) VALUES (:postNumber, :name, :date, :password, :comment)");
           $sql -> bindParam(':postNumber', $postNumber, PDO::PARAM_STR);
           $sql -> bindParam(':name', $name, PDO::PARAM_STR);
           $sql -> bindParam(':date', $date, PDO::PARAM_STR);
           $sql -> bindParam(':password', $password, PDO::PARAM_STR);
           $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
           $sql -> execute(); 
       }
       
       
        
        //最新の投稿番号の取得
        function GetLatestPostNumber($data){//動作確認済み
            if(!empty($data)){
                
                return abs(end($data)['postNumber']);
                
            }
        }
        
        
        
        
        //指定した投稿番号のコメントを取得
        function GetComment($data,$postNumber){//動作確認済み
            
            if(!empty($data)){
                
                return $data[$postNumber -1]['comment'];
                
            }
        }
        
        //指定した投稿番号の投稿番号を取得
        function GetPostNumber($data,$postNumber){//動作確認済み
            
            if(!empty($data)){
                
                return $data[$postNumber -1]['postNumber'];
                
            }
        }
        
                
        //指定した投稿番号の名前を取得
        function GetName($data,$postNumber){//動作確認済み
            
            if(!empty($data)){
                
                return $data[$postNumber -1]['name'];
                
            }
        }
        
        //指定した投稿番号のパスワードを取得
        function GetPassword($data,$postNumber){//動作確認済み
            
            if(!empty($data)){
                
                return $data[$postNumber -1]['password'];
                
            }
        }
        
        //ファイルの内容を表示，-のものは表示しない.
        function Display($tableName,$pdo){
            
            //--------------------------------------------------------------
            $results = GetDataFromSQL($tableName,$pdo);
            foreach ($results as $row){
                
                if($row['postNumber'] >= 0){
                    //$rowの中にはテーブルのカラム名が入る
                    echo "<br>";
                    echo $row['postNumber'].' ';
                    echo "【".$row['name']."】".' ';
                    echo $row['date'].'<br>';
                    echo nl2br(str_replace($row['password'],"***",$row['comment'])."<br>");//本文中のpasswordを***で置き換えるver
                    //echo nl2br($row['comment'].'<br>');//nl2br()で改行が可能！
                }
            }
            //--------------------------------------------------------------
            
        }
        
        //消去
        function DeletePost($tableName,$pdo,$postNumber){//動作確認済み
            
            //削除する投稿データの投稿番号を-1を掛けた状態にする
            //-----------------------編集--------------------------
            $id = $postNumber;
            //$sql = 'UPDATE '.$tableName.' SET postNumber=:postNumber WHERE id=:id';
            $sql = 'UPDATE mainTable SET postNumber=:postNumber WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $tmp = $postNumber*-1;
            $stmt->bindParam(':postNumber', $tmp, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            //-----------------------------------------------------
            
        }
        
        //復元
        function RestorePost($tableName,$pdo,$postNumber){//動作確認済み
            
            //復元する投稿データの投稿番号を自然数に戻す
            //-----------------------編集--------------------------
            $id = $postNumber;
            $sql = 'UPDATE '.$tableName.' SET postNumber=:postNumber WHERE id=:id';
            //$sql = 'UPDATE mainTable SET postNumber=:postNumber WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':postNumber', $postNumber, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            //-----------------------------------------------------
            
            echo "<br>投稿番号【".$postNumber."】の投稿を復元しました.<br>";
            
        }
        
        
    
    ?>
    
    
</body>
</html>
