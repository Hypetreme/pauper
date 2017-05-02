<?
include('functions.php');
include('header.php');
include('nav.php');
?>

<html>
<div class="bg"></div>
<div class="container">
<div class="preview-single">
<?
cardView();

?>
</div>
</div>
</html>
<?php
include('footer.php');
 ?>
<script src="replace.js"></script> 
<script>
$('.vote').click(function(event){
    event.preventDefault();
    var multiverseid = $(".vote").attr("name");
    var finish = $.post("functions.php", { voteCard: 'vote', id: multiverseid, name: cardname},
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
