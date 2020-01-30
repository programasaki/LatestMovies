<?php

  //共通関数
  require('function.php');

  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('レビュー画面');
  debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //ログイン認証
  require('auth.php');

  //================================
  // 画面処理
  //================================

  // 画面表示用データ取得
  //================================
  //GETデータを格納
  $r_id = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '';

  //DBからデータを取得
  $reviewData = getReview($_SESSION['user_id'], $r_id);
  $reviewDataOne = getReviewOne($r_id);
  $movieDataOne = getMovieOne($r_id);
  $dbFormData = getUser($_SESSION['user_id']);
  debug('取得した映画(一つ)：'.print_r($reviewMyDataOne));
  //新規登録画面か編集画面か判別
  $edit_flg = (empty($movieDataOne['id'] === $reviewDataOne['movie_id'])) ? false : true;
  debug('レビューID：'.$r_id);
  debug('フォーム用DBデータ：'.print_r($reviewData,true));
  
  //パラメータ改ざんチェック
  if(!empty($r_id) && empty($dbFormData)){
    debug('GETパラメータのレビューIDが違います。マイページへ遷移します。');
    header("Location:mypage.php"); //マイページへ
  }

  //POST送信処理
  if(!empty($_POST)){
    debug('POST送信があります。');
    debug('POST情報：'.print_r($_POST,true));

  //変数にユーザーIDを登録
    $score = $_POST['score'];
    $comment = $_POST['comment'];

  //更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($reviewData)){
    //基本バリデーション
    //最大文字数チェック
    validMaxLen($comment, 'comment', 500);
  }else{
    //DBデータとの違いを照合
    if($reviewData['comment'] !== $comment){
      //最大文字数チェック
      validMaxLen($comment, 'comment', 500);
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOK');
    //例外処理
    try{
      $dbh = dbConnect();
      //編集画面はUPDATE文、新規登録画面はINSERT文
      if($edit_flg){
        $sql = 'UPDATE review SET score = :score, comment = :comment, update_date = :date
                WHERE user_id = :u_id AND id = :r_id AND delete_flg = 0';
        $data = array(':score' => $score, ':comment' => $comment, ':date' => date('Y-m-d H:i:s'), ':u_id' => $_SESSION['user_id'], ':r_id' => $r_id);
      }else{
        $sql = 'INSERT INTO review(user_id, username, movie_id, thumbnail, movie_img, title, comment, score, create_date, update_date) VALUES(:u_id, :u_name, :m_id, :thumbnail, :m_img, :title, :comment, :score, :c_date, :u_date)';
        $data = array(':u_id' => $_SESSION['user_id'], ':u_name' => $dbFormData['username'], 'm_id' => $movieDataOne['id'], ':thumbnail' => $dbFormData['thumbnail'], ':m_img' => $movieDataOne['image'], ':title' => $movieDataOne['title'], ':score' => $score, ':comment' => $comment, ':c_date' => date('Y-m-d H:i:s'), ':u_date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：'.$sql);
      debug('流し込みデータ：'.print_r($data, true));
      $stmt = queryPost($dbh, $sql, $data);
      //クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します');
        header("Location:mypage.php"); //マイページへ
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e -> getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
?>

<?php
  $siteTitle = 'レビュー';
  require('head.php');
?>
  <body>
  <!--ヘッダー-->
  <?php require('header.php') ?>
    
    <!--メイン-->
    <section id="main">
      <div class="site-width">

        <div class="movieData">
          <h2 class="movie-title"><?php echo $movieDataOne['title']; ?></h2>
          <p class="releaseData"><?php echo $movieDataOne['release_date']. '公開'; ?></p>
        </div>
        <div class="movie-wraps">
          <div class="movie-left">
              <a href="<?php echo $movieDataOne['image']; ?>" data-lightbox="movie">
                <img src="<?php echo $movieDataOne['image']; ?>" class="review-img">
              </a>
          </div>
          <div class="movie-right review-right">
            <form action="" method="post">
              <!--スライダースコア-->
              <div class="slider-score">
                <p style="text-align: center; margin-bottom: 20px;">スコア</p>
                <div class="value" name="value">0</div>
                <input type="range" name="score" min="0" max="10" step="0.1" value="0">
              </div>
              <!--感想-->
              <label class="<?php if(!empty($err_msg['comment']) || !empty($err_msg['not-match'])) echo 'err'; ?>">
                感想(2,000文字以内)
                <div class="area-msg">
                  <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
                </div>
                <!--入力された値を固定表示する-->
                <textarea wrap="hard" id="js-count" style="width:100%;" rows="10" placeholder="感想(任意)" name="comment" value="<?php if(!empty($_POST['comment'])) echo $_POST['comment']; ?>"></textarea>
                <div class="counter review-counter">
                  <span class="show-count">0</span>&#47;2000
                </div>
              </label>
              <!--エラーメッセージ（例外処理）-->
              <div class="area-msg">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>
              <div class="submit-wrap">
                <input type="submit" value="<?php echo (!$edit_flg) ? '投稿する' : '編集する'; ?>">
              </div>
            </form>
          </div>
        </div>
        
      </div>
    </section>
        
    <!--footer-->
    <?php require('footer.php'); ?>