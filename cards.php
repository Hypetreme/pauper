<?
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
include('nav.php')
?>

<html>
<div class="container">
  <div class="nav-up">

  </div>
<?
$data = getCards();
?>
<div class="nav-bottom">
<?if ($data['page'] != 1 && $data['prev_page'] >= 100 && $data['more'] == true) {
    echo'<input type="submit" value="Previous Page" class="page-button" onclick="location.href=\'cards.php?page='.($data['page']-1).'\'">';
    echo '<script>
document.getElementsByClassName("nav-up")[0].innerHTML = "<input type=\'submit\' value=\'Previous Page\' class=\'page-button\' onclick=\'location.href=\"cards.php?page='.($data['page']-1).'\"\'>"
</script>';
}
if ($data['more'] == true) {
    echo'<input type="submit" value="Next Page" class="page-button" onclick="location.href=\'cards.php?page='.($data['page']+1).'\'">';
    echo '<script>
document.getElementsByClassName("nav-up")[0].innerHTML += "<input type=\'submit\' value=\'Next Page\' class=\'page-button\' onclick=\'location.href=\"cards.php?page='.($data['page']+1).'\"\'>"
</script>';
}?>
</div>
</div>
</html>
<?php
include('footer.php');
 ?>
