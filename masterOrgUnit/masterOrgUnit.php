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
      //print_r($hmis_org);
      $malaria_org = getOrgUnitId(MALARIA_DB, 48);

      $inHmisNotMalaria = array();
      $inMalariaNotHmis = array();

      $flag = 0;

      // foreach ($hmis_org as $hmis_value) {
      //   foreach ($malaria_org as $value) {
      //     if ($hmis_value[id] == $key[id] ) {
      //       $flag++;
      //       break;
      //     }
      //   }
      //   if ($flag == 0) {
      //     array_push($inHmisNotMalaria, $hmis_value);
      //     // $inHmisNotMalaria[$hmis_key] = $hmis_value;
      //   }
      //   $flag = 0;
      //   //echo $hmis_value[id];
      // }

      foreach ($malaria_org as $value) {
        foreach ($hmis_org as $hmis_value) {
          if ($value[id] == $hmis_value[id]) {
            $flag++;
            break;
          }
        }
        if ($flag == 0) {
          array_push($inMalariaNotHmis, $value);
          //$inMalariaNotHmis[$key] = $value;
        }
        $flag = 0;
      }

      // $fp = fopen('toBeAddedToHmis.json', 'w');
      // fwrite($fp, json_encode($inMalariaNotHmis, JSON_UNESCAPED_UNICODE));
      // fclose($fp);
      //
      // $fp = fopen('inMalariaNotHmis.json', 'w');
      // fwrite($fp, json_encode($inMalariaNotHmis, JSON_UNESCAPED_UNICODE));
      // fclose($fp);
      //
      // function spitDHIS2xml($arrayOfOrg) {
      //
      // }

      //print_r($inHmisNotMalaria);
      print_r($inMalariaNotHmis);

      function getOrgUnitId($db, $parentid) {
        $host = "localhost";
        $user = "postgres";
        $pass = "Sam123456";
        $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");
        $orgunit = array();

        $query = "SELECT * FROM organisationunit WHERE parentid = $parentid";

        $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

        while($level2 = pg_fetch_assoc($rs1)) {

          $query = "SELECT * FROM organisationunit WHERE parentid = $level2[organisationunitid]";

          $rs2 = pg_query($con, $query);

          if (pg_num_rows($rs2)) {
            while ($level3 = pg_fetch_assoc($rs2)) {
              $query = "SELECT * FROM organisationunit WHERE parentid = $level3[organisationunitid]";

              $rs3 = pg_query($con, $query);
              if (pg_num_rows($rs3)) {
                while ($level4 = pg_fetch_assoc($rs3)) {
                  //echo "<tr><td>$level4[code]</td><td>$level2[name]</td><td>$level3[name]</td><td>$level4[uid]</td><td>$level4[name]</td></tr>";
                  $idPath = explode("/", $level4[path]);
                  //print_r($idPath);
                  array_push($orgunit, array(
                    "code" => $level4[code],
                    "name" => $level4[name],
                    "created" => str_replace(" ","T",$level4[created]),
                    "lastUpdated" => str_replace(" ","T",$level4[lastupdated]),
                    "shortName" => $level4[shortname],
                    "description" => $level4[description],
                    "uuid" => $level4[uuid],
                    "parent" => array("id" => $idPath[3]),
                    "path" => $level4[path],
                    "openingDate" => $level4[openingdate],
                    "comment" => $level4[comment],
                    "featureType" => $level4[featuretype],
                    "coordinates" => $level4[coordinates],
                    "contactPerson" => $level4[contactperson],
                    "phoneNumber" => $level4[phonenumber],
                    "id" => $level4[uid],
                    "level" => $level4[hierarchylevel]
                  ));
                  //$orgunit[$level4[uid]] = "Lao P.D.R > $level2[name] > $level3[name] > $level4[name]";
                }
              } else {
                //echo "<tr><td>$level3[code]</td><td>$level2[name]</td><td>$level3[name]</td></tr>";
                $idPath = explode("/", $level3[path]);
                array_push($orgunit, array(
                  "code" => $level3[code],
                  "name" => $level3[name],
                  "created" => str_replace(" ","T",$level3[created]),
                  "lastUpdated" => str_replace(" ","T",$level3[lastupdated]),
                  "shortName" => $level3[shortname],
                  "description" => $level3[description],
                  "uuid" => $level3[uuid],
                  "parent" => array("id" => $idPath[2]),
                  "path" => $level3[path],
                  "openingDate" => $level3[openingdate],
                  "comment" => $level3[comment],
                  "featureType" => $level3[featuretype],
                  "coordinates" => $level3[coordinates],
                  "contactPerson" => $level3[contactperson],
                  "phoneNumber" => $level3[phonenumber],
                  "id" => $level3[uid],
                  "level" => $level3[hierarchylevel]
                ));
              }
            }
          } else {
            //echo "<tr><td>$level2[code]</td><td>$level2[name]</td></tr>";
            $idPath = explode("/", $level2[path]);
            array_push($orgunit, array(
              "code" => $level2[code],
              "name" => $level2[name],
              "created" => str_replace(" ","T",$level2[created]),
              "lastUpdated" => str_replace(" ","T",$level2[lastupdated]),
              "shortName" => $level2[shortname],
              "description" => $level2[description],
              "uuid" => $level2[uuid],
              "parent" => array("id" => $idPath[1]),
              "path" => $level2[path],
              "openingDate" => $level2[openingdate],
              "comment" => $level2[comment],
              "featureType" => $level2[featuretype],
              "coordinates" => $level2[coordinates],
              "contactPerson" => $level2[contactperson],
              "phoneNumber" => $level2[phonenumber],
              "id" => $level2[uid],
              "level" => $level2[hierarchylevel]
            ));
            //$orgunit[$level2[uid]] = "Lao P.D.R > $level2[name]";
          }
        }
        pg_close($con);
        return $orgunit;
      }

     ?>
  </body>
</html>
