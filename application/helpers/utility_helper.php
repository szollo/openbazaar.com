<?php

function asset_url()
{
	return base_url() . 'assets/';
}

function market_price($coinType)
{
	$ticker = file_get_contents("https://ticker.openbazaar.org/api");
	$ticker = json_decode($ticker);
	return "Ƀ" . number_format(1 / $ticker->{$coinType}->last * $ticker->BTC->last, 5);
}

function convert_price($amount, $from, $to, $precision = 8)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$ob_ticker = $CI->cache->get('ob_ticker');
	if ($ob_ticker == "") {
		$ob_ticker = json_decode(file_get_contents("https://ticker.openbazaar.org/api"));
	}

	if ($to != "BTC" || isset($ob_ticker->$to)) {
		$precision = 2;
	}

	$e = $CI->cache->file->save('ob_ticker', $ob_ticker, 300);
	if ($from == "BTC") {
		if (!isset($ob_ticker->$to)) {
			$to = "BTC";
		}

		$price = ($amount / 100000000) * $ob_ticker->$to->last;
	}
	else {
		$price = (($amount / 100) * $ob_ticker->$from->last) / $ob_ticker->$to->last;
	}

	return $price;
}

function get_market_price($coinType, $precision = 8)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$ob_ticker = $CI->cache->get('ob_ticker');
	if ($ob_ticker == "") {
		$ob_ticker = json_decode(file_get_contents("https://ticker.openbazaar.org/api"));
	}

	$e = $CI->cache->file->save('ob_ticker', $ob_ticker, 300);
	return $ob_ticker->$coinType->last;
}

function get_http_response_code($url)
{
	$headers = get_headers($url);
	return substr($headers[0], 9, 3);
}

function get_profile($peerID)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$profile_load = $CI->cache->get('profile_' . $peerID);
	if ($profile_load == "") {
		/*
		if(get_http_response_code("https://gateway.ob1.io/ipns/".$peerID."/profile.json") != "200"){
		$profile_load = "{}";
		}else{
		*/
		$ctx = stream_context_create(array(
			'http' => array(
				'timeout' => 1
			)
		));
		$profile_load = @file_get_contents("https://gateway.ob1.io/ipns/" . $peerID . "/profile.json", 0, $ctx);

		// 	    }

		$CI->cache->file->save('profile_' . $peerID, $profile_load, 900); // 15 minutes cache
	}

	return json_decode($profile_load);
}

function get_listings($peerID)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$load = $CI->cache->get('listings_' . $peerID);
	if ($load == "") {
		$ctx = stream_context_create(array(
			'http' => array(
				'timeout' => 1
			)
		));
		$load = @file_get_contents("https://gateway.ob1.io/ipns/" . $peerID . "/listings.json", 0, $ctx);
		if($load != "") {
			$CI->cache->file->save('listings_' . $peerID, $load, 900); // 15 minutes cache
		}		
	}
	
	$load = ($load == "") ? "[]" : $load;

	return json_decode($load);
}

function get_listing($peerID, $slug)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$listing_load = $CI->cache->get('listing_' . $slug);
	if ($listing_load == "") {
		$listing_load = @file_get_contents("https://gateway.ob1.io/ipns/" . $peerID . "/listings/" . $slug . ".json");
		$CI->cache->file->save('listing_' . $slug, $listing_load, 5400); // 60 minutes cache
	}

	return json_decode($listing_load);
}

function contract_type_to_friendly($type)
{
	switch ($type) {
	case "PHYSICAL_GOOD":
		return "Physical Good";
		break;

	case "DIGITAL_GOOD":
		return "Digital Good";
		break;

	case "SERVICE":
		return "Service";
		break;

	case "CRYPTOCURRENCY":
		return "Cryptocurrency";
		break;

	default:
		return "";
	}
}

function condition_to_friendly($condition)
{
	switch ($condition) {
	case "NEW":
		return "New";
		break;

	case "USED_EXCELLENT":
		return "Used - Excellent";
		break;

	case "USED_GOOD":
		return "Used - Good";
		break;

	case "USED_POOR":
		return "Used - Poor";
		break;

	case "REFURBISHED":
		return "Refurbished";
		break;

	default:
		return "";
	}
}

function set_new_url($url_params, $name, $value)
{

	// if(isset($url_params[$name])) {

	$url_params[$name] = urlencode($value);

	// }

	$URI = http_build_query($url_params);
	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	return "//$_SERVER[HTTP_HOST]$uri_parts[0]?$URI";
}

