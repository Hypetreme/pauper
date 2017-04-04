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
</script>
