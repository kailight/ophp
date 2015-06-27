<?php
namespace o;

/**
 * Created by: Kai.
 * Date: 15.01.13
 * Time: 17:29
 * Contact: kai@kailight.ru
 */

class oUser extends oTableRow implements StaticInit {

	public $logged = false;
	public $table = 'users';
	public $fields = array(
		'Id',
		'CurrentEmailAddress',
		'CurrentEmailChecked',
		'InviteKey',
		'InviteSent',
		'InvitedAt',
		'NewEmailAddress',
		'NewEmailChecked',
		'NewEmailPassword',
		'NewEmailType',
		'EmailForwarding',
		'TransferHelp',
		'TransferAgree',
		'TransferComplete',
		'TransferRequestedAt',
		'TransferDue',
		'PhoneNumber',
		'SMSUpdates',
		'Created',
		'CurrentStep'
	);


    function __construct() {
        $this->oUser();
    }

    function oUser() {
	    $this->init();
	    $this->checkSession();
	    $this->checkCookie();
    }

	static function init() {

	}


	function isLogged() {
        return $this->logged;
    }


	function generateInviteKey() {

		$this->set('InviteKey', sha1(microtime().$this->CurrentEmailAddress));


	}



	function cleanupEmail($email) {

        $email = htmlspecialchars($email);
        $email = trim($email);
        $email = preg_replace("/[^\w.@-_]/", "", $email);

    return $email;
    }


    function login($key) {
	rec(__CLASS__."login($key)");


	    $query = <<<HEREDOC
SELECT * FROM `{$this->table}` WHERE `InviteKey` LIKE "$key"
HEREDOC;
	    $result = q($query);


	    if ($result) {

		    $row = new oArray($result->get(0));
		    $this->set($row);
		    $this->logged = true;
		    $_SESSION['InviteKey'] = $row->get('InviteKey');
		    $_COOKIE['InviteKey'] = $row->get('InviteKey');

	    return true;
	    }


    return false;
    }







    function IDDQD() {
        self::$logged = true;
    }




    function logout() {
        $this->logged = false;
	    $this->Id = null;
        if (@$_SESSION['InviteKey']) { unset($_SESSION['InviteKey']); }
        if (@$_COOKIE['InviteKey']) { unset($_COOKIE['InviteKey']); }
        // header('location: '.$_SERVER['PHP_SELF'] );
    }



    function checkSession() {
	    if (!$this->logged) {
	        if (@$_SESSION['InviteKey']) {
	            $this->login($_SESSION['InviteKey']);
	        }
	    }
    }


	function checkCookie() {
		if (!$this->logged) {
			if ( @$_COOKIE['InviteKey'] ) {
				$this->login( $_COOKIE['InviteKey'] );
			}
		}
	}



	function toJson() {

		return json_encode ( self::get() );

	}


}