function get_language()
{
	$locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	echo $locale;
}

function ticker_to_symbol($ticker)
{
	$currency_symbols = array(
		'AED' => '&#1583;.&#1573;', // ?
		'BTC' => 'Ƀ',
		'AFN' => '&#65;&#102;',
		'ALL' => '&#76;&#101;&#107;',
		'AMD' => '',
		'ANG' => '&#402;',
		'AOA' => '&#75;&#122;', // ?
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&#402;',
		'AZN' => '&#1084;&#1072;&#1085;',
		'BAM' => '&#75;&#77;',
		'BBD' => '&#36;',
		'BDT' => '&#2547;', // ?
		'BGN' => '&#1083;&#1074;',
		'BHD' => '.&#1583;.&#1576;', // ?
		'BIF' => '&#70;&#66;&#117;', // ?
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => '&#36;&#98;',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTN' => '&#78;&#117;&#46;', // ?
		'BWP' => '&#80;',
		'BYR' => '&#112;&#46;',
		'BZD' => '&#66;&#90;&#36;',
		'CAD' => '&#36;',
		'CDF' => '&#70;&#67;',
		'CHF' => '&#67;&#72;&#70;',
		'CLF' => '', // ?
		'CLP' => '&#36;',
		'CNY' => '&#165;',
		'COP' => '&#36;',
		'CRC' => '&#8353;',
		'CUP' => '&#8396;',
		'CVE' => '&#36;', // ?
		'CZK' => '&#75;&#269;',
		'DJF' => '&#70;&#100;&#106;', // ?
		'DKK' => '&#107;&#114;',
		'DOP' => '&#82;&#68;&#36;',
		'DZD' => '&#1583;&#1580;', // ?
		'EGP' => '&#163;',
		'ETB' => '&#66;&#114;',
		'EUR' => '&#8364;',
		'FJD' => '&#36;',
		'FKP' => '&#163;',
		'GBP' => '&#163;',
		'GEL' => '&#4314;', // ?
		'GHS' => '&#162;',
		'GIP' => '&#163;',
		'GMD' => '&#68;', // ?
		'GNF' => '&#70;&#71;', // ?
		'GTQ' => '&#81;',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => '&#76;',
		'HRK' => '&#107;&#110;',
		'HTG' => '&#71;', // ?
		'HUF' => '&#70;&#116;',
		'IDR' => '&#82;&#112;',
		'ILS' => '&#8362;',
		'INR' => '&#8377;',
		'IQD' => '&#1593;.&#1583;', // ?
		'IRR' => '&#65020;',
		'ISK' => '&#107;&#114;',
		'JEP' => '&#163;',
		'JMD' => '&#74;&#36;',
		'JOD' => '&#74;&#68;', // ?
		'JPY' => '&#165;',
		'KES' => '&#75;&#83;&#104;', // ?
		'KGS' => '&#1083;&#1074;',
		'KHR' => '&#6107;',
		'KMF' => '&#67;&#70;', // ?
		'KPW' => '&#8361;',
		'KRW' => '&#8361;',
		'KWD' => '&#1583;.&#1603;', // ?
		'KYD' => '&#36;',
		'KZT' => '&#1083;&#1074;',
		'LAK' => '&#8365;',
		'LBP' => '&#163;',
		'LKR' => '&#8360;',
		'LRD' => '&#36;',
		'LSL' => '&#76;', // ?
		'LTL' => '&#76;&#116;',
		'LVL' => '&#76;&#115;',
		'LYD' => '&#1604;.&#1583;', // ?
		'MAD' => '&#1583;.&#1605;.', //?
		'MDL' => '&#76;',
		'MGA' => '&#65;&#114;', // ?
		'MKD' => '&#1076;&#1077;&#1085;',
		'MMK' => '&#75;',
		'MNT' => '&#8366;',
		'MOP' => '&#77;&#79;&#80;&#36;', // ?
		'MRO' => '&#85;&#77;', // ?
		'MUR' => '&#8360;', // ?
		'MVR' => '.&#1923;', // ?
		'MWK' => '&#77;&#75;',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => '&#77;&#84;',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => '&#67;&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#65020;',
		'PAB' => '&#66;&#47;&#46;',
		'PEN' => '&#83;&#47;&#46;',
		'PGK' => '&#75;', // ?
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PYG' => '&#71;&#115;',
		'QAR' => '&#65020;',
		'RON' => '&#108;&#101;&#105;',
		'RSD' => '&#1044;&#1080;&#1085;&#46;',
		'RUB' => '&#1088;&#1091;&#1073;',
		'RWF' => '&#1585;.&#1587;',
		'SAR' => '&#65020;',
		'SBD' => '&#36;',
		'SCR' => '&#8360;',
		'SDG' => '&#163;', // ?
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&#163;',
		'SLL' => '&#76;&#101;', // ?
		'SOS' => '&#83;',
		'SRD' => '&#36;',
		'STD' => '&#68;&#98;', // ?
		'SVC' => '&#36;',
		'SYP' => '&#163;',
		'SZL' => '&#76;', // ?
		'THB' => '&#3647;',
		'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
		'TMT' => '&#109;',
		'TND' => '&#1583;.&#1578;',
		'TOP' => '&#84;&#36;',
		'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => '',
		'UAH' => '&#8372;',
		'UGX' => '&#85;&#83;&#104;',
		'USD' => '&#36;',
		'UYU' => '&#36;&#85;',
		'UZS' => '&#1083;&#1074;',
		'VEF' => '&#66;&#115;',
		'VND' => '&#8363;',
		'VUV' => '&#86;&#84;',
		'WST' => '&#87;&#83;&#36;',
		'XAF' => '&#70;&#67;&#70;&#65;',
		'XCD' => '&#36;',
		'XDR' => '',
		'XOF' => '',
		'XPF' => '&#70;',
		'YER' => '&#65020;',
		'ZAR' => '&#82;',
		'ZMK' => '&#90;&#75;', // ?
		'ZWL' => '&#90;&#36;',
	);
	if (!array_key_exists($ticker, $currency_symbols)) {
		return $currency_symbols["BTC"];
	}
	else {
		return $currency_symbols[$ticker];
	}
}

