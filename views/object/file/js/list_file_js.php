<script>
    $(function(){  
        $('#carousel-attachment').slick({
  dots: true,
  infinite: true,
  speed: 500,
  fade: true,
  cssEase: 'linear'
          });
    });
    //mostra o modal de slideshow
    function showSlideShow(){
        $("#modalSlideShow").modal('show');
    }
    
</script>
