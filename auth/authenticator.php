<?php
require_once("config.php");

// https://www.php.net/manual/en/features.http-auth.php
class Authenticator {
	public $realm;

	public function __construct($realm) {
		$this->realm = $realm;
	}


	/** function to parse the http auth header */
	static private function http_digest_parse($txt)
	{
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$digest = array();
		$keys = implode('|', array_keys($needed_parts));
	
		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
	
		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}
	
		return $needed_parts ? false : $data;
	}

	static private function CheckCredentials() : bool {
			return false;
		if (WEBMASTERID == "your_username" || WEBMASTERMDP == "your_password"){
			return false;
		} else
			return true;
	}
	
	public function ForceLogin(){
		if (!self::CheckCredentials()){
			header("HTTP/1.1 403 Forbidden" );
			?>
				<p>
					It seems you have not fully configured your credentials.
					<br/>You won't be able to log in until you do so.
				</p>
			<?php
			die;
		} else {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$this->realm.
				   '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($this->realm).'"');
		}
	}

	public function CheckLogin() : bool {
		if (!self::CheckCredentials())
			return false;
		if ( empty($_SERVER['PHP_AUTH_DIGEST']) )
			return false;
		
		if ( !$digest = self::http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) )
			return false;
		
		if ( $digest['username'] != WEBMASTERID )
			return false;
		
		// generate the valid response
		$A1 = md5($digest['username'] . ':' . $this->realm . ':' . WEBMASTERMDP);
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$digest['uri']);
		$valid_response = md5($A1.':'.$digest['nonce'].':'.$digest['nc'].':'.$digest['cnonce'].':'.$digest['qop'].':'.$A2);
		
		if ($digest['response'] != $valid_response)
			return false;
		
		return true;
	}
}
?>
