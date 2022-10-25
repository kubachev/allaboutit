<?php

require_once __DIR__ . '/includes/google-api/vendor/autoload.php';
$googleAccountKeyFilePath = __DIR__ . '/includes/my-project-testovo-770bc5c3682e.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(['https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/spreadsheets']);

$service = new Google_Service_Sheets($client);

// Create new table
$spreadsheet = new Google_Service_Sheets_Spreadsheet();
$spreadsheetProperties = new Google_Service_Sheets_SpreadsheetProperties();
$spreadsheetProperties->setTitle('Topvisor Test Task');
$spreadsheet->setProperties($spreadsheetProperties);
$response = $service->spreadsheets->create($spreadsheet);

// Save table id
$spreadsheetId = $response->spreadsheetId;

// Get first sheet's title
$response = $service->spreadsheets->get($spreadsheetId);
$sheets = $response->getSheets();
$sheet = $sheets[0];
$sheetProperties = $sheet->getProperties(); // sheetProperties->title

// Fill data array for cells & Write it to sheet
$values = array();
for ($i=1;$i<=10;$i++) {
	array_push($values, array('0'=>$i));
}

$body = new Google_Service_Sheets_ValueRange( [ 'values' => $values ] );
$options = array( 'valueInputOption' => 'USER_ENTERED' );
$service->spreadsheets_values->update( $spreadsheetId, $sheetProperties->title.'!A1', $body, $options );

// Put doc to GDrive
$drive = new Google_Service_Drive($client);
$drivePermisson = new Google_Service_Drive_Permission();
$drivePermisson->setType('user');
$drivePermisson->setEmailAddress('admin@kubachev.ru');
$drivePermisson->setRole('writer');
$response = $drive->permissions->create($spreadsheetId, $drivePermisson);

//echo 'END';
?>