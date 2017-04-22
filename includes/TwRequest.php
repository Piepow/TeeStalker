<?php
/* 
Copyright (C) 2012-2013 Marius Neugebauer - see http://code.teele.eu/twrequest

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice, the URL and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


last change: 2013-01-14
*/

/*
 * My lame edits:
 *     - Commented out line 72, PHP gives a parse error. Ok, fuck Argentina.
 *     - Got angry, commented out entire $countries array (starting at line 61)
 *     - Commented out function getCountry (starting at line 851)
 *     - Commented out function getCountryCode (starting at line 858)
 *     - Commented out function getCountryName (starting at lin 865)
 */

// echo "Started TwRequest<br>";

class TwRequest {
	
	/* version */
	const VERSION_05 = 5; // 0.5.*
	const VERSION_06 = 6; // 0.6.*
	const DEFAULT_VERSION = self::VERSION_06; // to be used if none given
	const ALL_VERSIONS = null; // all versions
	protected $versions = array(self::VERSION_05, self::VERSION_06);
	
	/* default ports */
	const DEFAULT_PORT_MASTER = 8300; // default master server port
	const DEFAULT_PORT_VERSION = 8302; // default version server port
	const DEFAULT_PORT_SERVER = 8303; // default server port
	
	/* connection */
	const CONNECT_TIMEOUT = 1; // time in sec to establish the connection
	const MAX_CONNECTIONS_MASTER = 10; // maximum number of connections to have open to master servers at the same time
	const MAX_CONNECTIONS_SERVER = 50; // maximum number of connections to have open to servers at the same time
	const REQUEST_SLEEP = 20; // time in ms to sleep between every loop while waiting for answers
	const TIMEOUT_MASTER = 1000; // time in ms to wait for a master servers answer
	const TIMEOUT_VERSION = 1000; // time in ms to wait for a version servers answer
	const TIMEOUT_SERVER = 1000; // time in ms to wait for a servers answer
	
	/* server flags */
	const SERVER_FLAG_PASSWORD = 1;
	
	/* master servers */
	protected $masterservers = array(
		array("master1.teeworlds.com", 8300),
		array("master2.teeworlds.com", 8300),
		array("master3.teeworlds.com", 8300)/*,
		array("master4.teeworlds.com", 8300)*/
	);
	
	/* servers */
	protected $servers = array();
	
