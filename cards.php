<?php
if ($_GET['page'] < 1 && !isset($_GET['rank'])) {
header("Location: cards.php?page=1");
}
if ($_GET['page'] < 1 && !empty($_GET['rank'])) {
header("Location: cards.php?rank=staple&page=1");
}
if (isset($_GET['rank']) && empty($_GET['rank'])) {
header("Location: cards.php?page=1");
}
include('functions.php');
include('header.php');
?>
<html>
<?php include('nav.php');
?>
<div class="container">
  <div class="nav-up"></div>
<?php
$data = getCards();
?>
<div class="nav-bottom">
<?php if ($data['page'] != 1 && isset($data['page'])) {
    echo'<input type="submit" value="Previous" class="page-button" onclick="location.href=\'cards.php?page='.($data['page']-1).'\'">';
    echo '<script>
document.getElementsByClassName("nav-up")[0].innerHTML = "<input type=\'submit\' value=\'Previous\' class=\'page-button\' onclick=\'location.href=\"cards.php?page='.($data['page']-1).'\"\'>"
</script>';
}
if ($data['more'] == true) {
    echo'<input type="submit" value="Next" class="page-button" onclick="location.href=\'cards.php?page='.($data['page']+1).'\'">';
    echo '<script>
document.getElementsByClassName("nav-up")[0].innerHTML += "<input type=\'submit\' value=\'Next\' class=\'page-button\' onclick=\'location.href=\"cards.php?page='.($data['page']+1).'\"\'>"
</script>';
}?>
</div>
</div>
<?php
include('footer.php');
 ?>
</html>
<script>
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
