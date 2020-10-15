<?php

$info = SimpleXLSX::parse('name_pr.xlsx');
$problemFile = SimpleXLSX::parse('problemSpecific.xlsx');

$prcode = array();
$pr_uni = array();
foreach($info->rows() as $pr){
    $prcode[$pr[1]] = $pr[0];
    $pr_uni[$pr[1]] = $pr[2];
}

$data = array();
if($file = SimpleXLSX::parse('localData.xlsx')){
    echo '<table><tbody>';
    echo '
        <colgroup>
            <col span="1" style="width: 5%;">
            <col span="1" style="width: 53%;">
            <col span="1" style="width: 8%;">
            <col span="1" style="width: 7.5%;">
            <col span="1" style="width: 7.5%;"> 
            <col span="1" style="width: 7.5%;">
            <col span="1" style="width: 7.5%;">
            <col span="1" style="width: 7.5%;">
            <col span="1" style="width: 7.5%;">
            <col span="1" style="width: 10%;">
        </colgroup>
        ';
    echo '<tr>
        <th class="rank" rowspan = "2">#</th>
        <th rowspan="2"></th>
        <th rowspan="2"></th>';
    foreach($problemFile->rows() as $num=>$problem){
        $problemChar = chr(65 + $num);
        $totac = $problem[1];
        $totsub = $problem[0];
        echo '<th>' . $problemChar . '<span class="problemfoot">' . $totac . '/' . $totsub . 
            '</span></th>';
    }
    
    echo '<th class="score" rowspan = "2">SCORE</th></tr>';
    echo '<tr class="score"><th>50</th><th>50</th><th>150</th><th>200</th><th>200</th><th>300</th></tr>';
            
    foreach($file->rows() as $i=>$p){
        array_push($data, $p);
        $data[$i][15] = $pr_uni[$data[$i][0]];
        $data[$i][0] = $prcode[$data[$i][0]];
        $score = 0;
        for($j = 0; $j < 6; $j++){ // for every problem
            $div = 10 ** (5 - $j);
            $mod = 10;
            $status = ($p[1] / $div) % $mod;
            
            // echo $status;
            // echo "\n";
            if($status == 1){
                if($j == 5) $score += floor(($p[14] / 100) * 300);
                else if($j == 0 || $j == 1) $score += 50;
                else if($j == 2) $score += 150;
                else if($j == 3 || $j == 3) $score += 200;
            }
        }
        $data[$i][14] = $score;

    }
    $cmpfunc = function($row1, $row2){
        if($row1[14] > $row2[14]){
            return -1;
        }
        else if($row1[14] < $row2[14]){
            return 1;
        }
        else{
            //score equal
            $penalty1 = 0;
            for($j = 0; $j < 6; $j++){
                $div = 10 ** (5 - $j);
                $mod = 10;
                $status = ($row1[1] / $div) % $mod;
                if(empty($row1[2 + 2 * $j + 1]) || $row1[2 + 2 * $j + 1] <= 0 || $status != 1) continue;
                // echo $status . "\n";
                $penalty1 += $row1[2 + 2 * $j + 1];
            }
            $penalty2 = 0;
            for($j = 0; $j < 6; $j++){
                $div = 10 ** (5 - $j);
                $mod = 10;
                $status = ($row2[1] / $div) % $mod;
                if(empty($row2[2 + 2 * $j + 1]) || $row2[2 + 2 * $j + 1] <= 0 || $status != 1) continue;
                // echo $status . "\n";
                $penalty2 += $row2[2 + 2 * $j + 1];
            }
            if($penalty1 > $penalty2){
                return 1;
            }
            else if($penalty1 < $penalty2){
                return -1;
            }
            else{
                // penalty eual
                $time1 = 0;
                for($j = 0; $j < 6; $j++){
                    if(empty($row1[2 + 2 * $j])) continue;
                    $time1 += $row1[2 + 2 * $j];
                }
                $time2 = 0;
                for($j = 0; $j < 6; $j++){
                    if(empty($row2[2 + 2 * $j])) continue;
                    $time2 += $row2[2 + 2 * $j];
                }
                if($time1 < $time2)
                    return -1;
                else if($time2 < $time1)
                    return 1;
                else{
                    // execution time
                    $numOfzeros1 = 0;
                    for($k = 0; $k < 6; $k++){
                        $div = 10 ** (5 - $k);
                        $mod = 10;
                        $status = ($row1[1] / $div) % $mod;
                        if($status == 0) $numOfzeros1++;
                    }
                    $numOfzeros2 = 0;
                    for($k = 0; $k < 6; $k++){
                        $div = 10 ** (5 - $k);
                        $mod = 10;
                        $status = ($row2[1] / $div) % $mod;
                        if($status == 0) $numOfzeros2++;
                    }
                    return ($numOfzeros1 > $numOfzeros2)? 1 : -1;
                }
            }
        }
    };
    usort($data, $cmpfunc);

    function numOfOnes($stat){
        $cnt = 0;
        while($stat > 0){
            if($stat % 10 == 1) $cnt++;
            $stat /= 10;
        }
        return $cnt;
    }

    foreach($data as $key=>$row){
        // if($row[0] == "Symon Saroar") continue;
        echo "<tr>";
        echo '<td rowspan="2" class="rank">' . ($key + 1) . '</td>';
        echo '<td class="nameTop">' . $row[0] . "</td>";
        echo '<td class="numTop">' . numOfOnes($row[1]) . "</td>";
        for($j = 0; $j < 6; $j++){
            $div = 10 ** (5 - $j);
            $mod = 10;
            $status = ($row[1] / $div) % $mod;
            if($status == 0){
                echo '<td class="numTop unsolved"></td>';
            }
            else if($status == 1){
                if($j == 3) $actext = "Compression Test Passed!";
                else $actext = "Accepted!";
                echo '<td class="numTop accepted hastooltip ';
                if($row[2 + 2 * $j + 1] == -7759)
                    echo 'outofcontest';
                
                echo '">&#10003;<span>' . $actext . '</span></td>';
            }
            else if($status == 2){
                echo '<td class="numTop timelimit hastooltip ';
                if($row[2 + 2 * $j + 1] == -7759)
                    echo 'outofcontest';
                echo '" title="Time Limit Exceeded">&infin;
                <span>Time Limit Exceeded</span></td>';
            }
            else if($status == 3){
                echo '<td class="numTop runtime hastooltip ';
                if($row[2 + 2 * $j + 1] == -7759)
                    echo 'outofcontest';
                echo '" title="Runtime Error">&#127777;
                <span>Runtime Error</span></td>';
            }
            else if($status == 4){
                echo '<td class="numTop wronganswer hastooltip ';
                if($row[2 + 2 * $j + 1] == -7759)
                    echo 'outofcontest';
                echo '" title="Wrong Answer">&#10007;
                <span>Wrong Answer</span></td>';
            }
        }

        echo '<td rowspan="2" class="score">' . $row[14] . "</td>";
        echo "</tr>";

        echo '<tr class="bottomRow">';
        echo '<td class="nameBottom">' . $row[15] . '</td>';

        $penalty = 0;
        for($j = 0; $j < 6; $j++){
            if(empty($row[2 + 2 * $j + 1]) || $row[2 + 2 * $j + 1] <= 0) continue;
            $penalty += $row[2 + 2 * $j + 1];
        }
        echo '<td class="numBottom">' . floor($penalty / 60) . '</td>';
        // penalty is in seconds
        for($j = 0; $j < 6; $j++){
            echo '<td class="numBottom">';
            if($row[2 + 2 * $j + 1] != 0 && $row[2 + 2 * $j + 1] != -7759) echo floor($row[2 + 2 * $j + 1] / 60);
            else echo '';
            echo '</td>';
        }
        echo "</tr>";
    }
    
    echo "</tbody></table>";
}