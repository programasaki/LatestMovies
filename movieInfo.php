
<div class="movieData">
  <h2 class="movie-title"><?php echo $movieDataOne['title']; ?></h2>
  <p class="releaseData"><?php echo $movieDataOne['release_date'].'公開'; ?></p>
</div>

<div class="movie-wraps">
  <div class="movie-left">
    <a href="<?php echo $movieDataOne['image']; ?>" data-lightbox="movie">
      <img src="<?php echo $movieDataOne['image']; ?>">
    </a>
  </div>

  <div class="movie-right">
    <table class="table table-borderless table-sm text-white">
      <tbody>
        <tr><td class="width: 15%;">監督</td><td style="width: 85%;"><?php echo $movieDataOne['director']; ?></td></tr>
        <tr><td>出演</td><td><?php echo $movieDataOne['actor'].' ほか'; ?></td></tr>
        <tr><td>時間</td><td><?php echo $movieDataOne['screening_time'].'分'; ?></td></tr>
        <tr><td>配給</td><td><?php echo $movieDataOne['distribution']; ?></td></tr>
        <tr><td>制作国</td><td><?php echo $movieDataOne['country']; ?></td></tr>
      </tbody>
    </table>

    <div class="movieInfo-btn-wrap">      
      <i class="btn btn-like js-click-like <?php if(isLike($_SESSION['user_id'], $movieDataOne['id'])){ echo 'active'; } ?>" style="margin-right: 10px;" aria-hidden="true" data-productid="<?php echo sanitize($movieDataOne['id']); ?>" >いいね</i>
      <a href="reviewEdit.php?r_id=<?php echo $movieDataOne['id'] ?>" class="btn btn-review"><p>レビュー</p></a>
    </div>
  </div>
</div>

<div class="explanation">
  <h3>解説</h3>
  <p><?php echo $movieDataOne['discription']; ?></p>
</div>

    