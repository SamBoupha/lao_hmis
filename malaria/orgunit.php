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
      $db = "malaria_05012016";
      //$db = "hmis";

      $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");

      $query = "SELECT organisationunitid,name,code FROM organisationunit WHERE parentid = 48";
      $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

      echo "<table>";

      while($level2 = pg_fetch_assoc($rs1)) {

        $query = "SELECT organisationunitid,name,code FROM organisationunit WHERE parentid = $level2[organisationunitid]";

        $rs2 = pg_query($con, $query);

        if (pg_num_rows($rs2)) {
          // PAMS and province
          echo "<tr><td>$level2[code]</td><td>$level2[name]</td></tr>";
          while ($level3 = pg_fetch_assoc($rs2)) {
            $query = "SELECT organisationunitid,name,code FROM organisationunit WHERE parentid = $level3[organisationunitid]";

            $rs3 = pg_query($con, $query);
            if (pg_num_rows($rs3)) {
              // DAMS and district
              echo "<tr><td>$level3[code]</td><td>$level2[name]</td><td>$level3[name]</td></tr>";
              
              while ($level4 = pg_fetch_assoc($rs3)) {
                echo "<tr><td>$level4[code]</td><td>$level2[name]</td><td>$level3[name]</td><td>$level4[name]</td></tr>";
              }
            } else {
              //echo "<tr><td>$level3[code]</td><td>$level2[name]</td><td>$level3[name]</td></tr>";
            }
          }
        } else {
          //echo "<tr><td>$level2[code]</td><td>$level2[name]</td></tr>";
        }

        echo "</tr>";

      }

      echo "</table>";


      pg_close($con);

     ?>
  </body>
</html>