	/* countries */
// 	public static $countries = array(
// 		/* custom */
// 		-1	=> array('default', 'none'),
// 		901	=> array('XEN', 'England'),
// 		902	=> array('XNI', 'Northern Ireland'),
// 		903	=> array('XSC', 'Scotland'),
// 		904	=> array('XWA', 'Wales'),
// 		/* ISO 3166-1 */
// 		4	=> array('AF', 'Afghanistan'),
// 		248	=> array('AX', 'Ã…land Islands'),
// 		8	=> array('AL', 'Albania'),
// 		12	=> array('DZ', 'Algeria'),
// 		16	=> array('AS', 'American Samoa'),
// 		20	=> array('AD', 'Andorra'),
// 		24	=> array('AO', 'Angola'),
// 		660	=> array('AI', 'Anguilla'),
// 		10	=> array('AQ', 'Antarctica'),
// 		// 028	=> array('AG', 'Antigua and Barbuda'),
// 		032	=> array('AR', 'Argentina'),
// 		051	=> array('AM', 'Armenia'),
// 		533	=> array('AW', 'Aruba'),
// 		36	=> array('AU', 'Australia'),
// 		40	=> array('AT', 'Austria'),
// 		31	=> array('AZ', 'Azerbaijan'),
// 		44	=> array('BS', 'Bahamas'),
// 		48	=> array('BH', 'Bahrain'),
// 		50	=> array('BD', 'Bangladesh'),
// 		52	=> array('BB', 'Barbados'),
// 		112	=> array('BY', 'Belarus'),
// 		56	=> array('BE', 'Belgium'),
// 		84	=> array('BZ', 'Belize'),
// 		204	=> array('BJ', 'Benin'),
// 		60	=> array('BM', 'Bermuda'),
// 		64	=> array('BT', 'Bhutan'),
// 		68	=> array('BO', 'Bolivia, Plurinational State of'),
// 		535	=> array('BQ', 'Bonaire, Sint Eustatius and Saba'),
// 		70	=> array('BA', 'Bosnia and Herzegovina'),
// 		72	=> array('BW', 'Botswana'),
// 		74	=> array('BV', 'Bouvet Island'),
// 		76	=> array('BR', 'Brazil'),
// 		86	=> array('IO', 'British Indian Ocean Territory'),
// 		96	=> array('BN', 'Brunei Darussalam'),
// 		100	=> array('BG', 'Bulgaria'),
// 		854	=> array('BF', 'Burkina Faso'),
// 		108	=> array('BI', 'Burundi'),
// 		116	=> array('KH', 'Cambodia'),
// 		120	=> array('CM', 'Cameroon'),
// 		124	=> array('CA', 'Canada'),
// 		132	=> array('CV', 'Cape Verde'),
// 		136	=> array('KY', 'Cayman Islands'),
// 		140	=> array('CF', 'Central African Republic'),
// 		148	=> array('TD', 'Chad'),
// 		152	=> array('CL', 'Chile'),
// 		156	=> array('CN', 'China'),
// 		162	=> array('CX', 'Christmas Island'),
// 		166	=> array('CC', 'Cocos (Keeling) Islands'),
// 		170	=> array('CO', 'Colombia'),
// 		174	=> array('KM', 'Comoros'),
// 		178	=> array('CG', 'Congo'),
// 		180	=> array('CD', 'Congo, the Democratic Republic of the'),
// 		184	=> array('CK', 'Cook Islands'),
// 		188	=> array('CR', 'Costa Rica'),
// 		384	=> array('CI', 'CÃ´te d\'Ivoire'),
// 		191	=> array('HR', 'Croatia'),
// 		192	=> array('CU', 'Cuba'),
// 		531	=> array('CW', 'CuraÃ§ao'),
// 		196	=> array('CY', 'Cyprus'),
// 		203	=> array('CZ', 'Czech Republic'),
// 		208	=> array('DK', 'Denmark'),
// 		262	=> array('DJ', 'Djibouti'),
// 		212	=> array('DM', 'Dominica'),
// 		214	=> array('DO', 'Dominican Republic'),
// 		218	=> array('EC', 'Ecuador'),
// 		818	=> array('EG', 'Egypt'),
// 		222	=> array('SV', 'El Salvador'),
// 		226	=> array('GQ', 'Equatorial Guinea'),
// 		232	=> array('ER', 'Eritrea'),
// 		233	=> array('EE', 'Estonia'),
// 		231	=> array('ET', 'Ethiopia'),
// 		238	=> array('FK', 'Falkland Islands (Malvinas)'),
// 		234	=> array('FO', 'Faroe Islands'),
// 		242	=> array('FJ', 'Fiji'),
// 		246	=> array('FI', 'Finland'),
// 		250	=> array('FR', 'France'),
// 		254	=> array('GF', 'French Guiana'),
// 		258	=> array('PF', 'French Polynesia'),
// 		260	=> array('TF', 'French Southern Territories'),
// 		266	=> array('GA', 'Gabon'),
// 		270	=> array('GM', 'Gambia'),
// 		268	=> array('GE', 'Georgia'),
// 		276	=> array('DE', 'Germany'),
// 		288	=> array('GH', 'Ghana'),
// 		292	=> array('GI', 'Gibraltar'),
// 		300	=> array('GR', 'Greece'),
// 		304	=> array('GL', 'Greenland'),
// 		308	=> array('GD', 'Grenada'),
// 		312	=> array('GP', 'Guadeloupe'),
// 		316	=> array('GU', 'Guam'),
// 		320	=> array('GT', 'Guatemala'),
// 		831	=> array('GG', 'Guernsey'),
// 		324	=> array('GN', 'Guinea'),
// 		624	=> array('GW', 'Guinea-Bissau'),
// 		328	=> array('GY', 'Guyana'),
// 		332	=> array('HT', 'Haiti'),
// 		334	=> array('HM', 'Heard Island and McDonald Islands'),
// 		336	=> array('VA', 'Holy See (Vatican City State)'),
// 		340	=> array('HN', 'Honduras'),
// 		344	=> array('HK', 'Hong Kong'),
// 		348	=> array('HU', 'Hungary'),
// 		352	=> array('IS', 'Iceland'),
// 		356	=> array('IN', 'India'),
// 		360	=> array('ID', 'Indonesia'),
// 		364	=> array('IR', 'Iran, Islamic Republic of'),
// 		368	=> array('IQ', 'Iraq'),
// 		372	=> array('IE', 'Ireland'),
// 		833	=> array('IM', 'Isle of Man'),
// 		376	=> array('IL', 'Israel'),
// 		380	=> array('IT', 'Italy'),
// 		388	=> array('JM', 'Jamaica'),
// 		392	=> array('JP', 'Japan'),
// 		832	=> array('JE', 'Jersey'),
// 		400	=> array('JO', 'Jordan'),
// 		398	=> array('KZ', 'Kazakhstan'),
// 		404	=> array('KE', 'Kenya'),
// 		296	=> array('KI', 'Kiribati'),
// 		408	=> array('KP', 'Korea, Democratic People\'s Republic of'),
// 		410	=> array('KR', 'Korea, Republic of'),
// 		414	=> array('KW', 'Kuwait'),
// 		417	=> array('KG', 'Kyrgyzstan'),
// 		418	=> array('LA', 'Lao People\'s Democratic Republic'),
// 		428	=> array('LV', 'Latvia'),
// 		422	=> array('LB', 'Lebanon'),
// 		426	=> array('LS', 'Lesotho'),
// 		430	=> array('LR', 'Liberia'),
// 		434	=> array('LY', 'Libya'),
// 		438	=> array('LI', 'Liechtenstein'),
// 		440	=> array('LT', 'Lithuania'),
// 		442	=> array('LU', 'Luxembourg'),
// 		446	=> array('MO', 'Macao'),
// 		807	=> array('MK', 'Macedonia, the former Yugoslav Republic of'),
// 		450	=> array('MG', 'Madagascar'),
// 		454	=> array('MW', 'Malawi'),
// 		458	=> array('MY', 'Malaysia'),
// 		462	=> array('MV', 'Maldives'),
// 		466	=> array('ML', 'Mali'),
// 		470	=> array('MT', 'Malta'),
// 		584	=> array('MH', 'Marshall Islands'),
// 		474	=> array('MQ', 'Martinique'),
// 		478	=> array('MR', 'Mauritania'),
// 		480	=> array('MU', 'Mauritius'),
// 		175	=> array('YT', 'Mayotte'),
// 		484	=> array('MX', 'Mexico'),
// 		583	=> array('FM', 'Micronesia, Federated States of'),
// 		498	=> array('MD', 'Moldova, Republic of'),
// 		492	=> array('MC', 'Monaco'),
// 		496	=> array('MN', 'Mongolia'),
// 		499	=> array('ME', 'Montenegro'),
// 		500	=> array('MS', 'Montserrat'),
// 		504	=> array('MA', 'Morocco'),
// 		508	=> array('MZ', 'Mozambique'),
// 		104	=> array('MM', 'Myanmar'),
// 		516	=> array('NA', 'Namibia'),
// 		520	=> array('NR', 'Nauru'),
// 		524	=> array('NP', 'Nepal'),
// 		528	=> array('NL', 'Netherlands'),
// 		540	=> array('NC', 'New Caledonia'),
// 		554	=> array('NZ', 'New Zealand'),
// 		558	=> array('NI', 'Nicaragua'),
// 		562	=> array('NE', 'Niger'),
// 		566	=> array('NG', 'Nigeria'),
// 		570	=> array('NU', 'Niue'),
// 		574	=> array('NF', 'Norfolk Island'),
// 		580	=> array('MP', 'Northern Mariana Islands'),
// 		578	=> array('NO', 'Norway'),
// 		512	=> array('OM', 'Oman'),
// 		586	=> array('PK', 'Pakistan'),
// 		585	=> array('PW', 'Palau'),
// 		275	=> array('PS', 'Palestinian Territory, Occupied'),
// 		591	=> array('PA', 'Panama'),
// 		598	=> array('PG', 'Papua New Guinea'),
// 		600	=> array('PY', 'Paraguay'),
// 		604	=> array('PE', 'Peru'),
// 		608	=> array('PH', 'Philippines'),
// 		612	=> array('PN', 'Pitcairn'),
// 		616	=> array('PL', 'Poland'),
// 		620	=> array('PT', 'Portugal'),
// 		630	=> array('PR', 'Puerto Rico'),
// 		634	=> array('QA', 'Qatar'),
// 		638	=> array('RE', 'RÃ©union'),
// 		642	=> array('RO', 'Romania'),
// 		643	=> array('RU', 'Russian Federation'),
// 		646	=> array('RW', 'Rwanda'),
// 		652	=> array('BL', 'Saint BarthÃ©lemy'),
// 		654	=> array('SH', 'Saint Helena, Ascension and Tristan da Cunha'),
// 		659	=> array('KN', 'Saint Kitts and Nevis'),
// 		662	=> array('LC', 'Saint Lucia'),
// 		663	=> array('MF', 'Saint Martin (French part)'),
// 		666	=> array('PM', 'Saint Pierre and Miquelon'),
// 		670	=> array('VC', 'Saint Vincent and the Grenadines'),
// 		882	=> array('WS', 'Samoa'),
// 		674	=> array('SM', 'San Marino'),
// 		678	=> array('ST', 'Sao Tome and Principe'),
// 		682	=> array('SA', 'Saudi Arabia'),
// 		686	=> array('SN', 'Senegal'),
// 		688	=> array('RS', 'Serbia'),
// 		690	=> array('SC', 'Seychelles'),
// 		694	=> array('SL', 'Sierra Leone'),
// 		702	=> array('SG', 'Singapore'),
// 		534	=> array('SX', 'Sint Maarten (Dutch part)'),
// 		703	=> array('SK', 'Slovakia'),
// 		705	=> array('SI', 'Slovenia'),
// 		90	=> array('SB', 'Solomon Islands'),
// 		706	=> array('SO', 'Somalia'),
// 		710	=> array('ZA', 'South Africa'),
// 		239	=> array('GS', 'South Georgia and the South Sandwich Islands'),
// 		728	=> array('SS', 'South Sudan'),
// 		724	=> array('ES', 'Spain'),
// 		144	=> array('LK', 'Sri Lanka'),
// 		729	=> array('SD', 'Sudan'),
// 		740	=> array('SR', 'Suriname'),
// 		744	=> array('SJ', 'Svalbard and Jan Mayen'),
// 		748	=> array('SZ', 'Swaziland'),
// 		752	=> array('SE', 'Sweden'),
// 		756	=> array('CH', 'Switzerland'),
// 		760	=> array('SY', 'Syrian Arab Republic'),
// 		158	=> array('TW', 'Taiwan, Province of China'),
// 		762	=> array('TJ', 'Tajikistan'),
// 		834	=> array('TZ', 'Tanzania, United Republic of'),
// 		764	=> array('TH', 'Thailand'),
// 		626	=> array('TL', 'Timor-Leste'),
// 		768	=> array('TG', 'Togo'),
// 		772	=> array('TK', 'Tokelau'),
// 		776	=> array('TO', 'Tonga'),
// 		780	=> array('TT', 'Trinidad and Tobago'),
// 		788	=> array('TN', 'Tunisia'),
// 		792	=> array('TR', 'Turkey'),
// 		795	=> array('TM', 'Turkmenistan'),
// 		796	=> array('TC', 'Turks and Caicos Islands'),
// 		798	=> array('TV', 'Tuvalu'),
// 		800	=> array('UG', 'Uganda'),
// 		804	=> array('UA', 'Ukraine'),
// 		784	=> array('AE', 'United Arab Emirates'),
// 		826	=> array('GB', 'United Kingdom'),
// 		840	=> array('US', 'United States'),
// 		581	=> array('UM', 'United States Minor Outlying Islands'),
// 		858	=> array('UY', 'Uruguay'),
// 		860	=> array('UZ', 'Uzbekistan'),
// 		548	=> array('VU', 'Vanuatu'),
// 		862	=> array('VE', 'Venezuela, Bolivarian Republic of'),
// 		704	=> array('VN', 'Viet Nam'),
// 		92	=> array('VG', 'Virgin Islands, British'),
// 		850	=> array('VI', 'Virgin Islands, U.S.'),
// 		876	=> array('WF', 'Wallis and Futuna'),
// 		732	=> array('EH', 'Western Sahara'),
// 		887	=> array('YE', 'Yemen'),
// 		894	=> array('ZM', 'Zambia'),
// 		716	=> array('ZW', 'Zimbabwe'),
// 	);
	
	
	/* add a server */
	function addServer($address, $port = self::DEFAULT_PORT_SERVER, $version = self::DEFAULT_VERSION) {
		
		/* cast to right types */
		$address = (string)$address;
		$port = (int)$port;
		$version = (int)$version;
		
		/* if address is invalid abort */
		if (empty($address)) {
			return;
		}
		
		/* if port is invalid set to default port */
		if ($port < 0 || $port > 65535) {
			$port = self::DEFAULT_PORT_SERVER;
		}
		
		/* if version is invalid set to default version */
		if (!in_array($version, $this->versions)) {
			$version = self::DEFAULT_VERSION;
		}
		
		/* add if not a duplicate */
		$server = array($address, $port, $version);
		if (!in_array($server, $this->servers, true)) {
			$this->servers[] = $server;
		}
	}
	
