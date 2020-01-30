<header>
  <div class="header-width">
    <h1><a href="index.php"><span class="blue">L</span>atest&nbsp;<span class="blue">M</span>ovies</a></h1>
    <nav id="top-nav">
      <ul class="nav-list">
        <a href="mypage.php"><li class="nav-contents">マイページ</li></a>
        <?php if(empty($_SESSION['user_id'])){ ?>
          <a href="signup.php"><li class="nav-contents">ユーザー登録</li></a>
        <?php }else{ ?>
          <a href="signout.php"><li class="nav-contents">ログアウト</li></a>
        <?php } ?>
      </ul>
    </nav>
  </div>
</header>