<?php

// Debugging only, log stuff to browser
// include_once "logger/ChromePHP.php";
// ChromePhp::log('Hello console!');
$stalkees = json_decode($_POST['stalkeelist']);

/* All data to be returned will be stored
 * in here, then encoded in JSON and
 * returned to JavaScript */
$returnData;

function storeMasterServerTotalCount($data, $overwrite = false) {
    global $returnData;
    if ($overwrite) {
        $returnData['masterServerTotalCount'] = $data;
    } else {
        $returnData['masterServerTotalCount'] .= $data;
    }
}

function storeServerList($data, $overwrite = false) {
    // Escape stuff, decoded in JavaScript later
    $data = htmlentities($data);
    if (htmlspecialchars($_GET['serverList'] == 1)) {
        global $returnData;
        if ($overwrite) {
            $returnData['serverList'] = $data;
        } else {
            $returnData['serverList'] .= $data;
        }
    }
}

function storeStalkeeStatus($stalkeeName, $gametype, $serverIP, $serverPort) {
    global $returnData;
    $returnData['stalkeeStatus'][] = array($stalkeeName, $gametype, $serverIP . ":" . $serverPort);
}

function overwriteStalkeeStatus($data) {
    global $returnData;
    $returnData['stalkeeStatus'] = $data;
}

function storeRequestsFailed($data, $overwrite = false) {
    global $returnData;
    if ($overwrite) {
        $returnData['requestsFailed'] = $data;
    } else {
        $returnData['requestsFailed'] .= $data;
    }
}


require_once("TwRequest.php");
$request = new TwRequest;
$request->addMasterserver("master4.teeworlds.com");
$request->loadMasterserverCounts();
$totalServerCount = 0;
foreach ($request->getMasterservers() as $masterserver) {
    if (isset($masterserver['num_servers'])) {
        $totalServerCount += $masterserver['num_servers'];
    } else {
        // Request failed
    }
}
storeMasterServerTotalCount($totalServerCount);

$request->loadServersFromMasterservers(TwRequest::ALL_VERSIONS);
// request the statuses
$request->loadServerInfo();
// store all server IPs found
$allServerIPs = [];
// guilty until proven innocent
$noPlayersFound = true;
$failedServerRequests = 0;
foreach ($request->getServers() as $server) {
    storeServerList("Server: " . $server[0] . ":" . $server[1] . "<br>");
    $allServerIPs[] = $server[0];
    
    // check if the request was successful
    if (isset($server['version'])) {
        // it was
        storeServerList("Version: ".$server['version']."<br>");
        storeServerList("Name: ".$server['name']."<br>");
        storeServerList("Map: ".$server['map']."<br>");
        storeServerList("Gametype: ".$server['gametype']."<br>");
        if ($server['password']) {
            storeServerList("Password protected: yes<br>");
        } else {
            storeServerList("Password protected: no<br>");
        }
        // progression is 0.5 only
        if ($server[2] == TwRequest::VERSION_05) {
            storeServerList("Progression: ".$server['progression']." %<br>");
        }
        storeServerList("Players: ".$server['num_players']." / ".$server['max_players']."<br>");
        // ingame players is 0.6 only
        if ($server[2] == TwRequest::VERSION_06) {
            storeServerList("Players ingame: ".$server['num_players_ingame']. " / ".$server['max_players_ingame']."<br>");
        }
        storeServerList("Player list:<br>");
        // check if players are online
        if (count($server['players']) == 0) {
            storeServerList("No players online<br>");
        }
        else {
            storeServerList("<ul>");
            foreach ($server['players'] as $player) {
                storeServerList("<li>");
                foreach ($stalkees as $stalkee) {
                    if ($stalkee[1] == $player['name']) {
                        if ($stalkee[2] == $player['clan'] || $stalkee[3] == true) {
                            $noPlayersFound = false;
                            storeStalkeeStatus($stalkee[1], $server['gametype'], $server[0], $server[1]);
                        }
                    }
                }
                storeServerList("Name: ".$player['name']."<br>");
                storeServerList("Score: ".$player['score']."<br>");
                // this is 0.6 only
                if ($server[2] == TwRequest::VERSION_06) {
                    storeServerList("Clan: ".$player['clan']."<br>");
                    if ($player['ingame']) {
                        storeServerList("This player is ingame.<br>");
                    } else {
                        storeServerList("This player is in spectator mode.<br>");
                    }
                }
                storeServerList("</li>");
            }
            storeServerList("</ul>");
        }
    }
    else {
        // request unsuccessful
        storeServerList("Request failed<br>");
        $failedServerRequests++;
    }
    storeServerList("<br>");
}

storeRequestsFailed($failedServerRequests);

if ($noPlayersFound) {
    // Return an empty array
    overwriteStalkeeStatus(array());
}

// Debugging only
// $serverDataFile = $dataPath . 'test.txt';
// $serverDataHandle = fopen($serverDataFile, 'w') or die("Cannot open file: " . print_r(error_get_last(), true));
// fwrite($serverDataHandle, $returnData['serverList']);
// fclose($serverDataHandle);

echo json_encode($returnData);