<!-- list all the orgUnt int hmis backup -->
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style media="screen">
      table, tr, td {
        border: 1px solid black;
        border-collapse: collapse;
      }
    </style>
  </head>
  <body>
    <?php
      $host = "localhost";
      $user = "postgres";
      $pass = "Sam123456";
      $db = "hmis_09jun2016";
      $orgUnitIds = [79255,55,69,70,66,54,62,59,68,51,52,60,64,53,67,65,61,63,14838];

      $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");

      $query = "SELECT organisationunitid,name,code,parentid FROM organisationunit";
      $result = pg_query($con, $query) or die("Cannot execute query: $query\n");

      //echo "<table>";

      $orgUnits = pg_fetch_all($result);
      //print_r($orgUnits);

      echo "<table>";
      foreach ($orgUnits as $level2) {
        if($level2[parentid] == 50) {
          switch ($level2[organisationunitid]) {
            case 55:
            case 69:
            case 70:
            case 66:
            case 54:
            case 62:
            case 59:
            case 68:
            case 51:
            case 52:
            case 60:
            case 64:
            case 53:
            case 67:
            case 65:
            case 61:
            case 63:
            case 14838:
            case 79255:
              break;
            default:
                echo "<tr><td>$index</td><td>$level2[code]</td><td>$level2[name]</td></tr>";
              break;
          }
        }
      }
      $index = -12;
      foreach ($orgUnitIds as $orgUnitId) {
        foreach ($orgUnits as $level2) {
          if ($orgUnitId == $level2[organisationunitid]) {
            $index++;
            echo "<tr><td>$index</td><td>$level2[code]</td><td>$level2[name]</td></tr>";
            foreach ($orgUnits as $level3) {
              if ($level3[parentid] == $level2[organisationunitid]) {
                //print_r($value);
                $index++;
                echo "<tr><td>$index</td><td>$level3[code]</td><td>$level2[name]</td><td>$level3[name]</td></tr>";
                foreach ($orgUnits as $level4) {
                  if ($level4[parentid] == $level3[organisationunitid]) {
                    $index++;
                    echo "<tr><td>$index</td><td>$level4[code]</td><td>$level2[name]</td><td>$level3[name]</td><td>$level4[name]</td></tr>";
                  }
                }
              }
            }
          }
        }
      }

      echo "</table>";

      //echo "</table>";

      pg_close($con);

     ?>
  </body>
</html>
