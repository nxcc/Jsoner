<?php

namespace jsoner;

class Resolver
{
	private $config;

	/**
	 * Resolver constructor.
	 * @param \GlobalVarConfig $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	private $DEBUG_STR = <<<END

{
  "_data": [
  {
    "_title": "NORIS-HOUSING-RZ2",
    "begin": "2005-10-11 00:00:00",
    "customer": {
      "_target": "customer",
      "_title": "POP",
      "_url": "http://localhost/crud/customer/1",
      "id": 1
    },
    "end": "2016-02-17 14:30:35",
    "id": 3,
    "info": "Housing-Kunden im RZ2",
    "name": "NORIS-HOUSING-RZ2",
    "timestamp": "2016-02-17 14:30:37",
    "vlan_id": 2,
    "vlancontainer": null,
    "vlanzone": {
      "_target": "vlanzone",
      "_title": "global",
      "_url": "http://localhost/crud/vlanzone/1",
      "id": 1
    }
  },
  {
    "_title": "FHOME-DMZ",
    "begin": "2007-03-11 00:00:00",
    "customer": {
      "_target": "customer",
      "_title": "fsippel",
      "_url": "http://localhost/crud/customer/1650",
      "id": 1650
    },
    "end": null,
    "id": 4,
    "info": null,
    "name": "FHOME-DMZ",
    "timestamp": "2014-03-28 08:31:52",
    "vlan_id": 3,
    "vlancontainer": {
      "_target": "vlancontainer",
      "_title": "FHOME-DMZ",
      "_url": "http://localhost/crud/vlancontainer/967",
      "id": 967
    },
    "vlanzone": {
      "_target": "vlanzone",
      "_title": "global",
      "_url": "http://localhost/crud/vlanzone/1",
      "id": 1
    }
  },
  {
    "_title": "FHOME-ROUTER-NET",
    "begin": "2005-10-11 00:00:00",
    "customer": {
      "_target": "customer",
      "_title": "fsippel",
      "_url": "http://localhost/crud/customer/1650",
      "id": 1650
    },
    "end": null,
    "id": 5,
    "info": null,
    "name": "FHOME-ROUTER-NET",
    "timestamp": "2015-04-01 15:42:40",
    "vlan_id": 4,
    "vlancontainer": {
      "_target": "vlancontainer",
      "_title": "FHOME-ROUTER",
      "_url": "http://localhost/crud/vlancontainer/1592",
      "id": 1592
    },
    "vlanzone": {
      "_target": "vlanzone",
      "_title": "global",
      "_url": "http://localhost/crud/vlanzone/1",
      "id": 1
    }
  }
]
}
END;


	public function resolve( $url ) {
		return $this->DEBUG_STR;
		$ch = curl_init();
		try {
			$user = $this->config->get( "User" );
			$pass = $this->config->get( "Pass" );
			curl_setopt( $ch, CURLOPT_USERPWD, "$user:$pass" );
		} catch ( \ConfigException $ex ) {
			wfDebugLog( 'JSONer', 'Either $jsonerUser or $jsonerPass were not set.'
				. 'Trying unauthenticated.' );
		}

		curl_setopt_array( $ch, [
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => ["Accept: application/json",],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
		] );

		$response = curl_exec( $ch );
		$error_message = curl_error( $ch );
		$error_code = curl_errno( $ch );

		curl_close( $ch );

		if ( $response === false ) {
			throw new CurlException( $error_message, $error_code );
		}

		return $response;
	}
}
