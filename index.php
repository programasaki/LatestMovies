<?php
  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('トップページ');
  debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();

  //================================
  // 画面処理
  //================================
  //DBから映画データを取得
  $movieData = getMovie($_SESSION['user_id']);
  $movieData2 = getMovie2($_SESSION['user_id']);
  debug('取得したDBデータ：'.print_r($movieData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
  $siteTitle = "ホーム";
  require('head.php');
?>
  <body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>

    <!--Main-->
    <section id="main">
      <div class="site-width">

        <h2 class="page-title">&ndash;NOW&nbsp;SHOWING&ndash;</a></h2>
        <div class="panel-wrap">
        <?php
          if(!empty($movieData)){
            foreach($movieData as $key => $val){
        ?>     
          <a href="movie.php?m_id=<?php echo sanitize($val['id']); ?>"class="panel">
            <img class="panel-img" src="<?php echo sanitize($val['image']); ?>">
            <!--アイコングループ
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
            -->
            <p><?php echo sanitize($val['title']); ?></p>
          </a>
        <?php 
            }
          }
        ?>

        <h2 class="page-title" style="margin-top: 25px;">&ndash;COMMING&nbsp;SOON&ndash;</a></h2>
        <div class="panel-wrap">
        <?php
          if(!empty($movieData2)){
            foreach($movieData2 as $key => $val){
        ?>
          <a href="movie.php?m_id=<?php echo sanitize($val['id']); ?>"class="panel">
            <img class="panel-img" src="<?php echo sanitize($val['image']); ?>">
            <!--アイコングループ
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
            -->
            <p><?php echo sanitize($val['title']); ?></p>
          </a>
        <?php 
            }
          }
        ?>

      </div>
    </section>

    <!--footer-->
    <?php require('footer.php'); ?>