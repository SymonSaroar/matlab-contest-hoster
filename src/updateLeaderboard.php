<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/SimpleXLSX.php';

define('CREDENTIALS', __DIR__ . '/credentials.json');

$leaderboardId = "1nexI6syDdHYyh54Pv0MhxyT0AngmdJbPaRLy9Z6dRjQ";
$leaderboardGid = "0";
$range = "sheet!B3:B200";

$name2pr = SimpleXLSX::parse("../name_pr.xlsx");
$localFile = SimpleXLSX::parse("../localData.xlsx");

$client = new \Google_Client();
$client->setApplicationName('Matlab Leader Board Updater');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(CREDENTIALS);

$service = new Google_Service_Sheets($client);

$response = $service->spreadsheets_values->get($leaderboardId, $range);
$names = $response->getValues();
function getPrCode($name)
{
  $name2pr = SimpleXLSX::parse("../name_pr.xlsx");
  foreach ($name2pr->rows() as $aRow) {
    if ($aRow[0] == $name) {
      return $aRow[1];
    }
  }
  return "INVALID";
}

foreach ($names as $i => $row) {
  if (empty($row)) {
    continue;
  }
  $range = "sheet!C" . (3 + $i) . ":H" . (3 + $i + 1);
  $name = $row[0];
  $PRCODE = getPrCode($name);
  foreach ($localFile->rows() as $localDataRow) {
    if ($localDataRow[0] == $PRCODE) {
      // found PRCODE's data .. update leaderboard
      $updateValue = [["", "", "", "", "", ""], ["", "", "", "", "", ""]];
      for ($j = 0; $j < 6; $j++) {
        // for each problem
        $div = 10 ** (5 - $j);
        $mod = 10;
        if ($mod == 1) {
          $status = $localDataRow[1] / $div;
        } else {
          $status = ($localDataRow[1] / $div) % $mod;
        }
        // echo $status;
        // echo "\n";
        if ($status == 0) {
          continue;
        }
        if ($status == 1) {
          $updateValue[0][$j] = "AC";
          $updateValue[1][$j] = sprintf(
            "%d%7.1f",
            $localDataRow[2 + 2 * $j + 1],
            $localDataRow[2 + 2 * $j]
          );
        } elseif ($status == 2) {
          $updateValue[0][$j] = "TL";
          $updateValue[1][$j] = sprintf(
            "%d%7.1f",
            $localDataRow[2 + 2 * $j + 1],
            $localDataRow[2 + 2 * $j]
          );
        } elseif ($status == 3) {
          $updateValue[0][$j] = "RE";
          $updateValue[1][$j] = sprintf(
            "%d%7.1f",
            $localDataRow[2 + 2 * $j + 1],
            $localDataRow[2 + 2 * $j]
          );
        } elseif ($status == 4) {
          $updateValue[0][$j] = "WA";
          $updateValue[1][$j] = sprintf(
            "%d%7.1f",
            $localDataRow[2 + 2 * $j + 1],
            $localDataRow[2 + 2 * $j]
          );
        }
      }
      // Generated updateValue
      // Update:
      $body = new Google_Service_Sheets_ValueRange([
        'values' => $updateValue,
      ]);
      $params = [
        'valueInputOption' => 'RAW',
      ];
      $result = $service->spreadsheets_values->update(
        $leaderboardId,
        $range,
        $body,
        $params
      );
      break;
    }
  }
}