	/* add an array of servers in the form array(address, port, version) */
	function addServers(Array $servers) {
		
		/* go through each server */
		foreach ($servers as $server) {
			
			/* skip if address is missing */
			if (!isset($server[0])) {
				continue;
			}
			
			/* add this server */
			$this->addServer($server[0], isset($server[1]) ? $server[1] : self::DEFAULT_PORT_SERVER, isset($server[2]) ? $server[2] : self::DEFAULT_VERSION);
		}
	}
	
	/* returns the array of servers */
	function getServers() {
		return $this->servers;
	}
	
	/* returns first server in the array */
	function getFirstServer() {
		return reset($this->servers);
	}
	
	/* deletes all servers */
	function emptyServers() {
		$this->servers = array();
	}
	
	/* add a master server */
	function addMasterserver($address, $port = self::DEFAULT_PORT_MASTER) {
		
		/* cast to right types */
		$address = (string)$address;
		$port = (int)$port;
		
		/* if address is invalid abort */
		if (empty($address)) {
			return;
		}
		
		/* if port is invalid set to default port */
		if ($port < 0 || $port > 65535) {
			$port = self::DEFAULT_PORT_MASTER;
		}
		
		/* add if not a duplicate */
		$server = array($address, $port);
		if (!in_array($server, $this->masterservers, true)) {
			$this->masterservers[] = $server;
		}
	}
	
