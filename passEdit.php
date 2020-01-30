<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「');
  debug('パスワード変更');
  debug('」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //ログイン認証
  require('auth.php');

  //================================
  // 画面処理
  //================================
  // DBからユーザーデータを取得
  $userData = getUser($_SESSION['user_id']);
  debug('取得したユーザー：'.print_r($userData,true));

  if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));

    //変数にユーザー情報を代入
    $pass = $_POST['pass'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];

    //未入力チェック
    validRequired($pass, 'pass');
    validRequired($pass_new, 'pass_new');
    validRequired($pass_new_re, 'pass_new_re');

    if(empty($err_msg)){
      debug('未入力チェックOK');

      //古いパスワードのチェック
      validPass($pass, 'pass');

      //新しいパスワードのチェック
      validPass($pass_new, 'pass_new');
      validHalf($pass_new, 'pass_new');

      //古いパスワードとDBのパスワードを照合
      if(!password_verify($pass, $userData['pass'])){
        $err_msg['pass'] = MSG13;
      }

      //新しいパスワードと古いパスワードが同じかチェック
      if($pass === $pass_new){
        $err_msg['pass_new'] = MSG14;
      }

      //パスワード同値チェック
      validMatch($pass_new, $pass_new_re, 'pass_new', 'pass_new_re');

      if(empty($err_msg)){
        debug('バリデーションOK');
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET pass = :pass WHERE id = :id';
          $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':id' => $_SESSION['user_id']);
          $stmt = queryPost($dbh, $sql, $data);
          //クエリ成功の場合
          if($stmt){
            $_SESSION['msg_success'] = SUC01;
            header("Location:mypage.php"); //マイページへ
          }
        }catch(Exception $e){
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = "パスワード変更";
  require('head.php');
?>
  <body>
  <!--ヘッダー-->
  <?php require('header.php') ?>
    
    <!--メイン-->
    <section id="main">
      <div class="site-width">
      
        <h2 class="page-title">パスワード変更</h2>
        <form action="" method="post" class="form">
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <!--パスワード入力-->
          <label class="<?php if(!empty($err_msg['pass']))echo 'err'; ?>">
            現在のパスワード
            <div class="area-msg">
              <?php echo getErrMsg('pass'); ?>
            </div>
            <input type="password" name="pass" placeholder="現在のパスワード" value="<?php echo getFormData('pass'); ?>">
          </label>

          <label class="<?php if(!empty($err_msg['pass_new']))echo 'err'; ?>">
            新しいパスワード
            <div class="area-msg">
              <?php 
                echo getErrMsg('pass_new');  
              ?>
            </div>
            <input type="password" name="pass_new" placeholder="新しいパスワード" value="<?php echo getFormData('pass_new'); ?>">
          </label>

          <!--パスワード再入力-->
          <label class="<?php if(!empty($err_msg['pass_new_re']) || !empty($err_msg['pass_new'])) echo 'err'; ?>">
            <div class="area-msg">
              <?php echo getErrMsg('pass_new_re'); ?>
            </div>
            <input type="password" name="pass_new_re" placeholder="パスワード(再入力)" value="<?php echo getFormData('pass_new_re'); ?>">
          </label>

          <div class="submit-wrap">
            <input type="submit" value="変更する">
          </div>
        </form>

      </div>
    </section>
        
    <!--footer-->
    <?php require('footer.php'); ?>