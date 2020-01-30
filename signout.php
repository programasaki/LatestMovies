<?php

  //共通関数
  require('function.php');

  //デバッグ
  debug('「「「「「「「「「「「「「「「「「「「「「「');
  debug('「ログアウトページ」');
  debug('」」」」」」」」」」」」」」」」」」」」」」');
  debugLogStart();
  debug('ログアウトします');

  //セッションを削除
  session_destroy();
  debug('ログインページに遷移します');
  
  header('Location:login.php');