	/* add an array of servers in the form array(address, port) */
	function addMasterservers(Array $servers) {
		
		/* go through each server */
		foreach ($servers as $server) {
			
			/* skip if address is missing */
			if (!isset($server[0])) {
				continue;
			}
			
			/* add this server */
			$this->addMasterserver($server[0], isset($server[1]) ? $server[1] : self::DEFAULT_PORT_MASTER);
		}
	}
	
	/* deletes all servers */
	function emptyMasterservers() {
		$this->masterservers = array();
	}
	
	/* returns the master servers */
	function getMasterservers() {
		return $this->masterservers;
	}
	
	/* returns the current timestamp converted to ms */
	static function getTimeInMs() {
		return round(microtime(true) * 1000);
	}
	
	/* builds up a udp connection */
	static function establishConnection($authority, $data, &$errno = 0, &$errstr = "") {
		
		/* connect socket */
		$socket = @stream_socket_client("udp://".$authority, $errno, $errstr, self::CONNECT_TIMEOUT);
		
		/* abort on failure */
		if (!$socket || $errno) {
			return false;
		}
			
		// stream_set_timeout($socket, self::CONNECT_TIMEOUT);
		
		/* unblock, so that the requests can be parallel */
		stream_set_blocking($socket, 0);
		
		/* send request */
		fwrite($socket, $data);
		
		return $socket;
	}
	
