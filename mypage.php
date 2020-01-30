<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('マイページ');
  debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //ログイン認証
  require('auth.php');

  // DBからユーザーデータを取得
  $u_id = $_SESSION['user_id'];
  $reviewData = getMyReview($u_id);
  $likeData = getMyLike($u_id);
  $dbFormData = getUser($_SESSION['user_id']);
  debug('取得したユーザー：'.print_r($dbFormData,true));

  //ログイン認証
  require('auth.php');

  if(!empty($_GET['r_id'])){
    try {
      $dbh = dbConnect();
      $sql = 'INSERT INTO bord (sale_user, buy_user, movie_id, create_date) VALUES (:s_uid, :b_uid, :m_id, :date)';
      $data = array(':s_uid' => $viewData['user_id'], ':b_uid' => $_SESSION['user_id'], ':m_id' => $m_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = 'マイページ';
  require('head.php');
?>
  <body> 
    <!--ヘッダー-->
    <?php require("header.php"); ?>
    <!--メイン-->
    <section id="mypage">
      <div class="site-width">

        <!--プロフィール-->
        <div class="mypage">
          <div class="profile-wrap">
            <div class="profile-left">
              <img src="<?php echo showImg($dbFormData['thumbnail']); ?>">
            </div>
            <div class="profile-right"> 
              <p class="profile-name"><?php echo $dbFormData['username']; ?></p>
              <p class="profile-text"><?php echo $dbFormData['comment']; ?></p>
            </div>
          </div>
          <?php require('sidebar.php'); ?>
        </div>
          
        <!--タブ-->
        <div class="mypage-tab">
          <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
              <a href="#review" class="nav-link active" data-toggle="tab"><i class="fas fa-comment"></i>レビュー</a>
            </li>
            <li class="nav-item">
              <a href="#clip" class="nav-link" data-toggle="tab"><i class="fas fa-clipboard-list"></i>いいね</a>
            </li>
          </ul>
          
          <!--レビュー-->
          <div class="tab-content p-4">
            <div id="review" class="tab-pane active">
              <?php foreach($reviewData as $key => $val): ?>
                <div class="panel review-panel">
                    <div class="panel-body">
                      <div class="panel-top">
                        <div class="panel-top-left">
                          <a href="movie.php?m_id=<?php echo $val['id']; ?>">
                            <img class="panel-movie-img" src="<?php echo showImg(sanitize($val['movie_img'])); ?>">
                          </a>
                        </div>
                        <div class="panel-top-right">
                          <p class="panel-name"><?php echo sanitize($val['title']); ?></p>
                          <p class="panel-date"><?php echo sanitize($val['update_date']); ?></p>
                          <p class="panel-score" style="margin-top: 24px;">&#11088;<?php echo sanitize($val['score']); ?><span style="font-size:12px; margin-left:3px;">/10</span></p>
                        </div>  
                      </div>
                      <p class="panel-comment"><?php echo sanitize($val['comment']); ?></p>
                      <div class="text-wrap">
                        <a href="reviewEdit.php?r_id=<?php echo $val['id'] ?>" class="text-review">編集</span>
                        <a href="" id="js-" class="text-review delete">削除</a>
                      </div>
                    </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!--お気に入り-->
            <div id="clip" class="tab-pane">
              <div class="panel-wrap">
                <?php foreach($likeData as $key => $val): ?>
                  <div class="panel">
                    <a href="movie.php?m_id=<?php echo $val['id']; ?>">
                      <img class="panel-img" src="<?php echo sanitize($val['image']); ?>">
                      
                      <div class="panel-body-wrap">
                        <div class="panel-body">
                          <i class="fas fa-star"></i>
                          <span class="score"></span>
                        </div>
                        <div class="panel-body">
                          <i class="fas fa-heart"></i>
                          <span class="score"></span>
                        </div>
                        <div class="panel-body">
                          <i class="fas fa-comment"></i>
                          <span class="score"></span>
                        </div>
                      </div>
                      
                      <p><?php echo sanitize($val['title']); ?></p>
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>  
            </div>

          </div>
        </div>
      </div>
    </section>
    
    <!--footer-->
    <?php require('footer.php'); ?>