function pretty_price($price, $currency)
{
	$user_currency = (isset($_COOKIE['currency'])) ? $_COOKIE['currency'] : "BTC";
	$symbol = ticker_to_symbol($currency);
	$user_symbol = ticker_to_symbol($user_currency);

	if ($user_currency != "BTC") {
		$amount = money_format('%n', convert_price($price, $currency, $user_currency));
		return $user_symbol . number_format($amount, 2);
	}
	else {
		$amount = preg_replace('/0{1,2}$/', '', number_format(convert_price($price / 100000000, $currency, $user_currency) , 8));
		return $user_symbol . $amount;
	}
}

function sani_input($url) {
	return filter_var($url, FILTER_SANITIZE_STRING);
}

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }

    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

function country_code_to_name($code) {
	$code_matching_dict = array(
	"AF"=> "AFGHANISTAN",
	"AX"=> "ALAND_ISLANDS",
	"AL"=> "ALBANIA",
	"DZ"=> "ALGERIA",
	"AS"=> "AMERICAN_SAMOA",
	"AD"=> "ANDORRA",
	"AO"=> "ANGOLA",
	"AI"=> "ANGUILLA",
	"AG"=> "ANTIGUA",
	"AR"=> "ARGENTINA",
	"AM"=> "ARMENIA",
	"AW"=> "ARUBA",
	"AU"=> "AUSTRALIA",
	"AT"=> "AUSTRIA",
	"AZ"=> "AZERBAIJAN",
	"BS"=> "BAHAMAS",
	"BH"=> "BAHRAIN",
	"BD"=> "BANGLADESH",
	"BB"=> "BARBADOS",
	"BY"=> "BELARUS",
	"BE"=> "BELGIUM",
	"BZ"=> "BELIZE",
	"BJ"=> "BENIN",
	"BM"=> "BERMUDA",
	"BT"=> "BHUTAN",
	"BO"=> "BOLIVIA",
	"BA"=> "BOSNIA",
	"BW"=> "BOTSWANA",
	"BV"=> "BOUVET_ISLAND",
	"BR"=> "BRAZIL",
	"IO"=> "BRITISH_INDIAN_OCEAN_TERRITORY",
	"BN"=> "BRUNEI_DARUSSALAM",
	"BG"=> "BULGARIA",
	"BF"=> "BURKINA_FASO",
	"BI"=> "BURUNDI",
	"CV"=> "CABO_VERDE",
	"KH"=> "CAMBODIA",
	"CM"=> "CAMEROON",
	"CA"=> "CANADA",
	"KY"=> "CAYMAN_ISLANDS",
	"CF"=> "CENTRAL_AFRICAN_REPUBLIC",
	"TD"=> "CHAD",
	"CL"=> "CHILE",
	"CN"=> "CHINA",
	"CX"=> "CHRISTMAS_ISLAND",
	"CC"=> "COCOS_ISLANDS",
	"CO"=> "COLOMBIA",
	"KM"=> "COMOROS",
	"CD"=> "CONGO_REPUBLIC",
	"CG"=> "CONGO",
	"CK"=> "COOK_ISLANDS",
	"CR"=> "COSTA_RICA",
	"CI"=> "COTE_DIVOIRE",
	"HR"=> "CROATIA",
	"CU"=> "CUBA",
	"CW"=> "CURACAO",
	"CY"=> "CYPRUS",
	"CZ"=> "CZECH_REPUBLIC",
	"DK"=> "DENMARK",
	"DJ"=> "DJIBOUTI",
	"DM"=> "DOMINICA",
	"DO"=> "DOMINICAN_REPUBLIC",
	"EC"=> "ECUADOR",
	"EG"=> "EGYPT",
	"SV"=> "EL_SALVADOR",
	"GQ"=> "EQUATORIAL_GUINEA",
	"ER"=> "ERITREA",
	"EE"=> "ESTONIA",
	"ET"=> "ETHIOPIA",
	"FK"=> "FALKLAND_ISLANDS",
	"FO"=> "FAROE_ISLANDS",
	"FJ"=> "FIJI",
	"FI"=> "FINLAND",
	"FR"=> "FRANCE",
	"GF"=> "FRENCH_GUIANA",
	"PF"=> "FRENCH_POLYNESIA",
	"TF"=> "FRENCH_SOUTHERN_TERRITORIES",
	"GA"=> "GABON",
	"GM"=> "GAMBIA",
	"GE"=> "GEORGIA",
	"DE"=> "GERMANY",
	"GH"=> "GHANA",
	"GI"=> "GIBRALTAR",
	"GR"=> "GREECE",
	"GL"=> "GREENLAND",
	"GD"=> "GRENADA",
	"GP"=> "GUADELOUPE",
	"GU"=> "GUAM",
	"GT"=> "GUATEMALA",
	"GG"=> "GUERNSEY",
	"GN"=> "GUINEA",
	"GW"=> "GUINEA_BISSAU",
	"GY"=> "GUYANA",
	"HT"=> "HAITI",
	"VA"=> "HOLY_SEE",
	"HN"=> "HONDURAS",
	"HK"=> "HONG_KONG",
	"HU"=> "HUNGARY",
	"IS"=> "ICELAND",
	"IN"=> "INDIA",
	"ID"=> "INDONESIA",
	"IR"=> "IRAN",
	"IQ"=> "IRAQ",
	"IE"=> "IRELAND",
	"IM"=> "ISLE_OF_MAN",
	"IL"=> "ISRAEL",
	"IT"=> "ITALY",
	"JM"=> "JAMAICA",
	"JP"=> "JAPAN",
	"JE"=> "JERSEY",
	"JO"=> "JORDAN",
	"KZ"=> "KAZAKHSTAN",
	"KE"=> "KENYA",
	"KI"=> "KIRIBATI",
	"KP"=> "NORTH_KOREA",
	"KR"=> "SOUTH_KOREA",
	"KW"=> "KUWAIT",
	"KG"=> "KYRGYZSTAN",
	"LA"=> "LAO",
	"LV"=> "LATVIA",
	"LB"=> "LEBANON",
	"LS"=> "LESOTHO",
	"LR"=> "LIBERIA",
	"LY"=> "LIBYA",
	"LI"=> "LIECHTENSTEIN",
	"LT"=> "LITHUANIA",
	"LU"=> "LUXEMBOURG",
	"MO"=> "MACAO",
	"MK"=> "MACEDONIA",
	"MG"=> "MADAGASCAR",
	"MW"=> "MALAWI",
	"MY"=> "MALAYSIA",
	"MV"=> "MALDIVES",
	"ML"=> "MALI",
	"MT"=> "MALTA",
	"MH"=> "MARSHALL_ISLANDS",
	"MQ"=> "MARTINIQUE",
	"MR"=> "MAURITANIA",
	"MU"=> "MAURITIUS",
	"YT"=> "MAYOTTE",
	"MX"=> "MEXICO",
	"FM"=> "MICRONESIA",
	"MD"=> "MOLDOVA",
	"MC"=> "MONACO",
	"MN"=> "MONGOLIA",
	"ME"=> "MONTENEGRO",
	"MS"=> "MONTSERRAT",
	"MA"=> "MOROCCO",
	"MZ"=> "MOZAMBIQUE",
	"MM"=> "MYANMAR",
	"NA"=> "NAMIBIA",
	"NR"=> "NAURU",
	"NP"=> "NEPAL",
	"NL"=> "NETHERLANDS",
	"NC"=> "NEW_CALEDONIA",
	"NZ"=> "NEW_ZEALAND",
	"NI"=> "NICARAGUA",
	"NE"=> "NIGER",
	"NG"=> "NIGERIA",
	"NU"=> "NIUE",
	"NF"=> "NORFOLK_ISLAND",
	"MP"=> "NORTHERN_MARIANA_ISLANDS",
	"NO"=> "NORWAY",
	"OM"=> "OMAN",
	"PK"=> "PAKISTAN",
	"PW"=> "PALAU",
	"PA"=> "PANAMA",
	"PG"=> "PAPUA_NEW_GUINEA",
	"PY"=> "PARAGUAY",
	"PE"=> "PERU",
	"PH"=> "PHILIPPINES",
	"PN"=> "PITCAIRN",
	"PL"=> "POLAND",
	"PT"=> "PORTUGAL",
	"PR"=> "PUERTO_RICO",
	"QA"=> "QATAR",
	"RE"=> "REUNION",
	"RO"=> "ROMANIA",
	"RU"=> "RUSSIA",
	"RW"=> "RWANDA",
	"BL"=> "SAINT_BARTHELEMY",
	"SH"=> "SAINT_HELENA",
	"KN"=> "SAINT_KITTS",
	"LC"=> "SAINT_LUCIA",
	"MF"=> "SAINT_MARTIN",
	"PM"=> "SAINT_PIERRE",
	"VC"=> "SAINT_VINCENT",
	"WS"=> "SAMOA",
	"SM"=> "SAN_MARINO",
	"ST"=> "SAO_TOME",
	"SA"=> "SAUDI_ARABIA",
	"SN"=> "SENEGAL",
	"RS"=> "SERBIA",
	"SC"=> "SEYCHELLES",
	"SL"=> "SIERRA_LEONE",
	"SG"=> "SINGAPORE",
	"SX"=> "SINT_MAARTEN",
	"SK"=> "SLOVAKIA",
	"SI"=> "SLOVENIA",
	"SB"=> "SOLOMON_ISLANDS",
	"SO"=> "SOMALIA",
	"ZA"=> "SOUTH_AFRICA",
	"SD"=> "SOUTH_SUDAN",
	"ES"=> "SPAIN",
	"LK"=> "SRI_LANKA",
	"SD"=> "SUDAN",
	"SR"=> "SURINAME",
	"SJ"=> "SVALBARD",
	"SZ"=> "SWAZILAND",
	"SE"=> "SWEDEN",
	"CH"=> "SWITZERLAND",
	"SY"=> "SYRIAN_ARAB_REPUBLIC",
	"TW"=> "TAIWAN",
	"TJ"=> "TAJIKISTAN",
	"TZ"=> "TANZANIA",
	"TH"=> "THAILAND",
	"TL"=> "TIMOR_LESTE",
	"TG"=> "TOGO",
	"TK"=> "TOKELAU",
	"TO"=> "TONGA",
	"TT"=> "TRINIDAD",
	"TN"=> "TUNISIA",
	"TR"=> "TURKEY",
	"TM"=> "TURKMENISTAN",
	"TC"=> "TURKS_AND_CAICOS_ISLANDS",
	"TV"=> "TUVALU",
	"UG"=> "UGANDA",
	"UA"=> "UKRAINE",
	"AE"=> "UNITED_ARAB_EMIRATES",
	"UK"=> "UNITED_KINGDOM",
	"US"=> "UNITED_STATES",
	"UY"=> "URUGUAY",
	"UZ"=> "UZBEKISTAN",
	"VU"=> "VANUATU",
	"VE"=> "VENEZUELA",
	"VN"=> "VIETNAM",
	"VG"=> "VIRGIN_ISLANDS_BRITISH",
	"VI"=> "VIRGIN_ISLANDS_US",
	"WF"=> "WALLIS_AND_FUTUNA",
	"EH"=> "WESTERN_SAHARA",
	"YE"=> "YEMEN",
	"ZM"=> "ZAMBIA",
	"ZW"=> "ZIMBABWE"
);

	return $code_matching_dict[$code];

}