	/* loads servers from master servers and adds them to the server list */
	function loadServersFromMasterservers($versions = self::DEFAULT_VERSION) {
		
		/* if given, select all versions */
		if ($versions === self::ALL_VERSIONS) {
			$versions = $this->versions;
		}
		
		/* check versions */
		$versions = (array)$versions;
		foreach ($versions as $k => $version) {
			if (!in_array($version, $this->versions)) {
				unset($versions[$k]);
			}
		}
		
		/* if none version, set to default */
		if (!count($versions)) {
			$versions = array(self::DEFAULT_VERSION);
		}
		
		/* plan connections */
		$connectionStack = array();
		foreach ($this->masterservers as &$masterserver) {
			foreach ((array)$versions as $version) {
				$connectionStack[] = array(&$masterserver, $version);
			}
		}
		
		/* define variables */
		$connections = array(); // to store the active connections
		$numConnections = 0; // number of connections (number of elements in $connections)
		$curConn = 0; // next position in the connection stack to handle
		
		/* additionally request the count of servers, to prevent the connection to wait for more servers than necessary */
		$connectionsSrvCount = array(); // to store the active connections for the server count request
		$numConnectionsSrvCount = 0; // number of connections for the server count request (number of elements in $this->masterservers)
		$curConnSrvCount = 0; // next position in the connection stack for the server count request to handle
		
		/* loop as long as there are master servers to wait for */
		while ($numConnections > 0 || isset($connectionStack[$curConn])) {
			
			/* count request */
			
			/* build up connections until limit is reached */
			for (; isset($this->masterservers[$curConnSrvCount]) && ($numConnections + $numConnectionsSrvCount) < self::MAX_CONNECTIONS_MASTER; $curConnSrvCount++) {
				
				$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffcou2";
				
				$authority = $this->masterservers[$curConnSrvCount][0].':'.$this->masterservers[$curConnSrvCount][1];
				$connection = self::establishConnection($authority, $data);
				
				/* add to connection if not failed */
				if ($connection) {
					$connectionsSrvCount[$curConnSrvCount] = array($connection, &$this->masterservers[$curConnSrvCount], self::getTimeInMs());
					$numConnectionsSrvCount++;
				}
				
				/* save memory */
				unset($authority, $connection);
			}
			
			/* check for responses */
			foreach ($connectionsSrvCount as $n => $connection) {
				
				/* read data */
				$data = fread($connection[0], 16); // size = header_size + 2 = 16
				
				/* packet length */
				$datalen = strlen($data);
				
				/* check if data is usable, otherwise skip */
				if ($data === false || $datalen < 14 || substr($data, 0, 14) !== "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffsiz2") {
					continue;
				}
				
				/* analyse data */
				$connection[1]['num_servers'] = (ord($data[14]) << 8) | ord($data[15]);
						
				/* only one packet expected, so drop after one packet */
				unset($connectionsSrvCount[$n]);
				
			}
			
			/* drop connections that have timed out */
			foreach ($connectionsSrvCount as $n => $connection) {
				if ((self::getTimeInMs() - $connection[2]) >= self::TIMEOUT_MASTER) {
					unset($connectionsSrvCount[$n]);
				}
			}
			
			/* renew connection counter */
			$numConnectionsSrvCount = count($connectionsSrvCount);
			
			/* server list request */
			
			/* build up connections until limit is reached */
			for (; isset($connectionStack[$curConn]) && ($numConnections + $numConnectionsSrvCount) < self::MAX_CONNECTIONS_MASTER; $curConn++) {
				
				switch ($connectionStack[$curConn][1]) {
					case self::VERSION_05:
						$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffreqt"; break;
					case self::VERSION_06:
						$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffreq2"; break;
					default:
						continue 2;
				}
				
				$authority = $connectionStack[$curConn][0][0].':'.$connectionStack[$curConn][0][1];
				$connection = self::establishConnection($authority, $data);
				
				/* set loaded server counter */
				if (!isset($connectionStack[$curConn][0]['num_servers_loaded'])) {
					$connectionStack[$curConn][0]['num_servers_loaded'] = 0;
				}
				
				/* add to connection if not failed */
				if ($connection) {
					$connections[$curConn] = array($connection, &$connectionStack[$curConn], self::getTimeInMs());
					$numConnections++;
				}
				
				/* save memory */
				unset($authority, $connection);
			}
			
			/* check for responses */
			foreach ($connections as $n => $connection) {
				
				/* read data */
				switch ($connection[1][1]) {
					case self::VERSION_05:
						$data = fread($connection[0], 464); // size = header_size + max_servers_per_packet * server_size = 14 + 75 * 6 = 464
						break;
					case self::VERSION_06:
						$data = fread($connection[0], 1364); // size = header_size + max_servers_per_packet * server_size = 14 + 75 * 18 = 1364
						break;
				}
				
				/* packet length */
				$datalen = strlen($data);
				
				/* check if data is usable, otherwise skip */
				switch ($connection[1][1]) {
					case self::VERSION_05:
						if ($data === false || $datalen < 14 || substr($data, 0, 14) !== "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xfflist") {
							continue 2;
						}
						break;
					case self::VERSION_06:
						if ($data === false || $datalen < 14 || substr($data, 0, 14) !== "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xfflis2") {
							continue 2;
						}
						break;
				}
				
				/* analyse data */
				switch ($connection[1][1]) {
					
					case self::VERSION_05:
						
						for ($i = 14; ($i + 6) <= $datalen; $i += 6) {
							
							/* IP */
							$ip = inet_ntop(substr($data, $i, 4));
							
							/* port (actually an array. the port is in $port[1]) */
							$port = unpack("v", substr($data, $i + 4, 2));
							
							$this->addServer($ip, $port[1], self::VERSION_05);
							
							/* increment server loaded counter */
							$connection[1][0]['num_servers_loaded']++;
							
						}
						
						break;
					
					case self::VERSION_06:
						
						for ($i = 14; ($i + 18) <= $datalen; $i += 18) {
							
							/* switch between IPv4 and IPv6 */
							if (substr($data, $i, 12) === "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
								$ip = inet_ntop(substr($data, $i + 12, 4)); // IPv4
							} else {
								$ip = "[".inet_ntop(substr($data, $i, 16))."]"; // IPv6
							}
							
							/* port (actually an array. the port is in $port[1]) */
							$port = unpack("n", substr($data, $i + 16, 2));
							
							$this->addServer($ip, $port[1], self::VERSION_06);
							
							/* increment server loaded counter */
							$connection[1][0]['num_servers_loaded']++;
							
						}
						
						break;
					
				}
				
				/* if connection is finished drop it */
				switch ($connection[1][1]) {
					case self::VERSION_05:
						if ($datalen < 494) {
							unset($connections[$n]);
						}
						break;
					case self::VERSION_06:
						if ($datalen < 1364) {
							unset($connections[$n]);
						}
						break;
				}
				
			}
			
			/* drop connections that have timed out */
			foreach ($connections as $n => $connection) {
				if ((self::getTimeInMs() - $connection[2]) >= self::TIMEOUT_MASTER) {
					unset($connections[$n]);
				}
			}
			
			/* drop connections that have gathered enough servers */
			foreach ($connections as $n => $connection) {
				if (!empty($connection[1][0]['num_servers']) && !empty($connection[1][0]['num_servers_loaded']) && $connection[1][0]['num_servers_loaded'] >= $connection[1][0]['num_servers']) {
					unset($connections[$n]);
				}
			}
			
			/* renew connection counter */
			$numConnections = count($connections);
			
			/* sleep a bit */
			usleep(self::REQUEST_SLEEP * 1000);
		}
		
	}
	
