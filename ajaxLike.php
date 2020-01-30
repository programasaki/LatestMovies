<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['productId']) && isset($_SESSION['user_id'])){
  debug('POST送信があります。');
  $m_id = $_POST['productId'];
  debug('レビューID：'.$m_id);
  try {
    $dbh = dbConnect();
    // レコードがあるか検索
    // likeという単語はLIKE検索とうSQLの命令文で使われているため、そのままでは使えないため、｀（バッククウォート）で囲む
    $sql = 'SELECT * FROM `like` WHERE user_id = :u_id AND movie_id = :m_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':m_id' => $m_id);
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);
    // レコードが１件でもある場合
    if(!empty($resultCount)){
      // レコードを削除する
      $sql = 'DELETE FROM `like` WHERE user_id = :u_id AND movie_id = :m_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':m_id' => $m_id);
      $stmt = queryPost($dbh, $sql, $data);
    }else{
      // レコードを挿入する
      $sql = 'INSERT INTO `like` (user_id, movie_id, create_date) VALUES (:u_id, :m_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':m_id' => $m_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>