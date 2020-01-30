<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('アカウント情報');
  debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
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
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    //DBの情報と違う場合にバリデーションを行う
    if($dbFormData['email'] !== $email){
      //メール形式チェック
      validEmail($email, 'email');
      //最大文字数チェック
      validMaxLen($email, 'email');
      //重複チェック
      validEmailDup($email);
    }

    if($dbFormData['pass'] !== $pass){
      //半角英数字チェック
      validHalf($pass, 'pass');
      //最大文字数チェック
      validMaxLen($pass, 'pass');
      //最小文字数チェック
      validMinLen($pass, 'pass');
    }

    if(empty($err_msg)){
      //パスワード同値チェック
      validMatch($pass, $paee_re, 'pass', 'pass_re');

      if(empty($err_msg)){
        debug('バリデーションOKです');
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET username = :u_name, comment = :comment WHERE id = :u_id';
          $data = array(':u_name' => $username, 'comment' => $comment, ':u_id' => $dbFormData['id']);
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
  $siteTitle = 'アカウント情報';
  require('head.php');
?>
  <body>
  <!--ヘッダー-->
  <?php require('header.php') ?>
    
    <!--メイン-->
    <section id="main">
      <div class="site-width">

        <h2 class="page-title">アカウント情報</h2>
        <div class="mypage">
          <div class="account-contents">
            <div class="account-wrap">
              <a href="mailEdit.php" class="account-btn">
                <p>メールアドレス</p>
                <p><?php echo $dbFormData['email']; ?><span style="margin-left: 10px;">&gt;</span></p>
              </a>
            </div>
            
            <div class="account-wrap">
              <a href="passEdit.php" class="account-btn">
                <p>パスワード</p>
                <span style="margin-left: 10px;">&gt;</span>
              </a>
            </div>

            <div class="account-wrap">
              <a href="withdraw.php" class="account-btn">
                <p>退会</p>
                <span style="margin-left: 10px;">&gt;</span>
              </a>
            </div>
          </div>  
          <?php require('sidebar.php');?>
        </div>

      </div>
    </section>
        
    <!--footer-->
    <?php require('footer.php'); ?>