	/* loads the information of the server */
	function loadServerInfo() {
		
		/* define variables */
		$connections = array(); // to store the active connections
		$numConnections = 0; // number of connections (number of elements in $connections)
		$curServer = 0; // next position in the server array to handle
		
		/* loop as long as there are master servers to wait for */
		while ($numConnections > 0 || isset($this->servers[$curServer])) {
			
			/* build up connections until limit is reached */
			for (; isset($this->servers[$curServer]) && $numConnections < self::MAX_CONNECTIONS_SERVER; $curServer++) {
				
				switch ($this->servers[$curServer][2]) {
					case self::VERSION_05:
						$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffgief"; break;
					case self::VERSION_06:
						$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffgie3\x05"; break;
					default:
						continue 2;
				}
				
				$authority = $this->servers[$curServer][0].':'.$this->servers[$curServer][1];
				$connection = self::establishConnection($authority, $data);
				
				/* add to connection if not failed */
				if ($connection) {
					$connections[$curServer] = array($connection, &$this->servers[$curServer], self::getTimeInMs());
					$numConnections++;
				}
				
				/* save memory */
				unset($authority, $connection);
			}
			
			/* check for responses */
			foreach ($connections as $n => $connection) {
				
				/* read data */
				switch ($connection[1][2]) {
					case self::VERSION_05:
						$data = fread($connection[0], 2048); // TODO: find out how great it really can be
						break;
					case self::VERSION_06:
						$data = fread($connection[0], 2048); // by my calc the max size is 850, but trying to read more doesn't harm
						break;
				}
				
				/* packet length */
				$datalen = strlen($data);
				// var_dump($datalen);
				
				/* check if data is usable, otherwise skip */
				switch ($connection[1][2]) {
					case self::VERSION_05:
						if ($data === false || $datalen < 14 || substr($data, 0, 14) !== "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffinfo") {
							continue 2;
						}
						break;
					case self::VERSION_06:
						if ($data === false || $datalen < 15 || substr($data, 0, 15) !== "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffinf35") {
							continue 2;
						}
						break;
				}
				
				/* analyse data */
				switch ($connection[1][2]) {
					
					case self::VERSION_05:
						
						$data = explode("\x00", $data);
						
						$connection[1]['version'] = substr((string)$data[0], 14);
						$connection[1]['name'] = (string)$data[1];
						$connection[1]['map'] = (string)$data[2];
						$connection[1]['gametype'] = (string)$data[3];
						$flags = (int)$data[4];
						$connection[1]['password'] = (($flags & self::SERVER_FLAG_PASSWORD) === self::SERVER_FLAG_PASSWORD);
						$connection[1]['progression'] = (int)$data[5];
						$connection[1]['num_players'] = (int)$data[6];
						$connection[1]['max_players'] = (int)$data[7];
						$connection[1]['ping'] = (int)(self::getTimeInMs() - $connection[2]);
						
						$connection[1]['players'] = array();
						for ($i = 8; isset($data[$i+2]); $i += 2) {
							$player = array();
							$player['name'] = (string)$data[$i];
							$player['score'] = (int)$data[$i+1];
							$connection[1]['players'][] = $player;
						}
						
						break;
					
					case self::VERSION_06:
						
						$data = explode("\x00", $data);
						
						$connection[1]['version'] = (string)$data[1];
						$connection[1]['name'] = (string)$data[2];
						$connection[1]['map'] = (string)$data[3];
						$connection[1]['gametype'] = (string)$data[4];
						$flags = (int)$data[5];
						$connection[1]['password'] = (($flags & self::SERVER_FLAG_PASSWORD) === self::SERVER_FLAG_PASSWORD);
						$connection[1]['num_players'] = (int)$data[8];
						$connection[1]['max_players'] = (int)$data[9];
						$connection[1]['num_players_ingame'] = (int)$data[6];
						$connection[1]['max_players_ingame'] = (int)$data[7];
						$connection[1]['ping'] = (int)(self::getTimeInMs() - $connection[2]);
						
						$connection[1]['players'] = array();
						for ($i = 10; isset($data[$i+4]); $i += 5) {
							$player = array();
							$player['name'] = (string)$data[$i];
							$player['clan'] = (string)$data[$i+1];
							$player['country'] = (int)$data[$i+2];
							// $player['countrydata'] = self::getCountry($player['country']);
							$player['score'] = (int)$data[$i+3];
							$player['ingame'] = (bool)$data[$i+4];
							$connection[1]['players'][] = $player;
						}
						
						break;
					
				}
				
				/* drop it, because there comes only one packet to analyse */
				unset($connections[$n]);
				
			}
			
			/* drop connections that have timed out */
			foreach ($connections as $n => $connection) {
				if ((self::getTimeInMs() - $connection[2]) >= self::TIMEOUT_SERVER) {
					unset($connections[$n]);
				}
			}
			
			/* renew connection counter */
			$numConnections = count($connections);
			
			/* sleep a bit */
			usleep(self::REQUEST_SLEEP * 1000);
		}
		
	}
	
