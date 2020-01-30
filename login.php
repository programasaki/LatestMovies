<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「');
  debug('「ログインページ」');
  debug('」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //ログイン認証
  require('auth.php');

  //================================
  //ログイン画面処理
  //================================
  if(!empty($_POST)){
    debug('POST送信があります');

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    
    if(empty($err_msg)){
      //メールアドレス
      //メール形式チェック
      validEmail($email, 'email');
      //最大文字数チェック
      validMaxLen($email, 'email');

      //パスワード
      //半角英数字チェック
      validHalf($pass, 'pass');
      //最大文字数チェック
      validMaxLen($pass, 'pass');
      //最小文字数チェック
      validMinLen($pass, 'pass');
      
      if(empty($err_msg)){
        debug('バリデーションOKです');
        try{
          $dbh = dbConnect();
          $sql = 'SELECT pass, id FROM users WHERE email = :email AND delete_flg = 0';
          $data = array(':email' => $email);
          $stmt = queryPost($dbh, $sql, $data);
          //クエリ結果の値を取得
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          debug('クエリ結果の中身：'.print_r($result, true));
          //パスワードの値を取得
          if(!empty($result) && password_verify($pass, array_shift($result))){
            debug('パスワードがマッチしました');
            //ログイン有効期限（デフォルトを1時間とする）
            $sesLimit = 60*60;
            //最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time(); 
            if($pass_save){
              debug('ログイン保持にチェックがあります');
              //ログイン有効期限を30日にセット
              $_SESSION['login_limit'] = $sesLimit * 24 * 30;
            }else{
              debug('ログイン保持にチェックがありません');
              //ログイン保持しないので、有効期限をデフォルトの1時間でセット
              $_SESSION['login_limit'] = $sesLimit;
            }
            //ユーザーIDを格納
            $_SESSION['user_id'] = $result['id'];
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('マイページへ遷移します');
            header("Location:mypage.php");
          }else{
            debug('パスワードがアンマッチです');
            $err_msg['common'] = MSG09;
          }
        }catch(Exception $e){
          error_log('エラー発生：'.$e -> getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
  }
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = 'ログイン';
  require('head.php');
?>
  <body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>

    <!--メイン-->
    <section id="main">
      <div class="site-width">

        <form method="post" class="form">
          <div class="area-msg common">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>

          <label class="<?php if(!empty($err_msg['email']) || !empty($err_msg['common'])) echo 'err'; ?>">
            メールアドレス
            <div class="area-msg">
              <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" >
          </label>

          <label class="<?php if(!empty($err_msg['pass']) || !empty($err_msg['common'])) echo 'err'; ?>">
            パスワード
            <div class="area-msg">
              <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
            </div>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>" >
          </label>
          
          <div class="submit-wrap-login">
            <label>
              <input type="checkbox" name="pass_save">次回ログインを省略する
            </label>
            <input type="submit" value="ログイン">
          </div> 
        </form>

      </div>
    </section>
    
    <!--footer-->
    <?php require('footer.php'); ?>