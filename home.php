<!DOCTYPE html>
<html lang="en">
<head>
	<title>TeeStalker</title>
	<?php include "includes/head.inc.php"; ?>
</head>
<body>
	<div class = "bodyWrap">
	<div class="jumboHead">
		<a href="<?php getcwd();?>"><h1>TeeStalker</h1></a>
	</div>
		<?php include "includes/nav.inc.php"; ?>
	<div id = "mainWrap">
		<div class = "infoBox" id = "loadingWrap">
			<div id = "stalkeeStatus"></div>
			<div id = "dynamicLoading"></div>
		</div>
		<div class = "infoBox" id = "masterServerStatus"></div>
		<div class = "infoBox" id = "serverList"></div>
		<div class = "infoBox" id = "stalkeeList"></div>
		<div class = "infoBox settings" id = "settings1">
			<div class = "block">
				<input type = "checkbox" id = "muteNotifications"><label for = "muteNotifications"><span class = "weirdPlaceholder"></span>Mute notifications</label><br>
			</div>
			<div class = "block">
				<input type = "checkbox" id = "showNotificationsOnOffline"><label for = "showNotificationsOnOffline"><span class = "weirdPlaceholder"></span>Don't show notifications when stalkees go offline</label><br>
			</div>
		</div>
		<div class = "infoBox settings" id = "settings5">
			<div class = "block">
				<input type = "checkbox" id = "loadServerList"><label for = "loadServerList"><span class = "weirdPlaceholder"></span>Load full server list <span class = "lightColor">(requires high bandwidth)</span></label>
			</div>
		</div>
		<div class = "infoBox settings" id = "settings2">
			<div id = "refreshDelayWrap" class= "block">
				<p id = "refreshDelayHeader">Refresh delay:</p>
					<div id = "refreshDelayButtonWrap">
						<input type = "radio" name = "refreshDelay" value = "20000" id = "refreshDelay20"><label for = "refreshDelay20"><span class= "weirdPlaceholder"></span>20 seconds</label><br>
						<input type = "radio" name = "refreshDelay" value = "40000" id = "refreshDelay40"><label for = "refreshDelay40"><span class= "weirdPlaceholder"></span>40 seconds</label><br>
						<input type = "radio" name = "refreshDelay" value = "60000" id = "refreshDelay60"><label for = "refreshDelay60"><span class= "weirdPlaceholder"></span>1 minute</label><br>
						<input type = "radio" name = "refreshDelay" value = "120000" id = "refreshDelay120"><label for = "refreshDelay120"><span class= "weirdPlaceholder"></span>2 minutes</label><br>
						<input type = "radio" name = "refreshDelay" value = "300000" id = "refreshDelay300"><label for = "refreshDelay300"><span class= "weirdPlaceholder"></span>5 minutes</label>
					</div>
			</div>
		</div>
		<div class = "infoBox settings" id = "settings4">
			<div id = "totalServerThresholdWrap" class= "block">
				<p id = "totalServerThresholdHeader">Minimum threshold for loaded servers:</p>
					<div id = "totalServerThresholdButtonWrap">
						<input type = "radio" name = "totalServerThreshold" value = "500" id = "totalServerThreshold500"><label for = "totalServerThreshold500"><span class= "weirdPlaceholder"></span>500 servers</label><br>
						<input type = "radio" name = "totalServerThreshold" value = "600" id = "totalServerThreshold600"><label for = "totalServerThreshold600"><span class= "weirdPlaceholder"></span>600 servers</label><br>
						<input type = "radio" name = "totalServerThreshold" value = "700" id = "totalServerThreshold700"><label for = "totalServerThreshold700"><span class= "weirdPlaceholder"></span>700 servers</label><br>
						<input type = "radio" name = "totalServerThreshold" value = "800" id = "totalServerThreshold800"><label for = "totalServerThreshold800"><span class= "weirdPlaceholder"></span>800 servers</label><br>
						<input type = "radio" name = "totalServerThreshold" value = "900" id = "totalServerThreshold900"><label for = "totalServerThreshold900"><span class= "weirdPlaceholder"></span>900 servers</label><br>
					</div>
			</div>
		</div>
		<div class = "infoBox settings" id = "settings3">
			<div id = "requestsFailedToleranceWrap" class= "block">
				<p id = "requestsFailedToleranceHeader">Tolerance for failed server requests:</p>
					<div id = "requestsFailedToleranceButtonWrap">
						<input type = "radio" name = "requestsFailedTolerance" value = "0.05" id = "requestsFailedToleranceHigh"><label for = "requestsFailedToleranceHigh"><span class= "weirdPlaceholder"></span>5%</label><br>
						<input type = "radio" name = "requestsFailedTolerance" value = "0.1" id = "requestsFailedToleranceMedium"><label for = "requestsFailedToleranceMedium"><span class= "weirdPlaceholder"></span>10%</label><br>
						<input type = "radio" name = "requestsFailedTolerance" value = "0.2" id = "requestsFailedToleranceLow"><label for = "requestsFailedToleranceLow"><span class= "weirdPlaceholder"></span>20%</label><br>
					</div>
			</div>
		</div>
		<div class="infoBox settings invisible" id = "settings6">
			<div id="resetSettingsButton" class = "button" onclick="userData.loadDefaults()">Load defaults</div>
			<div id="clearAllButton" class = "button" onclick="modalFunctions.displayClearAll()">Clear all</div>
		</div>
		<div class = "infoBox" id = "stalkerAbout">
			<?php include "includes/about.inc.php";?>
		</div>
	</div>
	<?php include "includes/modal.inc.html"; ?>
</div>
</body>
</html>