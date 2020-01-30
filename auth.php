<?php

  //=====================================
  //ログイン認証・自動ログアウト
  //=====================================
  //ログインしている場合
  if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');

    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです');
      //セッションを削除する（ログアウトする）
      session_destroy();
      //ログインページへ
      header("Location:login.php");
    }else{
      debug('ログイン有効期限内です');
      //最終ログイン日時を現在日時に更新
      $_SESSION['login_date'] = time();
      if(basename($_SERVER['PHP_SELF']) === 'login.php'){
        debug('マイページに遷移します');
        header("Location:mypage.php");
      }
    }
  }else{
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
      header("Location:login.php");
    }
  }