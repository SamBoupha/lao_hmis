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
      $db = "malaria_09jun2016";
      $startIndex = 0;
      //$db = "dhis2_18012016";
      $index = 0;

      $con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");

      $query = "SELECT organisationunitid,name,code FROM organisationunit WHERE parentid = 48";
      $rs1 = pg_query($con, $query) or die("Cannot execute query: $query\n");

      echo "<table>";

      function makeQuery($id) {
        return "select usermembership.userinfoid, users.username, users.lastlogin, userrole.name as userrole from usermembership, users, userrole, userrolemembers where userrolemembers.userroleid = userrole.userroleid and users.userid = userrolemembers.userid and usermembership.organisationunitid = $id and usermembership.userinfoid = users.userid and users.lastlogin >= '20160501' and users.lastlogin <= '20160611'";
      }

      while($level2 = pg_fetch_assoc($rs1)) {
        $startUser;
        $query = makeQuery($level2[organisationunitid]);
        $rs2 = pg_query($con, $query);
        if (pg_num_rows($rs2)) {
          while ($activeUser = pg_fetch_assoc($rs2)) {
            if($startIndex == 0) {
              $startUser = $activeUser[username];
              $startIndex++;
              echo "<tr><td>$index</td><td>$activeUser[lastlogin]</td><td>$level2[name]</td><td>$activeUser[username]</td><td>$activeUser[userrole]";
            } else {
              if ($startUser == $activeUser[username]) {
                echo ", $activeUser[userrole]</td></tr>";
              } else {
                echo "<tr><td>$index</td><td>$activeUser[lastlogin]</td><td>$level2[name]</td><td>$activeUser[username]</td><td>$activeUser[userrole]</td></tr>";
              }
              $startIndex = 0;
            }
            //$activeUser[lastlogin] = str_replace("-","/",$activeUser[lastlogin])
          }
        }

        $query = "SELECT organisationunitid,name,code FROM organisationunit WHERE parentid = $level2[organisationunitid]";

        $rs2 = pg_query($con, $query);

        if (pg_num_rows($rs2)) {
          while ($level3 = pg_fetch_assoc($rs2)) {

            $query = makeQuery($level3[organisationunitid]);

            $rs3 = pg_query($con, $query);
            if (pg_num_rows($rs3)) {
              while ($activeUser = pg_fetch_assoc($rs3)) {
                if($startIndex == 0) {
                  $startUser = $activeUser[username];
                  $startIndex++;
                  echo "<tr><td>$index</td><td>$activeUser[lastlogin]</td><td>$level2[name]</td><td>$level3[name]</td><td>$activeUser[username]</td><td>$activeUser[userrole]";
                } else {
                  if ($startUser == $activeUser[username]) {
                    echo ", $activeUser[userrole]</td></tr>";
                  } else {
                    echo "<tr><td>$index</td><td>$activeUser[lastlogin]</td><td>$level2[name]</td><td>$level3[name]</td><td>$activeUser[username]</td><td>$activeUser[userrole]</td></tr>";
                  }
                  $startIndex = 0;
                }

                //$activeUser[lastlogin] = str_replace("-","/",$activeUser[lastlogin]);

              }
            }
          }
        }
        echo "</tr>";
      }

      echo "</table>";

      echo "<br /><br />";

      $query = makeQuery(48);

      $rs = pg_query($con, $query);
      if (pg_num_rows($rs)) {
        echo "<table>";
        while ($activeUser = pg_fetch_assoc($rs)) {
          $index++;
          echo "<tr><td>$index</td><td>$activeUser[lastlogin]</td><td>Lao PDR</td><td>$activeUser[username]</td><td>$activeUser[userrole]</td></tr>";
        }
        echo "</table>";
      }

      pg_close($con);

     ?>
  </body>
</html>
