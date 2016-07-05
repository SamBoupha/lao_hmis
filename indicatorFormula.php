<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style media="screen">
      table, tr, td {
        border: 1px solid black;
        border-collapse: collapse;
      }
      table {
        width: 100%;
      }
      td {
        word-wrap: break-all;
      }
    </style>
  </head>
  <body>
    <?php
      $host = "localhost";
      $user = "postgres";
      $pass = "Sam123456";
      $db = "hmis_23may2016";
      $indicatorArray = array();
      $dataElementArray = array();
      $categoryOptionComboArray = array();

      $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");

      $query = "SELECT name,description,numerator,denominator FROM indicator";
      $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

      while($indicator = pg_fetch_assoc($rs1)) {
        $indicatorArray[$indicator[name]] = array(
          "description" => $indicator[description],
          "numerator" => $indicator[numerator],
          "denominator" => $indicator[denominator]
        );
      }

      $query = "SELECT uid,name FROM dataelement";
      $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

      while($dataElement = pg_fetch_assoc($rs1)) {
        $dataElementArray[$dataElement[uid]] = $dataElement[name];
      }

      $query = "SELECT name,uid FROM categoryoptioncombo";
      $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

      while($categoryOptionCombo = pg_fetch_assoc($rs1)) {
        $categoryOptionComboArray[$categoryOptionCombo[uid]] = $categoryOptionCombo[name];
      }
      pg_close($con);

      function translate($idValue) {
        global $dataElementArray, $categoryOptionComboArray;
        // match all text within {} bug is OUG{} & #{}
        preg_match_all('/{(.*?)}/',$idValue,$matches);
        if (sizeof($matches[1])) {
          foreach ($matches[1] as $key => $value) {
            $data = explode(".",$value);
            foreach($dataElementArray as $uid => $name) {
              if ($data[0] == $uid) {
                $data[0] = $name;
                break;
              }
            }
            foreach($categoryOptionComboArray as $uid => $name) {
              if ($data[1] == $uid) {
                $data[1] = $name;
                break;
              }
            }
            $matches[1][$key] = $data[0]." ".$data[1];
          }

          $str = "";
          for ($i=0; $i < sizeof($matches[1]); $i++) {
            $str .= $matches[1][$i];
            if ($i < sizeof($matches[1])-1) {
              $str .= "+\n";
            }
          }
          return $str;
        }
        return 1;
      }

     ?>
     <table>
       <tr>
         <th>Index</th>
         <th>Indicator</th>
         <th>Description</th>
         <th>numerator</th>
         <th>denominator</th>
       </tr>
       <?php
        $index = 1;
          foreach ($indicatorArray as $name => $value) {
            echo "<tr>";
            echo "<td>$index</td>";
            echo "<td>$name</td>";
            echo "<td>$value[description]</td>";
            echo "<td style=''>".translate($value[numerator])."</td>";
            echo "<td style='word-wrap: break-all;'>".translate($value[denominator])."</td>";
            echo "</tr>";
            $index++;
          }
        ?>
     </table>
  </body>
</html>
