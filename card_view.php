<?php
include('functions.php');
include('header.php');
?>

<html>
<?php include('nav.php');
?>
<div class="bg"></div>
<div class="container">
<div class="preview-single">
<?php
cardView();

?>
</div>
</div>
<?php
include('footer.php');
 ?>
</html>
<script>
$('.vote').click(function(event){
    event.preventDefault();
    var multiverseid = $(".vote").attr("name");
    var score = $(this).attr("id");
    var finish = $.post("functions.php", { voteCard: 'vote', id: multiverseid, name: cardname,
    votedscore: score},
    function(data) {
      if(data){
        console.log(data);
      }

    });
});

$( ".nav-mobile" ).click(function(event) {
  event.stopPropagation();
  $( ".nav-mobile" ).toggleClass('open');
  if ($( ".nav-mobile" ).hasClass('open')) {
  $( ".nav-first" ).css('visibility', 'visible');
  $( ".nav-first" ).slideDown();
} else {
  $( ".nav-first" ).css('visibility', 'visible');
  $( ".nav-first" ).slideUp();
}
});
</script>
