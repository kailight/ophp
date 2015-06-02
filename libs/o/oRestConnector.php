<?php
/**
 * Created by: i.
 * Date: 17.04.13
 * Time: 12:36
 * Contact: xander@inspiration-vibes.com
 */

namespace o;

class iRestConnector {

    var $startTime = null;
    var $endTime = null;
    var $server = null;

    function iRestConnector() {

    }



    function setStartTime($time) {
        
        $this->startTime = $time;

    }


    function setEndTime($time) {

        $this->endTime = $time;

    }


    function setServer($server) {

        if (!$this->server) {
            rec('Setting server to '.$server);
        }
        if ($this->server != $server) {
            rec('Changing server to '.$server);
        }

    $this->server = $server;
    }



    function runGet($item,$param1=null,$param2=null) {
        info('iRestConnector::runGet()');

        $servers = iSettings::getServers();
        $config = $servers[$this->server];


        $filter = '';
        if ($item == 'invoices') {
            $startdate = $this->startTime->format('Y-m-d H:i:s');
            $enddate = $this->endTime->format('Y-m-d H:i:s');
            $filter = 'date_cre >= "'.$startdate.'" AND date_cre <= "'.$enddate.'"';
            $loc = 'invoices';
            // $filter = '?filter='.rawurlencode('(date_cre >= "2013-03-01" AND date_cre <= "2013-03-01")');
        }
        else if ($item == 'invoice') {
            $loc = 'invoices/'.$param1;
        }
        else if ($item == 'lineitems') {
            $loc = 'invoices/'.$param1.'/lineitems';
        }
        else if ($item == 'lineitem') {
            $loc = 'invoices/'.$param1.'/lineitems/'.$param2;
        }
        else if ($item == 'payments') {
            $loc = 'invoices/'.$param1.'/payments';
        }
        else if ($item == 'payment') {
            $loc = 'invoices/'.$param1.'/payments/'.$param2;
        }
        else if ($item == 'customer') {
            $loc = 'customers/'.$param1;
        }
        else if ($item == 'products') {
            $loc = 'products';
            if ($param1 === false) {
                $filter = 'inventoried=0';
            }
        }
        else if ($item == 'product') {
            $loc = 'products/'.$param1;
        }
        else if ($item == 'customers') {
            $startdate = $this->startTime->format('Y-m-d H:i:s');
            $enddate = $this->endTime->format('Y-m-d H:i:s');
            $filter = 'date_cre >= "'.$startdate.'" AND date_cre <= "'.$enddate.'"';
            $loc = 'customers';
        }
        else if ($item == 'photo') {
            $loc = 'products/'.$param1.'/product_photos/'.$param2.'/image';
        }
        else if ($item == 'photos') {
            $loc = 'products/'.$param1.'/product_photos';
        }
        else if ($item == 'photo_info') {
            $loc = 'products/'.$param1.'/product_photos/'.$param2.'';
        }
        else if ($item == 'users') {
            $loc = 'users';
        }
        else {
            error('Unknown item '.$item);
            return false;
        }

        if ($filter) {
            $filter = '?filter='.rawurlencode('('.$filter.')');
        }
        // @todo hardcoded
        $filter = '';
        $location = $config['address'].'/api/';
        $location .= $loc.'/'.$filter;

        if ($item == 'product') {
            $_location = rawurldecode($location);
            info("Created curl GET request for $item#$param1: $_location");
        } else {
            rec('Created request for location '.$location);
        }


        try {
            $curl = $this->initCurl($location);
        } catch (\Exception $e) {
            throw $e;
            // do nothing
            /*
            if (stristr($error,"couldn't connect to host")) {
                warning('REST_CANT_CONNECT',array($this->server));
            }
            */
        }

        if (curl_error($curl)) {
            $error = curl_error($curl);
            if (stristr($error,'<url> malformed')) {
                warning("URL '.$location.' is malformed");
            }
            // in case of this error - repeat
            elseif (stristr($error,'Unknown SSL protocol error in connection')) {
                return $this->runGet($item,$param1,$param2);
            }
            else {
                error('CURL',array($error));
            }
        }

        if ($item == 'photo') {
            $binary_data = curl_exec($curl);
            return $binary_data;
        } else {
            $xml = curl_exec($curl);
        }

        $data = xml2array($xml);
        if (is_array($data['localizable_message']) && $data['localizable_message']['plain_message']) {
            error($data['localizable_message']['plain_message']);
            return false;
        }
        // pr($xml);

    return $xml;
    }



    function lock($item,$id1,$id2=null) {

        $servers = iSettings::getServers();
        $config = $servers[$this->server];

        if ($item == 'customer') {
            $item = 'customers/'.$id1;
        }
        if ($item == 'product') {
            $item = 'products/'.$id1;
        }
        if ($item == 'photo') {
            $item = 'products/'.$id1;
        }
        if ($item == 'invoice') {
            $item = 'invoices/'.$id1;
        }
        $location = $config['address'].'/api/'.$item.'/';
        rec('resource '.rawurldecode($location).' is locked');


        $curl = $this->initCurl($location);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'LOCK');
        $xml = curl_exec($curl) or error(curl_error($curl));

