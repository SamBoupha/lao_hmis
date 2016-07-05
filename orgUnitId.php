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
    define("HMIS_DB", "hmis_23may2016");
    define("MALARIA_DB", "malaria_09052016");

      //$db = "malaria_05012016";
      //$db = "dhis2_18012016";

      $hmis_org = getOrgUnitId(HMIS_DB, 50);
      $malaria_org = getOrgUnitId(MALARIA_DB, 48);

      $inHmisNotMalaria = array();
      $inMalariaNotHmis = array();

      $flag = 0;

      foreach ($hmis_org as $hmis_key => $hmis_value) {
        foreach ($malaria_org as $key => $value) {
          if ($hmis_key == $key ) {
            $flag++;
            break;
          }
        }
        if ($flag == 0) {
          //array_push($inHmisNotMalaria, $hmis_value);
          $hmis_key = "$hmis_key";
          $inHmisNotMalaria[$hmis_key] = $hmis_value;
        }
        $flag = 0;
      }

      foreach ($malaria_org as $key => $value) {
        foreach ($hmis_org as $hmis_key => $hmis_value) {
          if ($key == $hmis_key ) {
            $flag++;
            break;
          }
        }
        if ($flag == 0) {
          //array_push($inHmisNotMalaria, $hmis_value);
          $inMalariaNotHmis[$key] = $value;
        }
        $flag = 0;
      }

      $fp = fopen('inHmisNotMalaria.json', 'w');
      fwrite($fp, json_encode($inHmisNotMalaria, JSON_UNESCAPED_UNICODE));
      fclose($fp);

      $fp = fopen('inMalariaNotHmis.json', 'w');
      fwrite($fp, json_encode($inMalariaNotHmis, JSON_UNESCAPED_UNICODE));
      fclose($fp);

      // print_r($inHmisNotMalaria);
      // print_r($inMalariaNotHmis);

      function getOrgUnitId($db, $parentid) {
        $host = "localhost";
        $user = "postgres";
        $pass = "Sam123456";
        $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");
        $orgunit = array();

        $query = "SELECT uid, name, code,organisationunitid FROM organisationunit WHERE parentid = $parentid";

        $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

        while($level2 = pg_fetch_assoc($rs1)) {

          $query = "SELECT uid,name,code,organisationunitid FROM organisationunit WHERE parentid = $level2[organisationunitid]";

          $rs2 = pg_query($con, $query);

          if (pg_num_rows($rs2)) {
            while ($level3 = pg_fetch_assoc($rs2)) {
              $query = "SELECT uid,organisationunitid,name,code FROM organisationunit WHERE parentid = $level3[organisationunitid]";

              $rs3 = pg_query($con, $query);
              if (pg_num_rows($rs3)) {
                while ($level4 = pg_fetch_assoc($rs3)) {
                  //echo "<tr><td>$level4[code]</td><td>$level2[name]</td><td>$level3[name]</td><td>$level4[uid]</td><td>$level4[name]</td></tr>";
                  $orgunit[$level4[uid]] = "Lao P.D.R > $level2[name] > $level3[name] > $level4[name]";
                }
              } else {
                //echo "<tr><td>$level3[code]</td><td>$level2[name]</td><td>$level3[name]</td></tr>";
                $orgunit[$level3[uid]] = "Lao P.D.R > $level2[name] > $level3[name]";
              }
            }
          } else {
            //echo "<tr><td>$level2[code]</td><td>$level2[name]</td></tr>";
            $orgunit[$level2[uid]] = "Lao P.D.R > $level2[name]";
          }
        }
        pg_close($con);
        return $orgunit;
      }

     ?>
  </body>
</html>
