<?php
function getCards()
{
    $page = $names = "";
    $exclude = "+-e:td2+-e:me4+-e:pz2+-e:dpa+-e:cst+-e:dkm+-e:dds+-e:pd2+-e:pd3+-e:h09+-e:td0+-e:mp2";
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    if (!isset($_GET['rank'])) {
        $card = file_get_contents('https://api.scryfall.com/cards/search?q=r:common+not:online+f:pauper+'.$exclude.'&page='.$page.'');
        $results = true;
    } elseif (isset($_GET['rank'])) {
        include('dbh.php');
        if ($_GET['rank'] == "staple") {
            $stmt = $conn->prepare("SELECT * FROM card WHERE score >= 20 ");
        } elseif ($_GET['rank'] == "cubable") {
            $stmt = $conn->prepare("SELECT * FROM card WHERE score >= 10 AND score < 20");
        } elseif ($_GET['rank'] == "borderline") {
            $stmt = $conn->prepare("SELECT * FROM card WHERE score >= 5 AND score < 10");
        }
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $name = str_replace(' ', '+', $row['name']);
                $names .= '"'.$name.'"'." or ";
            }
            $names = substr($names, 0, strlen($names)-4);
            $names = rawurlencode($names);
            $card = file_get_contents('https://api.scryfall.com/cards/search?q=('.$names.')'.$exclude.'+r:common+not:online+f:pauper');
            $results = true;
        } else {
          echo '<div class="no-results">';
          echo '<h3>No results.</h3>';
          echo '</div>';
          $results = false;
        }
    }
    if ($results == true) {
    $card = json_decode($card, true);
    $more = $card['has_more'];
    if (!empty($card['data'])) {
        echo '<div class="all-cards">';


        $length = count($card['data']);
        for ($i=0; $i < $length; $i++) {
            if (isset($card['data'][$i]['multiverse_id']) && $card['data'][$i]['multiverse_id'] != "0") {
                $id = $card['data'][$i]['multiverse_id'];
                $link = 'http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid='.$id.'&type=card';
            } else {
                $id = $card['data'][$i]['collector_number'];
                $link = $card['data'][$i]['image_uri'];
            }
            $name = str_replace(' ', '+', $card['data'][$i]['name']);
            $img = '<a href="card_view.php?name='.$name.'"><img class="card-image" src="'.$link.'"></a>';
            $name = $card['data'][$i]['name'];
            echo '<div id="'.$id.'" class="preview">';
            echo '<p>'.$name.'</p>';
            echo $img;
            echo '</div>';
        }

        echo '</div>';
        $data['page'] = $page;
        $data['more'] = $more;
        $data['prev_page'] = $i;
        return $data;
    } else {
        //header("Location: index.php");
    }
  }
}
function cardView()
{
    include('dbh.php');
    $name = str_replace(' ', '+', $_GET['name']);
    $name = str_replace('\'', '', $name);
    $exclude = '+-e:td2+-e:me4+-e:pz2+-e:dpa+-e:cst+-e:dkm+-e:dds+-e:pd2+-e:pd3+-e:h09+-e:td0+-e:mp2';
    $terms = 'r:common+not:online+';
        if ($card = json_decode(@file_get_contents("https://api.scryfall.com/cards/search?q=r:common+not:online+f:pauper+!'".$name."'".$exclude.""), true)) {

        $name = $card['data'][0]['name'];
        $stmt = $conn->prepare("SELECT * FROM card WHERE name = :name");
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        $rank = "Unknown";
        if ($row = $stmt->fetch()) {
            if ($row['score'] >= 20) {
                $rank = "Staple";
            } elseif ($row['score'] >= 10 && $row['score'] < 20) {
                $rank = "Cubable";
            } elseif ($row['score'] >= 5 && $row['score'] < 10) {
                $rank = "Borderline";
            }
        }

        $id = $card['data'][0]['multiverse_id'];
        //$link = 'http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid='.$id.'&type=card';
        $link = $card['data'][0]['image_uri'];
        $img = '<img src="'.$link.'">';
        $color = "";
        if (isset($card['data'][0]['colors'][0])) {
            $color = $card['data'][0]['colors'][0];
        }
        if (isset($card['data'][0]['colors'][1])) {
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
            echo 'document.getElementsByClassName("bg")[0].style="background-image:url(\'artifact.png\')";
</script>';
        }
        echo '<div class="card-frame">';
        echo $img;
        echo '</div>';
        echo '<div class="space"></div>';
        echo '<div class="info">
        <button type="submit" class="vote" name="'.$id.'"><i class="material-icons">thumb_up</i></button>
        <input type="button" class="rank '.$rank.'" value="'.$rank.'" disabled>
        </div>';
        echo '<div class="oracle">';
        /*echo('<pre style="font-size:15px;">');
        print_r($card['data'][0]);
        echo('</pre>');*/
        echo '<h3 class="card-name">'.$card['data'][0]['name']."</h3>";
        echo '<p class="card-type"><i>'.$card['data'][0]['type_line']."</i></p>";
        if (isset($card['data'][0]['oracle_text'])) {
            $oracle_text = nl2br($card['data'][0]['oracle_text']);
            echo '<h5 class="oracle-text">'.$oracle_text.'</h5>';
        }
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
    $id = $_POST['id'];
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO card (name, multiverse_id, score) VALUES (:name, :id, score + 1)
  ON DUPLICATE KEY UPDATE name = :name, multiverse_id = :id, score = score + 1 ");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    echo "Name: ".$name."\n";
    echo "Multiverse ID: ".$id;
}
if (isset($_POST['voteCard'])) {
    voteCard();
}
