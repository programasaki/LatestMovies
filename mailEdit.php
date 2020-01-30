<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('メールアドレス変更');
  debug('」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //ログイン認証
  require('auth.php');

  //================================
  // 画面処理
  //================================
  // DBからユーザーデータを取得
  $dbFormData = getUser($_SESSION['user_id']);
  debug('取得したユーザー：'.print_r($dbFormData,true));

  if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));

    //変数にユーザー情報を代入
    $email = $_POST['email'];

    //未入力チェック
    validRequired($email, 'email');

    if(empty($err_msg)){
      //DBの情報と違う場合にバリデーションを行う
      if($dbFormData['email'] !== $email){
        //メール形式チェック
        validEmail($email, 'email');
        //最大文字数チェック
        validMaxLen($email, 'email');
        //重複チェック
        validEmailDup($email);
      }

      //新しいパスワードと古いパスワードが同じかチェック
      if($email === $dbFormData['email']){
        $err_msg['email'] = MSG15;
      }

      if(empty($err_msg)){
        debug('バリデーションOKです');
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET email = :email WHERE id = :u_id';
          $data = array(':email' => $email, ':u_id' => $dbFormData['id']);
          $stmt = queryPost($dbh, $sql, $data);
          if($stmt){
            $_SESSION['msg_success'] = SUC02;
            debug('マイページへ遷移します');
            header("Location:mypage.php");
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
  $siteTitle = 'メールアドレス変更';
  require('head.php');
?>
  <body>
  <!--ヘッダー-->
  <?php require('header.php') ?>

    <!--メイン-->
    <section id="main">
      <div class="site-width">

        <h2 class="page-title">メールアドレス変更</h2>
        <form action="" method="post" class="form">
          <div class="area-msg">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>
          <!--メールアドレス-->
          <label class="<?php if(!empty($err_msg['email']) || !empty($err_msg['email'])) echo 'err'; ?>">
            新しいメールアドレス
            <div class="area-msg">
              <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="submit-wrap">
            <input type="submit" value="変更する">
          </div>
        </form>  

      </div>
    </section>
        
    <!--footer-->
    <?php require('footer.php'); ?>