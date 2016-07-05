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
      $db = "hmis_23may2016";

      $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");

      // $query = "SELECT dataelementgroupid, name FROM dataelementgroup";
      // $result = pg_query($con, $query) or die("Cannot execute query: $query\n");
      //
      // //echo "<table>";
      //
      // $deGroupIds = pg_fetch_all($result);
      //
      // $query = "SELECT dataelementgroup.dataelementgroupid as group_id, dataelement.uid, dataelement.name, dataelement.shortname, translation.value FROM dataelementgroupmembers, dataelement, dataelementgroup, translation WHERE dataelementgroup.dataelementgroupid = dataelementgroupmembers.dataelementgroupid AND dataelementgroupmembers.dataelementid = dataelement.dataelementid AND translation.objectuid = dataelement.uid AND translation.objectproperty = 'name'";
      //
      // $result = pg_query($con, $query) or die("Cannot execute query: $query\n");
      //
      // $translations = pg_fetch_all($result);
      //
      // pg_close($con);
      //
      // echo "<table>";
      //
      // echo "<tr>
      // <td>English name</td>
      // <td>English shortname</td>
      // <td>Lao name</td>
      // </tr>";
      //
      // foreach ($deGroupIds as $value) {
      //   $groupid = $value[dataelementgroupid];
      //   echo "<tr><th colspan=3>$value[name]</th></tr>";
      //   foreach ($translations as $translation) {
      //     if ($groupid == $translation[group_id]) {
      //       echo "<tr>";
      //       echo "<td>$translation[name]</td>";
      //       echo "<td>$translation[shortname]</td>";
      //       echo "<td>$translation[value]</td>";
      //       echo "</tr>";
      //     }
      //   }
      // }
      //
      // echo "</table>";

      $query = "SELECT indicatorgroupid, name FROM indicatorgroup";
      $result = pg_query($con, $query) or die("Cannot execute query: $query\n");

      //echo "<table>";

      $deGroupIds = pg_fetch_all($result);

      $query = "SELECT indicatorgroup.indicatorgroupid as group_id, indicator.uid, indicator.name, indicator.shortname FROM indicatorgroupmembers, indicator, indicatorgroup WHERE indicatorgroup.indicatorgroupid = indicatorgroupmembers.indicatorgroupid AND indicatorgroupmembers.indicatorid = indicator.indicatorid ";

      $result = pg_query($con, $query) or die("Cannot execute query: $query\n");

      $translations = pg_fetch_all($result);

      pg_close($con);

      echo "<table>";

      echo "<tr>
      <td>English name</td>
      <td>English shortname</td>
      <td>Lao name</td>
      </tr>";

      foreach ($deGroupIds as $value) {
        $groupid = $value[indicatorgroupid];
        echo "<tr><th colspan=3>$value[name]</th></tr>";
        foreach ($translations as $translation) {
          if ($groupid == $translation[group_id]) {
            echo "<tr>";
            echo "<td>$translation[name]</td>";
            echo "<td>$translation[shortname]</td>";
            echo "<td>$translation[value]</td>";
            echo "</tr>";
          }
        }
      }

      echo "</table>";


      //print_r($deGroupIds[0][dataelementgroupid]);
      //print_r($deGroupIds);
      //print_r($translations);

     ?>
  </body>
</html>
