<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('映画情報');
  debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //================================
  // 画面処理
  //================================
  //映画IDのGETパラメータを取得
  $m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
  $movieDataOne = getMovieOne($m_id);
  $reviewDataOne = getReviewOne($m_id);
  $reviewData = getMyReview($S_SESSION['user_id']);
  $dbFormData = getUser($_SESSION['user_id']);
  debug('取得した映画：'.print_r($movieDataOne,true));


  // 画面表示用データ取得
  //================================
  // カレントページのGETパラメータを取得
  $currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページ目
  // パラメータに不正な値が入っているかチェック
  if(!is_int((int)$currentPageNum)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:index.php"); //トップページへ
  }
  // 表示件数
  $listSpan = 5;
  // 現在の表示レコード先頭を算出
  $currentMinNum = (($currentPageNum-1)*$listSpan); //1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
  // DBからレビューを取得
  $dbProductData = getReviewList($m_id, $currentMinNum);
  debug('現在のページ：'.$currentPageNum);
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = $movieDataOne['title'];
  require('head.php');
?>
  <body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>
    <!--メイン-->
    <section id="main">
      <div class="site-width">

        <?php require('movieInfo.php'); ?>
        <div class="review-top-wrap" id="review-top">
        <h2 class="txtl">レビュー</h2>
          <div class="pagenation-title">
            <div class="pagenation-left">
              <span class="total-num"><?php echo sanitize($dbProductData['total']); ?></span>件のレビューが見つかりました
            </div>
            <div class="pagenation-right">
              <span class="num"><?php echo $currentMinNum+1; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbProductData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbProductData['total']); ?></span>件中
            </div>
          </div>
          <div class="panel-wrap">
            <?php foreach($dbProductData['data'] as $key => $val): ?>
            <div class="panel review-panel">
                <div class="panel-body review-body">
                  <div class="panel-top">
                    <div class="panel-top-left">
                      <img class="panel-thumbnail" src="<?php echo sanitize($val['thumbnail']); ?>">
                    </div>
                    <div class="panel-top-right">
                      <p class="panel-title"><?php echo sanitize($val['username']); ?></p>
                      <p class="panel-date"><?php echo sanitize($val['create_date']); ?></p>
                    </div>  
                  </div>
                  <p class="panel-score" style="margin: 15px 0 15px 60px;">&#11088;<?php echo sanitize($val['score']); ?><span style="font-size:12px; margin-left:3px;">/10</span></p>
                  <p class="panel-comment"><?php echo sanitize($val['comment']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
          </div>
          <!--ページネーション-->
          <?php pagination($currentPageNum, $dbProductData['total_page']); ?>
        </div>

      </div>
    </section>

    <!--フッター-->
    <?php require('footer.php'); ?>