	/* return an array with the country code and name */
// 	static function getCountry($number) {
// 		$number = (int)$number;
// 		return isset(self::$countries[$number]) ? self::$countries[$number] : self::$countries[-1];
// 	}
	
	/* return an array with the country code */
// 	static function getCountryCode($number) {
// 		$number = (int)$number;
// 		return isset(self::$countries[$number]) ? self::$countries[$number][0] : self::$countries[-1][0];
// 	}
	
	/* return an array with the country name */
// 	static function getCountryName($number) {
// 		$number = (int)$number;
// 		return isset(self::$countries[$number]) ? self::$countries[$number][1] : self::$countries[-1][1];
// 	}
	
	/* loads servers from master servers and adds them to the server list */
	function loadMasterserverCounts() {
		
		/* define variables */
		$connections = array(); // to store the active connections
		$numConnections = 0; // number of connections (number of elements in $connections)
		$curConn = 0; // next position in the connection stack to handle
		
		/* loop as long as there are master servers to wait for */
		while ($numConnections > 0 || isset($this->masterservers[$curConn])) {
			
			/* build up connections until limit is reached */
			for (; isset($this->masterservers[$curConn]) && $numConnections < self::MAX_CONNECTIONS_MASTER; $curConn++) {
				
				$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffcou2";
				
				$authority = $this->masterservers[$curConn][0].':'.$this->masterservers[$curConn][1];
				$connection = self::establishConnection($authority, $data);
				
				/* add to connection if not failed */
				if ($connection) {
					$connections[$curConn] = array($connection, &$this->masterservers[$curConn], self::getTimeInMs());
					$numConnections++;
				}
				
				/* save memory */
				unset($authority, $connection);
			}
			
			/* check for responses */
			foreach ($connections as $n => $connection) {
				
				/* read data */
				$data = fread($connection[0], 16); // size = header_size + 2 = 16
				
				/* packet length */
				$datalen = strlen($data);
				
				/* check if data is usable, otherwise skip */
				if ($data === false || $datalen < 14 || substr($data, 0, 14) !== "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffsiz2") {
					continue;
				}
				
				/* analyse data */
				$connection[1]['num_servers'] = (ord($data[14]) << 8) | ord($data[15]);
						
				/* only one packet expected, so drop after one packet */
				unset($connections[$n]);
				
			}
			
			/* drop connections that have timed out */
			foreach ($connections as $n => $connection) {
				if ((self::getTimeInMs() - $connection[2]) >= self::TIMEOUT_MASTER) {
					unset($connections[$n]);
				}
			}
			
			/* renew connection counter */
			$numConnections = count($connections);
			
			/* sleep a bit */
			usleep(self::REQUEST_SLEEP * 1000);
		}
		
	}
	
