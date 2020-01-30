<?php

  //共通関数
  require("function.php");

  //POST送信されていた場合
  if(!empty($_POST)){
  
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){

      //メール形式チェック
      validEmail($email, 'email');
      //最大文字数チェック
      validMaxLen($email, 'email');
      //重複チェック
      validEmailDup($email);

      //パスワード(再入力のバリデーションは37行目でパスワード一致チェックをしているので省略)
      //半角英数字チェック
      validHalf($pass, 'pass');
      //最大文字数チェック
      validMaxLen($pass, 'pass');
      //最小文字数チェック
      validMinLen($pass, 'pass');

      if(empty($err_msg)){
        //パスワード同値チェック
        validMatch($pass, $pass_re, 'pass', 'pass_re');
        if(empty($err_msg)){
          try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO users(email, pass, login_time, cleate_date)
            VALUES(:email, :pass, :login_time, :cleate_date)';
            $data = array(':email' => $email,
                          ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                          ':login_time' => date('Y:m:d H:i:s'),
                          ':cleate_date' => date('Y:m:d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
            //クエリ成功の場合
            if($stmt){
              $sesLimit = 60*60;
              $_SESSION['login_date'] = time();
              $_SESSION['login_limit'] = $sesLimit;
              $_SESSION['user_id'] = $dbh -> lastInsertId();

              debug('セッション変数の中身：'.print_r($_SESSION,true));
              header("Location:mypage.php");
            }else{
              //クエリ失敗の場合
              error_log('クエリに失敗しました');
              $err_msg['common'] = MSG08;
            }
          } catch(Exception $e) {
            error_log('エラー発生：'. $e -> getMessage());
            $err_msg['common'] = MSG08;
          }
        }  
      }
    }
  }
?>

<?php
  $siteTitle  ='ユーザー登録';
  require('head.php');
?>
  <body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>
    <!--メイン-->
    <section id="main">
      <div class="site-width">
        <form method="post" class="form">

          <!--エラーメッセージ（例外処理）-->
          <div class="area-msg common">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>

          <!--メールアドレス-->
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            メールアドレス
            <div class="area-msg">
              <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>

          <!--パスワード-->
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            パスワード
            <div class="area-msg">
              <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
            </div>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>

          <!--パスワード再入力-->
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            <input type="password" name="pass_re" placeholder="パスワード（再入力）" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>

          <div class="submit-wrap-login">
            <a href="login.php">ログインはこちら</a>
            <input type="submit" value="登録する">
          </div>
        </form>
      </div>
    </section>
    
    <!--footer-->
    <?php require("footer.php"); ?>