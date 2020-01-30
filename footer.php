    <footer id="footer">
      <small>Copyright © 2019 LatestMovies. All Rights Reserved.</small>
    </footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!--メインJS-->
    <script src="js/main.js"></script>
    <!--Lightbox(画像ズームアップ)-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/lightbox.min.js"></script>

    <script>
      $(function(){

        // フッターを最下部に固定
        var $ftr = $('#footer');
        if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
          $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
        }

        var $like,
        likeProductId;
        $like = $('.js-click-like') || null; //nullというのはnull値という値で、「変数の中身は空ですよ」と明示するためにつかう値
        likeProductId = $like.data('productid') || null;
        // 数値の0はfalseと判定されてしまう。product_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
        if(likeProductId !== undefined && likeProductId !== null){
          $like.on('click',function(){
            var $this = $(this);
            $.ajax({
              type: "POST",
              url: "ajaxLike.php",
              data: { productId : likeProductId}
            }).done(function( data ){
              console.log('Ajax Success');
              // クラス属性をtoggleでつけ外しする
              $this.toggleClass('active');
            }).fail(function( msg ) {
              console.log('Ajax Error');
            });
          });
        }

        //画像ライブプレビュー
        var $dropArea = $('.area-drop');
        var $fileInput = $('.input-file');
        $dropArea.on('dragover', function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border', '3px #ccc dashed');
        });
        $dropArea.on('dragleave', function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border', 'none');
        });
        $fileInput.on('change', function(e){
          $dropArea.css('border', 'none');
          var file = this.files[0], //files配列にファイルが入っています
              $img = $(this).siblings('.prev-img'), //jQueryのsiblingsメソッドで兄弟のimgを取得
              fileReader = new FileReader(); //ファイルを読み込むFileReaderオブジェクト

          //読み込み完了した際のイベントハンドラ。imgのsrcにデータをセット
          fileReader.onload = function(event){
            //読み込んだデータをimgに設定
            $img.attr('src', event.target.result).show();
          };

          //画像読み込み
          fileReader.readAsDataURL(file);
        });

        //スライダースコア
        var elem = document.querySelector('input[type="range"]');
        var rangeValue = function () {
          var newValue = elem.value;
          var target = document.querySelector('.value');
          target.innerHTML = newValue;
        }
        elem.addEventListener("input", rangeValue);
        
      });
    </script>

  </body>
</html>