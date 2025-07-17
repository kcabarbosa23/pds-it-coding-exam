<?php

$inputFile = 'sample-log.txt';
$outputFile = 'output.txt';

if (!file_exists($inputFile)) 
{
    die("Input file not found.\n");
}

$lines = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$outputLogs = [];
$ids = [];
$userIDs = [];

for($i = 0; $i < count($lines); $i++)
{
    $id       = trim(substr($lines[$i], 0, 12));
    $userID   = trim(substr($lines[$i], 12, 6));
    $bytesTX  = number_format((int)trim(substr($lines[$i], 18, 8)));
    $bytesRX  = number_format((int)trim(substr($lines[$i], 26, 8)));
    $dateRaw  = trim(substr($lines[$i], 34, 17));

    $dateTime = DateTime::createFromFormat('Y-m-d H:i', $dateRaw);
    $formattedDate = $dateTime ? $dateTime->format('D, F d Y, H:i:s') : "Invalid Date";

    $outputLogs[] = "{$userID}|{$bytesTX}|{$bytesRX}|{$formattedDate}|{$id}";
    $ids[] = $id;
    $userIDs[] = $userID;
}

natsort($ids);
$ids = array_values($ids);

$userIDs = array_unique($userIDs);
sort($userIDs);

file_put_contents($outputFile, "");
foreach ($outputLogs as $log) 
{
    file_put_contents($outputFile, $log . "\n", FILE_APPEND);
}

file_put_contents($outputFile, "\n", FILE_APPEND);
foreach ($ids as $id) {
    file_put_contents($outputFile, $id . "\n", FILE_APPEND);
}

file_put_contents($outputFile, "\n\n\n", FILE_APPEND);
foreach ($userIDs as $index => $userID) {
    file_put_contents($outputFile, "[" . ($index + 1) . "] " . $userID . "\n", FILE_APPEND);
}

echo "Processing complete. Check output.txt\n";

?>
