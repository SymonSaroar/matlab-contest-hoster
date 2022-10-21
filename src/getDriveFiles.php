<?php
require __DIR__ . '/vendor/autoload.php';
function getPenalty($time){
    // echo $time;
    // echo "\n";
    $start = [18, 50];
    $timeArray = preg_split("/[\s:\/]+/", $time);
    $h = intval($timeArray[3]);
    $m = intval($timeArray[4]);
    $s = intval($timeArray[5]);
    $penalty = 0;
    $penalty += ($h - $start[0]) * 60 * 60 + $m * 60 + $s;
    return $penalty - ($start[1] * 60);
}
$everythingTry = false;
do {
    try{
$everythingTry = false;
$isPractice = true;
$servername = "localhost";
$username = "database-username";
$password = "database-password";
$dbname = "substatus";

$conn = new mysqli($servername, $username, $password, $dbname);

$problems = array("chocolates", "std_distance", "passive", "img_com", "head", "color");      // Problem Names

$client = new \Google_Client();
$client->setApplicationName('Drive File Retriever');
$client->setScopes([\Google_Service_Drive::DRIVE]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__ . '/credentials.json');

$service = new Google_Service_Drive($client);


$client->setScopes([\Google_Service_Sheets::SPREADSHEETS_READONLY]);

$responseSheetService = new Google_Service_Sheets($client);
// 1_gznc30QT71SyITz7uyfRZV7R0KKloK1djsmsGIe18g
// 1533225181
$responseSheetId = "1_gznc30QT71SyITz7uyfRZV7R0KKloK1djsmsGIe18g";                 // Sheets to Change
$responseSheetGId = "1533225181";
while(1){
$rowCounterFileName = "current_row.txt";                                       // Row initialization
$rowCounterFile = fopen($rowCounterFileName, "r");
fscanf($rowCounterFile, "%d", $rowCounter);
fclose($rowCounterFile);
$rowCounter++;
$range = "response!A{$rowCounter}:N";
echo $range;
echo "\n";
$tryAgain = false;
do{
    try{
        $tryAgain = false;
        $response = $responseSheetService->spreadsheets_values->get($responseSheetId, $range);
        $responseValues = $response->getValues();
    }
    catch (Exception $e){
        echo "Exeption Caugth: " . $e->getMessage() . "\n";
        $tryAgain = true;
        sleep(10);
    }
}while($tryAgain);

$newData = array();

$client->setScopes([\Google_Service_Drive::DRIVE_READONLY]);
if(empty($responseValues)){
    print "Empty\n";
}
else{
    foreach($responseValues as $row){
        $data = array();
        // 0 -> Timestamp
        // 1 -> Promo Code
        // 2, 3 -> a
        // 4, 5 -> b
        // ..
        // 12, 13 -> f
        if(empty($row)){
            continue;
        }
        
        $promocode = preg_replace('/\s+/', '', $row[1]);
        array_push($data, getPenalty($row[0]), $promocode);

        for($i = 2; $i < count($row); $i += 2){
            $problemName = $problems[floor($i / 2) - 1] . ".m";
            $problemNameW = $problems[floor($i / 2) - 1];
            $fileCreated = "";
            if($row[$i] == "" && $row[$i + 1] == ""){
                continue;
                $fileCreated = "";
            }
            else if($row[$i] == ""){
                // We have code
                // Write it 
                if(!file_exists(__DIR__ . "\\..\\$promocode")){
                    mkdir(__DIR__ . "\\..\\$promocode", 0777, true);
                }
                $fileCreated = "/{$promocode}/{$problemName}";
                echo $fileCreated . "\n";
                
                $mfile = fopen(__DIR__ . "\\..\\{$promocode}\\{$problemName}", "c+");
                ftruncate($mfile, 0);
                fprintf($mfile, "%s", $row[$i + 1]);
                fclose($mfile);
                array_push($data, $problemName);
            }
            else{
                // we have file
                // Download it
                if(!file_exists(__DIR__ . "\\..\\$promocode")){
                    mkdir(__DIR__ . "\\..\\$promocode", 0777, true);
                }
                $fileCreated = "/{$promocode}/{$problemName}";
                echo $fileCreated . "\n";
                
                $mfile = fopen(__DIR__ . "\\..\\{$promocode}\\{$problemName}", "c+");
                ftruncate($mfile, 0);
                $mfileId = substr($row[$i], strpos($row[$i], "=") + 1);
                echo $mfileId;
                echo "\n";
                $tryAgain = false;
                do{
                    try {
                        $tryAgain = false;
                        $mfileContent = $service->files->get($mfileId, array("alt" => "media"));
                        while(!$mfileContent->getBody()->eof()){
                            fwrite($mfile, $mfileContent->getBody()->read(1024));
                        }
                        fclose($mfile);
                    }
                    catch (Exception $e) {
                        echo "Exception Caught: " . $e->getMessage() . "\n";
                        $tryAgain = true;
                        sleep(10);
                    }
                }while($tryAgain);
                
                array_push($data, $problemName);
            }
            // python matlab_py.py problem prcode time_sec rootpath
            $rootPath = __DIR__ . "/../";
            $path = $rootPath . "{$promocode}";
            $pid = floor($i / 2) - 1;
            
            // $cmd = "cd " . $path . " && judge_" . $problemNameW . " '" . $data[1] . "' " . $data[0];
            echo "cd {$rootPath}{$data[1]}\n";
            if($pid == 0){
                echo "judge_chocolates('{$data[1]}', {$data[0]})\n";
            }
            else if($pid == 1){
                echo "judge_std_distance('{$data[1]}', {$data[0]})\n";
            }
            else if($pid == 2){
                echo "judge_passive('{$data[1]}', {$data[0]})\n";
            }
            else if($pid == 3){
                echo "judge_img_com('{$data[1]}', {$data[0]})\n";
            }
            else if($pid == 4){
                echo "judge_head('{$data[1]}', {$data[0]})\n";
            }
            else if($pid == 5){
                echo "judge_color('{$data[1]}', {$data[0]})\n";
            }
            if($isPractice) $data[0] = -7759;
            $cmd = "python matlab_py.py " . $pid . " " . $data[1] . " " . $data[0] . " " . $rootPath;
            $sql = "INSERT INTO submissions (promocode, url, status)
                    VALUES ('{$promocode}', '{$fileCreated}', -1)";
            echo $sql . "\n";
            if($conn->query($sql) === true){
                echo "Record Created with status -1\n";
            }
            echo $cmd;
            echo "\n";
            echo exec($cmd);
            echo "\n";
        }
        array_push($newData, $data);
    }

    $rowCounter = $rowCounter + count($newData) - 1;
    $rowCounterFile = fopen($rowCounterFileName, "w");
    fprintf($rowCounterFile, "%d", $rowCounter);
    fclose($rowCounterFile);
}
sleep(20);
}
}
catch (Exception $error){
    echo "Exception Caught: " . $error->getMessage() . "\n";
    $everythingTry = true;
    sleep(10);
}
}while($everythingTry);