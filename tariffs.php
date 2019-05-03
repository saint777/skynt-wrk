<?php
//error_reporting(1);
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <title>Тарифные планы</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
  </head>
  <body>

  <main class="tariffs-container">
  <?php

    //$json = file_get_contents(dirname(__FILE__) . "/1.json");
    $json = file_get_contents("https://www.sknt.ru/job/frontend/data.json");
    $data = html_entity_decode($json);
    $data = json_decode(html_entity_decode($json), true);
    $ids = 1;
    $ids_v2 = 1;

    if($data["result"] == "ok"){
      echo "<ul class=\"level-1\">"; // wrapper open
      foreach($data["tarifs"] as $tarifs){ // LEVEL-1
        echo "<li class=\"tariff-block\">"; // tariff-block open
        echo "<h2 class=\"tariff-title\">" . $tarifs["title"] . "</h2>"; // tariff-title
        $sknt_link = $tarifs["link"];
        $minmax_price = array();
        asort($tarifs["tarifs"]); // Fix JSON - самая дешевая земля не на первом месте
        $options = count($tarifs["free_options"]);
        $most_expensive = 1;
        echo "<input type=\"checkbox\"  class=\"lv2-check\" id=\"choose-tariff-" . $ids . "\" style=\"display: none;\"></input>";
        echo "<ul class=\"level-2\">"; // LEVEL-2 open
              echo "<label for=\"choose-tariff-" . $ids . "\" class=\"move-back\">Тариф \"" . $tarifs["title"] . "\"</label>";
              foreach($tarifs["tarifs"] as $v){
                echo "<li class=\"tariff-block\">";
                    echo "<h2 class=\"tariff-title\">" . $v["title"] . "</h2>";
                    $price_per_month = ((int)$v["price"] / (int)$v["pay_period"]);
                    echo "<label for=\"choose-final-" . $ids_v2 . "\" class=\"tariff\">";
                        echo "<span class=\"price-range\">" . $price_per_month . " Р/мес.</span>";
                        $price = (int)$v["price"];
                        $pay_period = (int)$v["pay_period"];
                        $pay_day = DateTime::createFromFormat("UO", (String)$v["new_payday"]);
                        $minmax_price[] = $price_per_month; // Каждый раз добавляем цену за месяц в массив
                        echo "<span class=\"bill-info\">Разовый платеж — " . $price . " Р</span>";
                        if($pay_period == 1){
                          $most_expensive = $price_per_month;
                        }
                        else if($pay_period > 1){
                          echo "<span class=\"bill-info\">Скидка — " . (($pay_period * $most_expensive) - $price) . " Р";
                        }
                    echo "</label>";
                    echo "<input type=\"checkbox\" class=\"lv3-check\" id=\"choose-final-" . $ids_v2 . "\" style=\"display: none;\">";

                    echo "<ul class=\"level-3\">";
                      echo "<label for=\"choose-final-" . $ids_v2 . "\" class=\"move-back\">Выбор тарифа</label>";
                      echo "<li class=\"tariff-block\">";
                        echo "<h2 class=\"tariff-title\">Тариф — " . $v["title"] . "</h2>";
                        echo "<div href=\"#\" class=\"tariff\">";

                          $tmp_month_spell = " месяц";
                          if($pay_period > 1 && $pay_period < 5){
                            $tmp_month_spell = " месяца";
                          } else if($pay_period > 5){
                            $tmp_month_spell = " месяцев";
                          }

                          echo "<span class=\"pay-period\">Период оплаты — " . $pay_period . $tmp_month_spell . "</span>";
                          echo "<span class=\"price-range\">" . $price_per_month . " Р/мес." . "</span>";
                          echo "<span class=\"bill-info\">разовый платеж — " . $price . " Р" . "</span>";
                          echo "<span class=\"bill-info\">со счета спишется — " . $price . " Р" . "</span>";
                          echo "<span class=\"bill-info activ-date\">вступит в силу — сегодня" . "</span>";
                          echo "<span class=\"bill-info new-paydate\">активно до — " . $pay_day->format("d.m.Y") . "</span>";
                        echo "</div>";
                        echo "<a href=\"#\" class=\"choose-btn\">Выбрать</a>";
                      echo "</li>";
                    echo "</ul>";
                echo "</li>";
                $ids_v2++;
              }
        echo "</ul>"; // LEVEL-2 close

        echo "<label for=\"choose-tariff-" . $ids . "\" class=\"tariff\">";  // label tariff open

        $speed_style = "";
        $tmp_title = (String)$tarifs["title"];
        if (mb_strpos($tmp_title, "Земля") !== false) {
          $speed_style = "earth";
        } elseif (mb_strpos($tmp_title, "Вода") !== false) {
          $speed_style = "water";
        } elseif (mb_strpos($tmp_title, "Огонь") !== false) {
          $speed_style = "fire";
        } else{
          $speed_style = "";
        }

        echo "<span class=\"speed " . $speed_style . "\">" . $tarifs["speed"] . " Мбит/сек</span>";
        echo "<span class=\"price-range\">" . min($minmax_price) . "—" . max($minmax_price) . " Р/мес</span>";

        if($options > 0){
          for($i = 0; $i < $options; $i++){
            echo "<span class=\"bill-info\">" . $tarifs["free_options"][$i] . "</span>";
          }
        }

        echo "</label>"; // label tariff close

        echo "<a href=\"" . $sknt_link . "\" class=\"sknt-link\">узнать подробнее на сайте www.sknt.ru</a>";
        unset($minmax_price);
        unset($tmp);
        echo "</li>"; // tariff-block close
        $ids = $ids+1;
        }
      echo "</ul>"; // wrapper close
    }
    //echo "<br><div><pre>";
    //var_dump($data);
    //echo "</pre></div>";
  ?>
  </main>
    <script type="text/javascript" src="js/jquery-3.4.0.js"></script>
  <script type="text/javascript">
      $(".tariff-block").on( "click", function() {
          $('html, body').animate({scrollTop: '0px'}, 200);
      });
  </script>
  </body>
</html>
