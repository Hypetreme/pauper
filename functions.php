<?php
function getCards()
{
    $page = $names = "";
    $param = '+r:common+not:digital';
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    if (!isset($_GET['rank']) && $card = file_get_contents('https://api.scryfall.com/cards/search?q='.$param.'&page='.$page.'')) {
        $results = true;
    } elseif (isset($_GET['rank'])) {
        include('dbh.php');
        if ($_GET['rank'] == "staple") {
            $stmt = $conn->prepare("SELECT * FROM card WHERE score >= 3 ");
        } elseif ($_GET['rank'] == "cubable") {
            $stmt = $conn->prepare("SELECT * FROM card WHERE score = 2");
        } elseif ($_GET['rank'] == "borderline") {
            $stmt = $conn->prepare("SELECT * FROM card WHERE score = 1");
        }
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $name = $row['name'];
                $names .= '"'.$name.'"'." or ";
            }
            $names = substr($names, 0, strlen($names)-4);
            $names = rawurlencode($names);
            $card = file_get_contents('https://api.scryfall.com/cards/search?q=!'.$names.''.$param.'');
            $results = true;
        } else {
          echo '<div class="no-results">';
          echo '<h3>No results.</h3>';
          echo '</div>';
        }
    }
    $card = json_decode($card, true);
    if ($results == true && !empty($card['data'])) {
      $more = $card['has_more'];
        echo '<div class="all-cards">';
        $length = count($card['data']);
        for ($i=0; $i < $length; $i++) {
            /*if (isset($card['data'][$i]['multiverse_id']) && $card['data'][$i]['multiverse_id'] != "0") {
                $id = $card['data'][$i]['multiverse_id'];
                $link = 'http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid='.$id.'&type=card';
            } else {*/
                $id = $card['data'][$i]['id'];
              if (!isset($card['data'][$i]['card_faces'])) {
                $link = $card['data'][$i]['image_uris']['normal'];
              } else {
                $link = $card['data'][$i]['card_faces'][0]['image_uris']['normal'];
              }
            //}
            $name = str_replace(' ', '+', $card['data'][$i]['name']);
            $img = '<a href="card_view.php?name='.$name.'"><img class="card-image" src="'.$link.'"></a>';
            $name = $card['data'][$i]['name'];
            echo '<div id="'.$id.'" class="preview">';
            //echo '<p>'.$name.'</p>';
            echo $img;
            echo '</div>';
        }

        echo '</div>';
        $data['page'] = $page;
        $data['more'] = $more;
        $data['prev_page'] = $i;
        return $data;
    } else {
        echo '<div class="no-results">';
        echo '<h3>Failed to fetch cards.</h3>';
        echo '</div>';
        //header("Location: index.php");
    }
}
function cardView()
{
    include('dbh.php');
    if (isset($_GET['name'])) {
        $name = str_replace(' ', '+', $_GET['name']);
      }
        $param = 'r:common+not:digital+not:token+-layout:token+-layout:double_faced_token+-layout:emblem+-layout:scheme+-layout:planar+';
        if (isset($_GET['name']) && $card = json_decode(@file_get_contents("https://api.scryfall.com/cards/search?q=".$param."name:/^".$name."/"), true)) {
        $name = $card['data'][0]['name'];
        $stmt = $conn->prepare("SELECT * FROM card WHERE name = :name");
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        $rank = "No rating";
        if ($row = $stmt->fetch()) {
            if ($row['score'] >= 3) {
                $rank = "Staple";
            } elseif ($row['score'] == 2) {
                $rank = "Cubable";
            } elseif ($row['score'] == 1) {
                $rank = "Borderline";
            }
        }
        if (!isset($card['data'][0]['card_faces'])) {
        $facea = $card['data'][0];
      } else {
        $facea = $card['data'][0]['card_faces'][0];
        $faceb = $card['data'][0]['card_faces'][1];
      }

        $id = "";
        //$link = 'http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid='.$id.'&type=card';

        $link = $facea['image_uris']['normal'];

        $img = '<img src="'.$link.'">';
        $color = "";
        if (isset($facea['colors'][0])) {
            $color = $facea['colors'][0];
        }
        if (isset($facea['colors'][1])) {
            $multi_color = "";
        }

        if (!isset($multi_color) && $color == 'R') {
            echo '<script>
    var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'red.png\')";
    </script>';
        } elseif (!isset($multi_color) && $color == 'U') {
            echo '<script>
          var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'blue.png\')";
  </script>';
        } elseif (!isset($multi_color) && $color == 'G') {
            echo '<script>
          var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'green.png\')";
  </script>';
        } elseif (!isset($multi_color) && $color == 'W') {
            echo '<script>
          var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'white.png\')";
  </script>';
        } elseif (!isset($multi_color) && $color == 'B') {
            echo '<script>
          var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'black.png\')";
  </script>';
        } elseif (isset($multi_color)) {
            echo '<script>
          var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'multicolor.png\')";
  </script>';
        } else {
            echo '<script>
          var cardname = "'.$name.'";';
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'colorless.png\')";
</script>';
        }
        echo '<div class="card-frame">';
        echo $img;
        echo '</div>';
        echo '<div class="space"></div>';
        echo '<div class="info">
        <button type="submit" id="1" class="vote" name="'.$id.'">B</button>
        <button type="submit" id="2" class="vote" name="'.$id.'">C</button>
        <button type="submit" id="3" class="vote" name="'.$id.'">S</button>
        <!--<button type="submit" class="vote" name="'.$id.'"><i class="material-icons">thumb_up</i></button>-->
        <input type="button" class="rank '.$rank.'" value="'.$rank.'" disabled>
        </div>';
        echo '<div class="oracle">';
        /*echo('<pre style="font-size:15px;">');
        print_r($card['data'][0]);
        echo('</pre>');*/
        echo '<h3 class="card-name">'.$facea['name']."</h3>";
        echo '<p class="card-type"><i>'.$facea['type_line']."</i></p>";

        if (isset($facea['oracle_text'])) {
            $oracle_text = nl2br($facea['oracle_text']);
            echo '<h5 class="oracle-text">'.$oracle_text.'</h5>';
        }
        if (isset($faceb)) {
        echo '<h3 class="card-name">'.$faceb['name']."</h3>";
        echo '<p class="card-type"><i>'.$faceb['type_line']."</i></p>";

        if (isset($faceb['oracle_text'])) {
            $oracle_text = nl2br($faceb['oracle_text']);
            echo '<h5 class="oracle-text">'.$oracle_text.'</h5>';
        } }
        echo '</div>';
    } else {
        echo '<div class="no-results">';
        echo '<h3>No results.</h3>';
        echo '</div>';
    }
}
function voteCard()
{
    include('dbh.php');
    $name = $_POST['name'];
    $votedscore = $_POST['votedscore'];
    $stmt = $conn->prepare("INSERT INTO card (name, score) VALUES (:name, :votedscore)
      ON DUPLICATE KEY UPDATE name = :name, score = :votedscore ");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":votedscore", $votedscore);
    $stmt->execute();
    echo "Name: ".$name."\n";
    echo "Given score: ".$votedscore;
}
if (isset($_POST['voteCard'])) {
    voteCard();
}
