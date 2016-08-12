<?php
namespace KYC2Ingest;

class KYC2Ingest_ONA {
	
	private static $apiurl = "https://api.ona.io/api/v1/";

	public function init() {
		// $ona_credentials = get_option("kyc2ingest_ona_credentials");
	}

	public function test() {
		$result = self::performCall("");
		return (!empty($result->osm));
	}

	public function orgs() {
		$result = self::performCall("orgs");
		return $result;
	}

	public function forms() {
		$result = self::performCall("forms");
		return $result;
	}

	public function data($id = false) {
		if ($id) {
			return self::performCall("data/$id");	
		}
		return self::performCall("data");
	}

	private function performCall($action) {
		// $fp = fopen(dirname(__FILE__)."/errorlog-{$action}.txt", 'w+');
		$ona_credentials = get_option("kyc2ingest_ona_credentials");
		$ch = curl_init(self::$apiurl . $action);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $ona_credentials["username"] . ":" . $ona_credentials["password"]);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// curl_setopt($ch, CURLOPT_STDERR, $fp);
		$resp = curl_exec($ch);
		if (curl_errno($ch)) die (curl_error($ch));
		curl_close($ch);
		// fwrite($fp, $resp);
		// fclose($fp);
		$decoded_resp = json_decode($resp);
		return ($decoded_resp);
	}
}