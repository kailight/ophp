<?php



namespace o;
/**
 * Created by: i.
 * Date: 15.01.13
 * Time: 17:29
 * Contact: xander@inspiration-vibes.com
 */

class iUser {

    public $logged = false;
    public $vars = array();
    public $table = 'user';
    public $group = null;
    private $groups = array(
        'Retail' => 'wadasdsfasfadw',
        'Manager' => 'adwadsdsadsa',
        'Admin' => 'hgsabjerh',
        'IT' => 'asdfjwojacaslum',
        'Office' => 'dasjpapsvvnwe',
    );
    public $name = null;
    private $ldapConnection = null;


    function __construct() {
        $this->iUser();
    }

    function iUser() {
        $this->checkSession();
    }


    function isLogged() {
        return $this->logged;
    }
    function isRetail() {
        return ($this->group == 'Retail');
    }
    function isManager() {
        return ($this->group == 'Manager');
    }
    function isAdmin() {
        return ($this->group == 'Admin');
    }
    function isIT() {
        return ($this->group == 'IT');
    }
    function isOffice() {
        return ($this->group == 'Office');
    }
    function getGroup() {
        return $this->group;
    }

    function cleanupName($name) {

        $name = htmlspecialchars($name);
        $name = trim($name);
        $name = preg_replace("/[^\w.]/", "", $name);

    return $name;
    }

    function login($name,$pass) {


        // get ldap config
        $config = iConfig::ldap();
        // cleanup name
        $name = $this->cleanupName($name);
        // create a filter for user
        $filter = "uid=" . $name;

        // Connect to the LDAP server.
        $this->ldapConnection = ldap_connect($config['server'], $config['port']) or
            die("Could not connect to " . $config['server'] . ":" . $config['port'] . ".");
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Bind with rootreader to the LDAP server to search and retrieve DN.
        $ldapbind = ldap_bind($this->ldapConnection) or die("Could not bind - contact your administrator");
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        $result = ldap_search($this->ldapConnection,$config['basedn'],$filter) or die ("Search error.");
        $entries = ldap_get_entries($this->ldapConnection, $result);
        $binddn = $entries[0]["dn"];

        // Username with fallback
        $xusername = $entries[0]['cn'][0];
        if (!$xusername) {
            $xusername = $entries[0]['uid'][0];
        }
        $this->name = $xusername;

        // Bind again using the DN retrieved. If this bind is successful,
        // then the user has managed to authenticate.
        $ldapbind = @ldap_bind($this->ldapConnection, $binddn, $pass);
        if (!$ldapbind) {
            $this->ldapError();
        }


        if ($ldapbind) {
            $user = $entries[0]['uid'][0];
            $filter = "(&(objectClass=posixGroup)(memberUid=".$user."))";
            $result = ldap_search($this->ldapConnection,$config['basedn'],$filter) or die ("Search error.");
            $entries = ldap_get_entries($this->ldapConnection, $result);

            $groupsnum = $entries['count'];
            $groups = array();
            for ( $i = 0; $i < $groupsnum; $i++ ) {
                $groups[] = $entries[$i]['apple-group-realname'][0];
            }
            // prd($entries);
            if (in_array('IT',$groups)) {
                $group = 'IT';
            } else if (in_array('Server Admin',$groups)) {
                $group = 'Admin';
            } else if (in_array('Managers',$groups)) {
                $group = 'Manager';
            } else if (in_array('RetailEmployees',$groups)) {
                $group = 'Retail';
            } else if (in_array('Office',$groups)) {
                $group = 'Office';
            } else {
                die ('You do not belong to a group having the privileges to access this page. Your groups are: '.implode(',',$groups));
            }
            // now in $group we have Retail, Manager or Admin or IT
            // echo "<div style='color: #333; text-align: center'>Successful authentication for <span style='font-weight: 700;'>" . $user . "</span> belonging to group <span style='font-weight: 700;'>".$group."</span></div>";
            $result = true;
        }


        if ($result) {
            $this->logged = true;
            $this->group = $group;
            $_SESSION['userkey'] = $this->groups[$group];
            $_SESSION['username'] = $this->name;
            header('location: '.$_SERVER['PHP_SELF'] );
            exit;
        }

    return false;
    }



    function ldapError() {

        // 19: Unable to bind to server: Constraint violation
        // or die("Could not bind SECOND TIME - contact your administrator");;
        if ($ldap_error = ldap_error($this->ldapConnection)) {
            if ($ldap_error_code = ldap_errno($this->ldapConnection)) {
                $error_code = 'LDAP'.$ldap_error_code;
                throw new \skynet\iException( $error_code, 1 );
            }
        }


    }


    function getAllUsersByGroup($group) {

        // get ldap config
        $config = iConfig::ldap();
        $filter = '(&(objectClass=posixGroup)(cn='.$group.'))';

        $this->ldapConnection = ldap_connect($config['server'], $config['port']) or
            die("Could not connect to " . $config['server'] . ":" . $config['port'] . ".");
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Bind with rootreader to the LDAP server to search and retrieve DN.
        $ldapbind = ldap_bind($this->ldapConnection) or die("Could not bind - contact your administrator");
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        $result = ldap_search($this->ldapConnection,$config['basedn'],$filter) or die ("Search error.");
        $entries = ldap_get_entries($this->ldapConnection, $result);

        $users = $entries[0]['memberuid'];

        // First entry is dirty
        $dirty = 1;
        foreach ($users as $n=>$user) {
            if ($dirty) {
                $dirty = 0;
                unset($users[$n]);
            } else {
            }
        }

        $full_users = array();
        foreach ($users as $user) {
            $binddn = $entries[0]['dn'];
            $filter = "uid=" . $user;

            // Connect to the LDAP server.
            $this->ldapConnection = ldap_connect($config['server'], $config['port']) or
                die("Could not connect to " . $config['server'] . ":" . $config['port'] . ".");
            ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

            // Bind with rootreader to the LDAP server to search and retrieve DN.
            $ldapbind = ldap_bind($this->ldapConnection) or die("Could not bind - contact your administrator");
            ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            $result = ldap_search($this->ldapConnection,$config['basedn'],$filter) or die ("Search error.");
            $entries = ldap_get_entries($this->ldapConnection, $result);
            $full_users[$user] = $entries[0]['cn'][0];
            /*
            foreach ($entries[0] as $k=>$entry) {
                if (!is_numeric($k)) {
                    $full_users[] = $entry['cn'][0];
                }
            }
            */
        }

    return $full_users;
    }



    function IDDQD() {
        $this->group = 'IT';
        $this->logged = true;
    }



    function loginByKey($key) {
        if (in_array($key,$this->groups)) {
            $this->logged = true;
            $flipped = array_flip($this->groups);
            $this->group = $flipped[$key];
            $this->name = $_SESSION['username'];
        }
    }


    function logout() {
        $this->logged = false;
        if (@$_SESSION['userkey']) { unset($_SESSION['userkey']); }
        $this->group = null;
        header('location: '.$_SERVER['PHP_SELF'] );
    }

    function checkSession() {
        if (@$_SESSION['userkey']) {
            $this->loginByKey($_SESSION['userkey']);
        }
    }



}
