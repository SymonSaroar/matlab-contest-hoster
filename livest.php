<?php

require __DIR__ . '/src/SimpleXlSX.php';

$servername = "localhost";
$username = "database-username";
$password = "database-password";
$dbname = "substatus";

$info = SimpleXLSX::parse('name_pr.xlsx');
$prcode = [];
$pr_uni = [];
$data = SimpleXLSX::parse('localData.xlsx');
foreach ($info->rows() as $pr) {
  $prcode[$pr[1]] = $pr[0];
  $pr_uni[$pr[1]] = $pr[2];
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection Failed : " . $conn->connect_error);
}

$sql = 'SELECT * FROM submissions ORDER BY id DESC LIMIT 5';
$result = $conn->query($sql);
echo '<table class="t_st"><tbody>';
echo '
        <colgroup>
            <col span="1" style="width: 50%;">
            <col span="1" style="width: 40%;">
            <col span="1" style="width: 20%;">
        </colgroup>
        ';
echo '<tr><th class="nameBottom">problem</th><th class="nameBottom">status</th></tr>';
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td class="nameBottom">' . $row['url'] . '</td>';
    $stat = $row['status'];
    if ($stat == -1) {
      echo '<td class="nameBottom">pending...</td>';
    } elseif ($stat == -3) {
      echo '<td class="nameBottom">denial</td>';
    } elseif ($stat == 1) {
      echo '<td  class="nameBottom">accepted</td>';
    } elseif ($stat == 2) {
      echo '<td  class="nameBottom">time limit</td>';
    } elseif ($stat == 3) {
      echo '<td class="nameBottom">runtime error</td>';
    } elseif ($stat == 4) {
      echo '<td class="nameBottom">wrong answer</td>';
    }
    $subTime = new DateTime($row['time']);
    $currentTime = new DateTime('now', new DateTimezone('Asia/Dhaka'));
    $sstr = $subTime->format('Y-m-d H:i:s');
    $cstr = $currentTime->format('Y-m-d H:i:s');
    $ago = strtotime($cstr) - strtotime($sstr);
    if ($ago < 60) {
      echo '<td class="numBottom">' . $ago . 's ago</td>';
    } elseif (floor($ago / 60) < 60) {
      echo '<td class="numBottom">' . floor($ago / 60) . 'm ago</td>';
    } else {
      echo '<td class="numBottom">' . floor($ago / 60 / 60) . 'h ago</td>';
    }
    echo "</tr>";
  }
}
echo "</tbody></table>";
