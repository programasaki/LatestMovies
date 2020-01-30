<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('退会ページ');
  debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //ログイン認証
  require('auth.php');

  //================================
  //画面処理
  //================================
  if(!empty($_POST)){
    debug('POST送信があります');
    try{
      $dbh = dbConnect();
      $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
      $sql2 = 'UPDATE product SET delete_flg = 1 WHERE user_id = :us_id';
      $data = array(':us_id' => $_SESSION['user_id']);
      $stmt1 = queryPost($dbh, $sql1, $data);
      $stmt2 = queryPost($dbh, $sql2, $data);
      //クエリ成功の場合
      if($stmt1){
        //セッション削除
        session_destroy();
        debug('セッション変数の中身：'.print_r($_SESSION,true));
        debug('トップページへ遷移します');
        header("Location:index.php");
      }else{
        debug('クエリが失敗しました');
        $err_msg['common'] = MSG08;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e -> getMessage());
      $err_msg['common'] = MSG08;
    }
  }
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = '退会';
  require('head.php');
?>
  <body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>

    <!--メイン-->
    <section id="main">
      <div class="site-width">

        <form action="" method="post" class="form">
          <div class="area-msg">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>
          <p style="text-align: center;">
            本当に退会しますか？<br>
            これまでの記録は全て削除されます(復元できません)。
          </p>
          <div class="withdraw-submit">
            <input type="submit" name="submit" value="退会する">
          </div>
        </form>

      </div>
    </section>
    
    <!--footer-->
    <?php require('footer.php'); ?>