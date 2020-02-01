<?php

  //=====================================
  //ログ
  //=====================================
  ini_set('log_errors','on');
  ini_set('error_log','php.log');

  //=====================================
  //デバッグ
  //=====================================
  $debug_flg = true;
  
  function debug($str) {
    global $debug_flg;
    if(!empty($debug_flg)){
      error_log('デバッグ；'.$str);
    }
  }

  //セッション準備
  session_save_path("/var/tmp");
  ini_set('session.gc_maxlifetime', 60*60*24*30);
  ini_set('session.cookie_lifetime', 60*60*24*30);
  session_start();
  session_regenerate_id();

  //================================
  // 画面表示処理開始ログ吐き出し関数
  //================================
  function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION, true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
      debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
  }


  //=====================================
  //定数
  //=====================================
  define('MSG01', '入力必須です');
  define('MSG02', 'Email形式で入力してください');
  define('MSG03', '255文字以内で入力してください');
  define("MSG04", 'パスワードが一致していません');
  define("MSG05", '半角英数字のみご利用いただけます');
  define("MSG06", '6文字以上入力してください');
  define("MSG07", 'このEmailはすでに登録されています');
  define("MSG08", 'エラーが発生しました。しばらく経ってからやり直してください。');
  define("MSG09", 'メールアドレスまたはパスワードが違います');
  define('MSG10', '半角数字のみご利用いただけます');
  define('MSG11', '15文字以下で入力してください');
  define('MSG12', '160文字以下で入力してください');
  define('MSG13', '現在のパスワードが違います');
  define('MSG14', '現在のパスワードと同じです');
  define('MSG15', '現在のメールアドレスと同じです');
  define('SUC01', 'パスワードを変更しました');
  define('SUC02', 'プロフィールを変更しました');
  define('SUC03', 'メールを送信しました');
  define('SUC04', '登録しました');
  define('SUC05', 'レビュー投稿しました');


  //エラーメッセージ格納用の配列
  $err_msg = array();
  $suc_msg = array();

  //バリデーション関数（未入力チェック）
  function validRequired($str, $key){
    if(empty($str)){
      global $err_msg;
      $err_msg[$key] = MSG01;
    }
  }

  //メール形式チェック
  function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG02;
    }
  }

  //最大文字数チェック
  function validMaxLen($str, $key, $max = 255){
    if(mb_strlen($str) > $max){
      global $err_msg;
      $err_msg[$key] = MSG03;
    }
  }

  //最小文字数チェック
  function validMinLen($str, $key){
    if(mb_strlen($str) < 6){
      global $err_msg;
      $err_msg[$key] = MSG06;
    }
  }

  //文字数チェック（15文字以下）
  function valid15Len($str, $key, $len = 15){
    if(mb_strlen($str) > $len){
      global $err_msg;
      $err_msg[$key] = MSG11;
    }
  }

  //文字数チェック（160文字以下）
  function valid160Len($str, $key, $len = 160){
    if(mb_strlen($str) > $len){
      global $err_msg;
      $err_msg[$key] = MSG12;
    }
  }

  function validMatch1($str, $key){
    if($str !== $key){
      global $err_msg;
      $err_msg[$key] = MSG04;
    }
  }

  //同値チェック
  function validMatch($str1, $str2, $key, $key2){
    if($str1 !== $str2){
      global $err_msg;
      $err_msg[$key] = MSG04;
    }
  }

  //半角英数字チェック
  function validHalf($str, $key){
    if(!preg_match("/^[0-9a-zA-Z]*$/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG05;
    }
  }

  //メールアドレス重複チェック
  function validEmailDup($email){
    global $err_msg;
    try{
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      $stmt = queryPost($dbh, $sql, $data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!empty(array_shift($result))){
        $err_msg['email'] = MSG07;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }

  //半角数字チェック
  function validNumber($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG10;
    }
  }

  //パスワードチェック
  function validPass($str, $key){
    //半角英数字チェック
    validHalf($str, $key);
    //最大文字数チェック
    validMaxLen($str, $key);
    //最小文字数チェック
    validMinLen($str, $key);
  }

  //エラーメッセージを表示
  function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
      return $err_msg[$key];
    }
  }

  /*//DB接続
  function dbConnect(){
    //DBへの接続準備
    $dsn = 'mysql:dbname=LatestMovies;host=localhost;charset=utf8mb4';
    $user = 'root';
    $password = 'root';
    $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    //PDOオブジェクトを生成（DBへ接続）
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
  }*/

  //本番環境
  function dbConnect(){
    $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
    $db['dbname'] = ltrim($db['path'], '/');
    $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8mb4";
    $user = $db['user'];
    $password = $db['pass'];
    $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
    );
    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
  }

  function queryPost($dbh, $sql, $data){
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセットし、SQL文を実行
    if(!$stmt->execute($data)) {
      debug('クエリ失敗しました');
      debug('失敗したSQL：'.print_r($stmt,true));
      $err_msg['common'] = MSG08;
      return 0;
    }
  debug('クエリ成功');
  return $stmt;
  }

  function getUser($u_id){
    debug('ユーザー情報を取得します');
    debug('ユーザーID：'.$u_id);
    try {
      $dbh = dbConnect();
      $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
      $data = array(':u_id' => $u_id);
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        // クエリ結果のデータを１レコード返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getMovie($m_id){
    debug('映画情報を取得します');
    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM movie LIMIT 0,10';
      $data = array();
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        return $stmt->fetchAll();
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getMovie2($m_id){
    debug('映画情報を取得します');
    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM movie LIMIT 10,10';
      $data = array();
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        // クエリ結果のデータを全レコード返却
        return $stmt->fetchAll();
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getMovieOne($m_id){
    debug('映画情報を取得します');
    debug('映画ID：'.$m_id);
    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM movie WHERE id = :id';
      $data = array(':id' => $m_id);
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        //クエリ結果のデータを1レコード返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getReview($u_id, $r_id){
    debug('レビューを取得します');
    debug('ユーザーID：'.$u_id);
    debug('レビューID：'.$r_id);
    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM review WHERE user_id = :u_id AND id = :r_id AND delete_flg = 0';
      $data = array(':u_id' => $u_id, ':r_id' => $r_id);
      $stmt = queryPost($dbh, $sql, $data);
      if(!$stmt){
        //クエリ結果のデータを1レコード返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getReviewOne($r_id){
    debug('レビュー情報を取得します。');
    debug('レビューID：'.$r_id);
    try { 
      $dbh = dbConnect();
      $sql = 'SELECT r.id, r.user_id, r.username, r.movie_id, r.thumbnail, r.movie_img, r.title, r.score, r.comment, r.create_date, r.update_date
              FROM review AS r LEFT JOIN users AS u ON r.user_id = u.id WHERE r.id = :r_id AND r.delete_flg = 0 AND u.delete_flg = 0';
      $data = array(':r_id' => $r_id);
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        // クエリ結果のデータを１レコード返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
  }

  function getReviewList($m_id, $currentMinNum = 1, $span = 5){
    debug('レビュー一覧を取得します');
    try{
      $dbh = dbConnect();
      //ページネーション用SQL文
      $sql = 'SELECT * FROM review AS r INNER JOIN movie AS m ON r.movie_id = m.id WHERE m.id = :m_id';
      $data = array(':m_id' => $m_id);
      $stmt = queryPost($dbh, $sql, $data);
      $rst['total'] = $stmt->rowCount(); //総レコード数
      $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
      if(!$stmt){
        return false;
      }
      //レビュー用SQL文
      $sql = 'SELECT * FROM review AS r INNER JOIN movie AS m ON r.movie_id = m.id WHERE m.id = :m_id';
      $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
      $data = array(':m_id' => $m_id);
      debug('SQL:'.$sql);
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        //クエリ結果の全レコードを格納
        $rst['data'] = $stmt->fetchAll();
        return $rst;
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getMyReview($u_id){
    debug('レビューを取得しています');
    debug('ユーザーID：'.$u_id);
    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM review WHERE user_id = :u_id AND delete_flg = 0';
      $data = array(':u_id' => $u_id);
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        return $stmt->fetchAll();
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getMyReviewOne($r_id){
    debug('レビューを取得しています');
    debug('レビューID：'.$r_id);
    try{
      $dbh = dbConnect();
      $sql = 'SELECT * 
              FROM review AS r INNER JOIN movie AS m ON r.movie_id = m.id INNER JOIN users AS u ON r.user_id = u.id WHERE r.id = :r_id AND r.delete_flg = 0';
      $data = array(':r_id' => $r_id);
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function getMyLike($u_id){
  debug('自分のお気に入り情報を取得します。');
  debug('ユーザーID：'.$u_id);
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM `like` AS l LEFT JOIN movie AS m ON l.movie_id = m.id WHERE l.user_id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

  function isLike($u_id, $m_id){
  debug('お気に入り情報があるか確認します。');
  debug('ユーザーID：'.$u_id);
  debug('映画ID：'.$m_id);
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM `like` WHERE user_id = :u_id AND movie_id = :m_id';
    $data = array(':u_id' => $u_id, ':m_id' => $m_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt->rowCount()){
      debug('お気に入りです');
      return true;
    }else{
      debug('特に気に入ってません');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

  //サニタイズ
  function sanitize($str){
    return htmlspecialchars($str, ENT_QUOTES);
  }

  // フォーム入力保持
  function getFormData($str){
    global $dbFormData;
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
      //フォームのエラーがある場合
      if(!empty($err_msg[$str])){
        //POSTにデータがある場合
        if(isset($_POST[$str])){
          return sanitize($_POST[$str]);
        }else{
          //ない場合（基本ありえない）はDBの情報を表示
          return sanitize($dbFormData[$str]);
        }
      }else{
        //POSTにデータがあり、DBの情報と違う場合
        if(isset($_POST[$str]) && $method[$str] !== $dbFormData[$str]){
          return sanitize($_POST[$str]);
        }else{
          return sanitize($dbFormData[$str]);
        }
      }
    }else{
      if(isset($_POST[$str])){
        return sanitize($_POST[$str]);
      }
    }
  }

  //sessionを1回だけ取得
  function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
      $data = $_SESSION[$key];
      $_SESSION[$key] = '';
      return $data;
    }
  }

  //認証キー生成
  function makeRandKey($length = 8){
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i = 0; $i < $length; ++$i){
      $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
  }

  //画像処理
  function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));
  
  if (isset($file['error']) && is_int($file['error'])) {
    try {
      // バリデーション
      switch ($file['error']) {
          case UPLOAD_ERR_OK: // OK
              break;
          case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
              throw new RuntimeException('ファイルが選択されていません');
          case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
          case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
              throw new RuntimeException('ファイルサイズが大きすぎます');
          default: // その他の場合
              throw new RuntimeException('その他のエラーが発生しました');
      }
      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
          throw new RuntimeException('画像形式が未対応です');
      }
      // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
      // ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
      // DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
      // image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      // 保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);
      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;
    } catch (RuntimeException $e) {
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}

  //ページネーション
  //$currentPageNum：現在のページ数
  //$totalPageNum：総ページ数
  //$pageColNum：ページネーション表示数
  function pagination( $currentPageNum, $totalPageNum, $pageColNum = 5){
    // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
    if( $currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum - 4;
      $maxPageNum = $currentPageNum;
    // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
    }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum - 3;
      $maxPageNum = $currentPageNum + 1;
    // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
    }elseif( $currentPageNum == 2 && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum - 1;
      $maxPageNum = $currentPageNum + 3;
    // 現ページが1の場合は左に何も出さない。右に５個出す。
    }elseif( $currentPageNum == 1 && $totalPageNum >= $pageColNum){
      $minPageNum = $currentPageNum;
      $maxPageNum = 5;
    // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
    }elseif($totalPageNum < $pageColNum){
      $minPageNum = 1;
      $maxPageNum = $totalPageNum;
    // それ以外は左に２個出す。
    }else{
      $minPageNum = $currentPageNum - 2;
      $maxPageNum = $currentPageNum + 2;
    }
    
    echo '<div class="pagination">';
      echo '<ul class="pagination-list">';
      global $movieDataOne;
        if($currentPageNum != 1){
          echo '<li class="list-item"><a href="?m_id='.$movieDataOne['id'].'&p=1">&lt;</a></li>';
        }
        for($i = $minPageNum; $i <= $maxPageNum; $i++){
          echo '<a href="?m_id='.$movieDataOne['id'].'&p='.$i.'"><li class="list-item ';
          if($currentPageNum == $i ){ echo 'active'; }
          echo '">'.$i.'</li></a>';
        }
        if($currentPageNum != $maxPageNum && $maxPageNum > 1){
          echo '<li class="list-item"><a href="?m_id='.$movieDataOne['id'].'&p='.$maxPageNum.'">&gt;</a></li>';
        }
      echo '</ul>';
    echo '</div>';
  }

  //画像表示用関数
  function showImg($path){
    if(empty($path)){
      return 'images/sample.jpg';
    }else{
      return $path;
    }
  }

  //GETパラメータ付与
  function appendGetParam($arr_del_key){
    if(!empty($_GET)){
      $str = '?';
      foreach($_GET as $key => $val){
        if(!in_array($key, $arr_del_key, true)){ 
          $str .= $key.'='.$val.'&';
        }
      }
      $str = mb_substr($str, 0, -1, "UTF-8");
      return $str;
    }
  }