<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('プロフィール編集');
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

  //POST送信されていた場合
  if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));
    debug('FILE情報：'.print_r($_FILES,true));

    //画像をアップロードし、パスを格納
    $thumbnail = (!empty($_FILES['thumbnail']['name'])) ? uploadImg($_FILES['thumbnail'], 'thumbnail') : '';
    //（画像）画像をPOSTしてない（登録していない）が、すでにDB登録されている場合、DBのパスを入れる
    $thumbnail = (empty($thumbnail) && !empty($dbFormData['thumbnail'])) ? $dbFormData['thumbnail'] : $thumbnail;
    $username = $_POST['username'];
    $comment = $_POST['comment'];

    //未入力チェック
    validRequired($username, 'username');

    //DBの情報と違う場合にバリデーションを行う
    if($dbFormData['username'] !== $username){
      //最大文字数入力チェック（15文字以下）
      valid15Len($username, 'username');
    }
    if($dbFormData['comment'] !== $comment){
      valid160Len($comment, 'comment');
    }

    if(empty($err_msg)){
      debug('バリデーションOKです');

      //例外処理
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET username = :u_name, comment = :comment, thumbnail = :thumbnail WHERE id = :u_id';
        $data = array(':u_name' => $username, ':comment' => $comment, ':thumbnail' => $thumbnail, ':u_id' => $dbFormData['id']);

        //クエリ実行
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
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = 'プロフィール編集';
  require('head.php');
?>
  <body>
    <?php
      require('header.php')
    ?>
    
    <!--メイン-->
    <section id="main">
      <div class="container">
        <div class="row">

          <div class="site-width">

            <h2 class="page-title">プロフィール編集</h2>
            
            <form action="" method="post" enctype="multipart/form-data">
              <!--エラーメッセージ（例外処理）-->
              <div class="area-msg">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>

              <div class="mypage">
                <div class="profile-wrap">

                  <div class="profile-left">
                    <div class="imgDrop">
                      プロフィール画像
                      <label class="area-drop <?php if(!empty($err_msg['thumbnail'])) echo 'err'; ?>">
                        <div class="area-msg">
                          <?php if(!empty($err_msg['thumbnail'])) echo $err_msg['thumbnail']; ?>
                        </div>
                        <i class="fas fa-plus"></i>
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="thumbnail" class="input-file">
                        <img src="<?php echo getFormData('thumbnail'); ?>" class="prev-img" style="<?php 
                        if(empty(getFormData('thumbnail'))) echo 'display: none;' ?>">
                      </label>
                    </div>
                  </div>

                  <div class="profile-right">
                    <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
                      ユーザー名<small class="red">※必須</small>
                      <div class="area-msg">
                        <?php if(!empty($err_msg['username'])) echo $err_msg['username']; ?>
                      </div>
                      <input type="text" name="username" placeholder="ユーザー名" value="<?php echo getFormData('username'); ?>">
                    </label>
                    
                    <div class="counter">
                      <span class="show-count">0</span>&#47;160
                    </div>

                    <div class="profile-text">
                      <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
                        プロフィール(160文字以内)
                        <div class="area-msg">
                          <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
                        </div>
                        <textarea id="js-count" style="width:100%;" rows="5" placeholder="プロフィール" name="comment"><?php echo getFormData('comment'); ?></textarea>
                      </label>
                    </div>

                    <div class="submit-wrap">
                      <input type="submit" name="submit" value="編集する">
                    </div>
                  </div>
                </div>
                <?php
                  require('sidebar.php');
                ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
        
    <!--footer-->
    <?php
      require('footer.php');
    ?>