	/* sums up the counts of servers from the master servers after a request */
	function getTotalServerCount() {
		$count = 0;
		foreach ($this->masterservers as $masterserver) {
			if (isset($masterserver['num_servers_loaded'])) {
				$count += $masterserver['num_servers_loaded'];
			} else if (isset($masterserver['num_servers'])) {
				$count += $masterserver['num_servers'];
			}
		}
		return $count;
	}
	
	/* requests the current version from a version server */
	function getCurrentVersion($address = "version.teeworlds.com", $port = self::DEFAULT_PORT_VERSION) {
		
		$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffverg";
		
		$authority = $address.':'.$port;
		$connection = self::establishConnection($authority, $data);
		
		/* add to connection if not failed */
		if (!$connection) {
			return "";
		}
		
		/* get time */
		$time = self::getTimeInMs();
		
		while ((self::getTimeInMs() - $time) < self::TIMEOUT_VERSION) {
			
			/* read data */
			$data = fread($connection, 18); // size = 14 + 4
			var_dump($data);
				
			/* packet length */
			$datalen = strlen($data);
			
			/* check if data is usable, otherwise skip */
			if ($data !== false && $datalen === 18 && substr($data, 0, 14) === "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffvers") {
				
				/* this is not tested, because the version server ist offline */
				
				/* analyse data */
				$version = substr($data, 15, 1).".".substr($data, 16, 1).".".substr($data, 17, 1);
				return $version;
				
			}
			
			/* sleep a bit */
			usleep(self::REQUEST_SLEEP * 1000);
		}
		
		return "";
	}
	
	/* requests the current map list from a version server */
	function getCurrentMaplist($address = "version.teeworlds.com", $port = self::DEFAULT_PORT_VERSION) {
		
		$maps = array();
		
		$data = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffvmlg";
		
		$authority = $address.':'.$port;
		$connection = self::establishConnection($authority, $data);
		
		/* add to connection if not failed */
		if (!$connection) {
			return $maps;
		}
		
		/* get time */
		$time = self::getTimeInMs();
		
		while ((self::getTimeInMs() - $time) < self::TIMEOUT_VERSION) {
			
			/* read data */
			$data = fread($connection, 782); // size = 14 + 48 * 16
			var_dump($data);
				
			/* packet length */
			$datalen = strlen($data);
			
			/* check if data is usable, otherwise skip */
			if ($data !== false && $datalen >= 14 && substr($data, 0, 14) === "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xffvmls") {
				
				/* this is not tested, because the version server ist offline */
				
				/* analyse data */
				for ($i = 15; ($i + 16) < $datalen; $i += 16) {
					$name = trim(substr($data, $i, 8));
					$crc = substr($data, $i + 8, 4);
					$size = unpack("N", substr($data, $i + 12, 4));
					$map[] = array($name, $crc, $size);
				}
				
				// if last packet (size not maximal packet size) or maximum maps reached
				if ($datalen < 782 || count($maps) >= 768) {
					return $maps;
				}
				
			}
			
			/* sleep a bit */
			usleep(self::REQUEST_SLEEP * 1000);
		}
		
		return $maps;
	}
}
?>