    return $xml;
    }



    function unlock($item,$id1,$id2=null) {

        $servers = iSettings::getServers();
        $config = $servers[$this->server];

        if ($item == 'customer') {
            $item = 'customers/'.$id1;
        }
        if ($item == 'product') {
            $item = 'products/'.$id1;
        }
        if ($item == 'photo') {
            $item = 'products/'.$id1;
        }
        if ($item == 'invoice') {
            $item = 'invoices/'.$id1;
        }
        $location = $config['address'].'/api/'.$item.'/';
        rec('resource '.rawurldecode($location).' is unlocked');

        $curl = $this->initCurl($location);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'UNLOCK');
        $xml = curl_exec($curl) or error(curl_error($curl));

    return $xml;
    }



    function initCurl($url) {

        $servers = iSettings::getServers();
        $config = $servers[$this->server];


        $curl = curl_init();


        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERPWD, "{$config['user']}:{$config['pass']}");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "User-Agent : {$config['public.appid']}/1.0",
                "X-PAPPID : {$config['private.appid']}",
            )
        );
        if (!@$_SESSION['LS_SERVER_SESSION_ID']) {
            curl_setopt($curl, CURLOPT_HEADER, 1);
            try {
                $response = curl_exec($curl);
            }
            catch (\Exception $e) {

            }
            if (!$response) {
                $error = curl_error($curl);
                if ((string) $error == "couldn't connect to host") {
                    warning('REST_CANT_CONNECT',array($this->server));
                }
                else {
                    error('CURL',array($error));
                }
            }
            // get cookie
            preg_match('/^Set-Cookie:\s*([^;]*)/mi', $response, $m);
            @parse_str($m[1], $cookies);
            $_SESSION['LS_SERVER_SESSION_ID'] = $cookies['LS_SERVER_SESSION_ID'];
        }
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_COOKIE, 'LS_SERVER_SESSION_ID='.@$_SESSION['LS_SERVER_SESSION_ID']);


    return $curl;
    }


    function logout() {

        $this->runPost('logout',null);

    }


    function runPost($item,$data=null,$id1=null) {

        $servers = iSettings::getServers();
        $config = $servers[$this->server];

        if ($item == 'logout') {
            $path = 'sessions/current/logout';
            rec('Logging out from '.$config['name']);
        }
        if ($item == 'customer') {
            $path = 'customers';
            rec('Creating new customer');
        }
        if ($item == 'product') {
            $path = 'products';
            rec('Creating new product');
        }
        if ($item == 'invoice') {
            $path = 'invoices';
            rec('Creating new invoice');
        }
        if ($item == 'lineitem') {
            $path = 'invoices/'.$id1.'/lineitems';
            rec('Adding product to invoice');
        }
        if ($item == 'photo') {
            $path = 'products/'.$id1.'/add_product_photo';
            rec('Creating new photo for product #'.$id1);
        }
        $location = $config['address'].'/api/'.$path.'/';

        rec('Creating curl POST request to create '.$item.' at '.$location);

        $curl = $this->initCurl($location);
        curl_setopt($curl, CURLOPT_POST, TRUE);

        if ($item == 'photo') {
            $this->lock('product',$id1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $xml = curl_exec($curl) or error(curl_error($curl));
            $this->unlock('product',$id1);
            return $xml;
        }
        if ($item == 'lineitem') {
            $this->lock('invoice',$id1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $xml = curl_exec($curl) or error(curl_error($curl));
            $this->unlock('invoice',$id1);
            return $xml;
        }
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        }
        else {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        }
        $xml = curl_exec($curl) or error(curl_error($curl));

    return $xml;
    }



    function runPut($item,$id1,$id2,$data) {
        rec('Creating curl PUT request for '.$item);

        $servers = iSettings::getServers();
        $config = $servers[$this->server];

        if ($item == 'customer') {
            $path = 'customers/'.$id1;
        }
        if ($item == 'product') {
            $path = 'products/'.$id1;
        }
        if ($item == 'photo') {
            $path = 'products/'.$id1.'/product_photos/'.$id2.'/image';
        }
        $location = $config['address'].'/api/'.$path.'/';


        $this->lock($item,$id1,$id2);

        rec('Sending PUT request to location '.rawurldecode($location));

        $curl = $this->initCurl($location);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // In case of photo need different approach
        if ($item == 'photo') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $xml = curl_exec($curl) or error(curl_error($curl));
            return $xml;
        }

        $xml = curl_exec($curl) or error(curl_error($curl));
        // prd($xml);
        $this->unlock($item,$id1,$id2);

    return $xml;
    }





}
