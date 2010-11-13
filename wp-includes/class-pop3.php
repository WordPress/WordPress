<?php
/**
 * mail_fetch/setup.php
 *
 * @package SquirrelMail
 *
 * @copyright (c) 1999-2006 The SquirrelMail Project Team
 *
 * @copyright (c) 1999 CDI (cdi@thewebmasters.net) All Rights Reserved
 * Modified by Philippe Mingo 2001 mingo@rotedic.com
 * An RFC 1939 compliant wrapper class for the POP3 protocol.
 *
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * pop3 class
 *
 * $Id$
 */

class POP3 {
    var $ERROR      = '';       //  Error string.

    var $TIMEOUT    = 60;       //  Default timeout before giving up on a
                                //  network operation.

    var $COUNT      = -1;       //  Mailbox msg count

    var $BUFFER     = 512;      //  Socket buffer for socket fgets() calls.
                                //  Per RFC 1939 the returned line a POP3
                                //  server can send is 512 bytes.

    var $FP         = '';       //  The connection to the server's
                                //  file descriptor

    var $MAILSERVER = '';       // Set this to hard code the server name

    var $DEBUG      = FALSE;    // set to true to echo pop3
                                // commands and responses to error_log
                                // this WILL log passwords!

    var $BANNER     = '';       //  Holds the banner returned by the
                                //  pop server - used for apop()

    var $ALLOWAPOP  = FALSE;    //  Allow or disallow apop()
                                //  This must be set to true
                                //  manually

    function POP3 ( $server = '', $timeout = '' ) {
        settype($this->BUFFER,"integer");
        if( !empty($server) ) {
            // Do not allow programs to alter MAILSERVER
            // if it is already specified. They can get around
            // this if they -really- want to, so don't count on it.
            if(empty($this->MAILSERVER))
                $this->MAILSERVER = $server;
        }
        if(!empty($timeout)) {
            settype($timeout,"integer");
            $this->TIMEOUT = $timeout;
            if (!ini_get('safe_mode'))
                set_time_limit($timeout);
        }
        return true;
    }

    function update_timer () {
        if (!ini_get('safe_mode'))
            set_time_limit($this->TIMEOUT);
        return true;
    }

    function connect ($server, $port = 110)  {
        //  Opens a socket to the specified server. Unless overridden,
        //  port defaults to 110. Returns true on success, false on fail

        // If MAILSERVER is set, override $server with it's value

	if (!isset($port) || !$port) {$port = 110;}
        if(!empty($this->MAILSERVER))
            $server = $this->MAILSERVER;

        if(empty($server)){
            $this->ERROR = "POP3 connect: " . _("No server specified");
            unset($this->FP);
            return false;
        }

        $fp = @fsockopen("$server", $port, $errno, $errstr);

        if(!$fp) {
            $this->ERROR = "POP3 connect: " . _("Error ") . "[$errno] [$errstr]";
            unset($this->FP);
            return false;
        }

        socket_set_blocking($fp,-1);
        $this->update_timer();
        $reply = fgets($fp,$this->BUFFER);
        $reply = $this->strip_clf($reply);
        if($this->DEBUG)
            error_log("POP3 SEND [connect: $server] GOT [$reply]",0);
        if(!$this->is_ok($reply)) {
            $this->ERROR = "POP3 connect: " . _("Error ") . "[$reply]";
            unset($this->FP);
            return false;
        }
        $this->FP = $fp;
        $this->BANNER = $this->parse_banner($reply);
        return true;
    }

    function user ($user = "") {
        // Sends the USER command, returns true or false

        if( empty($user) ) {
            $this->ERROR = "POP3 user: " . _("no login ID submitted");
            return false;
        } elseif(!isset($this->FP)) {
            $this->ERROR = "POP3 user: " . _("connection not established");
            return false;
        } else {
            $reply = $this->send_cmd("USER $user");
            if(!$this->is_ok($reply)) {
                $this->ERROR = "POP3 user: " . _("Error ") . "[$reply]";
                return false;
            } else
                return true;
        }
    }

    function pass ($pass = "")     {
        // Sends the PASS command, returns # of msgs in mailbox,
        // returns false (undef) on Auth failure

        if(empty($pass)) {
            $this->ERROR = "POP3 pass: " . _("No password submitted");
            return false;
        } elseif(!isset($this->FP)) {
            $this->ERROR = "POP3 pass: " . _("connection not established");
            return false;
        } else {
            $reply = $this->send_cmd("PASS $pass");
            if(!$this->is_ok($reply)) {
                $this->ERROR = "POP3 pass: " . _("Authentication failed") . " [$reply]";
                $this->quit();
                return false;
            } else {
                //  Auth successful.
                $count = $this->last("count");
                $this->COUNT = $count;
                return $count;
            }
        }
    }

    function apop ($login,$pass) {
        //  Attempts an APOP login. If this fails, it'll
        //  try a standard login. YOUR SERVER MUST SUPPORT
        //  THE USE OF THE APOP COMMAND!
        //  (apop is optional per rfc1939)

        if(!isset($this->FP)) {
            $this->ERROR = "POP3 apop: " . _("No connection to server");
            return false;
        } elseif(!$this->ALLOWAPOP) {
            $retVal = $this->login($login,$pass);
            return $retVal;
        } elseif(empty($login)) {
            $this->ERROR = "POP3 apop: " . _("No login ID submitted");
            return false;
        } elseif(empty($pass)) {
            $this->ERROR = "POP3 apop: " . _("No password submitted");
            return false;
        } else {
            $banner = $this->BANNER;
            if( (!$banner) or (empty($banner)) ) {
                $this->ERROR = "POP3 apop: " . _("No server banner") . ' - ' . _("abort");
                $retVal = $this->login($login,$pass);
                return $retVal;
            } else {
                $AuthString = $banner;
                $AuthString .= $pass;
                $APOPString = md5($AuthString);
                $cmd = "APOP $login $APOPString";
                $reply = $this->send_cmd($cmd);
                if(!$this->is_ok($reply)) {
                    $this->ERROR = "POP3 apop: " . _("apop authentication failed") . ' - ' . _("abort");
                    $retVal = $this->login($login,$pass);
                    return $retVal;
                } else {
                    //  Auth successful.
                    $count = $this->last("count");
                    $this->COUNT = $count;
                    return $count;
                }
            }
        }
    }

    function login ($login = "", $pass = "") {
        // Sends both user and pass. Returns # of msgs in mailbox or
        // false on failure (or -1, if the error occurs while getting
        // the number of messages.)

        if( !isset($this->FP) ) {
            $this->ERROR = "POP3 login: " . _("No connection to server");
            return false;
        } else {
            $fp = $this->FP;
            if( !$this->user( $login ) ) {
                //  Preserve the error generated by user()
                return false;
            } else {
                $count = $this->pass($pass);
                if( (!$count) || ($count == -1) ) {
                    //  Preserve the error generated by last() and pass()
                    return false;
                } else
                    return $count;
            }
        }
    }

    function top ($msgNum, $numLines = "0") {
        //  Gets the header and first $numLines of the msg body
        //  returns data in an array with each returned line being
        //  an array element. If $numLines is empty, returns
        //  only the header information, and none of the body.

        if(!isset($this->FP)) {
            $this->ERROR = "POP3 top: " . _("No connection to server");
            return false;
        }
        $this->update_timer();

        $fp = $this->FP;
        $buffer = $this->BUFFER;
        $cmd = "TOP $msgNum $numLines";
        fwrite($fp, "TOP $msgNum $numLines\r\n");
        $reply = fgets($fp, $buffer);
        $reply = $this->strip_clf($reply);
        if($this->DEBUG) {
            @error_log("POP3 SEND [$cmd] GOT [$reply]",0);
        }
        if(!$this->is_ok($reply))
        {
            $this->ERROR = "POP3 top: " . _("Error ") . "[$reply]";
            return false;
        }

        $count = 0;
        $MsgArray = array();

        $line = fgets($fp,$buffer);
        while ( !ereg("^\.\r\n",$line))
        {
            $MsgArray[$count] = $line;
            $count++;
            $line = fgets($fp,$buffer);
            if(empty($line))    { break; }
        }

        return $MsgArray;
    }

    function pop_list ($msgNum = "") {
        //  If called with an argument, returns that msgs' size in octets
        //  No argument returns an associative array of undeleted
        //  msg numbers and their sizes in octets

        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 pop_list: " . _("No connection to server");
            return false;
        }
        $fp = $this->FP;
        $Total = $this->COUNT;
        if( (!$Total) or ($Total == -1) )
        {
            return false;
        }
        if($Total == 0)
        {
            return array("0","0");
            // return -1;   // mailbox empty
        }

        $this->update_timer();

        if(!empty($msgNum))
        {
            $cmd = "LIST $msgNum";
            fwrite($fp,"$cmd\r\n");
            $reply = fgets($fp,$this->BUFFER);
            $reply = $this->strip_clf($reply);
            if($this->DEBUG) {
                @error_log("POP3 SEND [$cmd] GOT [$reply]",0);
            }
            if(!$this->is_ok($reply))
            {
                $this->ERROR = "POP3 pop_list: " . _("Error ") . "[$reply]";
                return false;
            }
            list($junk,$num,$size) = preg_split('/\s+/',$reply);
            return $size;
        }
        $cmd = "LIST";
        $reply = $this->send_cmd($cmd);
        if(!$this->is_ok($reply))
        {
            $reply = $this->strip_clf($reply);
            $this->ERROR = "POP3 pop_list: " . _("Error ") .  "[$reply]";
            return false;
        }
        $MsgArray = array();
        $MsgArray[0] = $Total;
        for($msgC=1;$msgC <= $Total; $msgC++)
        {
            if($msgC > $Total) { break; }
            $line = fgets($fp,$this->BUFFER);
            $line = $this->strip_clf($line);
            if(ereg("^\.",$line))
            {
                $this->ERROR = "POP3 pop_list: " . _("Premature end of list");
                return false;
            }
            list($thisMsg,$msgSize) = preg_split('/\s+/',$line);
            settype($thisMsg,"integer");
            if($thisMsg != $msgC)
            {
                $MsgArray[$msgC] = "deleted";
            }
            else
            {
                $MsgArray[$msgC] = $msgSize;
            }
        }
        return $MsgArray;
    }

    function get ($msgNum) {
        //  Retrieve the specified msg number. Returns an array
        //  where each line of the msg is an array element.

        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 get: " . _("No connection to server");
            return false;
        }

        $this->update_timer();

        $fp = $this->FP;
        $buffer = $this->BUFFER;
        $cmd = "RETR $msgNum";
        $reply = $this->send_cmd($cmd);

        if(!$this->is_ok($reply))
        {
            $this->ERROR = "POP3 get: " . _("Error ") . "[$reply]";
            return false;
        }

        $count = 0;
        $MsgArray = array();

        $line = fgets($fp,$buffer);
        while ( !ereg("^\.\r\n",$line))
        {
            if ( $line[0] == '.' ) { $line = substr($line,1); }
            $MsgArray[$count] = $line;
            $count++;
            $line = fgets($fp,$buffer);
            if(empty($line))    { break; }
        }
        return $MsgArray;
    }

    function last ( $type = "count" ) {
        //  Returns the highest msg number in the mailbox.
        //  returns -1 on error, 0+ on success, if type != count
        //  results in a popstat() call (2 element array returned)

        $last = -1;
        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 last: " . _("No connection to server");
            return $last;
        }

        $reply = $this->send_cmd("STAT");
        if(!$this->is_ok($reply))
        {
            $this->ERROR = "POP3 last: " . _("Error ") . "[$reply]";
            return $last;
        }

        $Vars = preg_split('/\s+/',$reply);
        $count = $Vars[1];
        $size = $Vars[2];
        settype($count,"integer");
        settype($size,"integer");
        if($type != "count")
        {
            return array($count,$size);
        }
        return $count;
    }

    function reset () {
        //  Resets the status of the remote server. This includes
        //  resetting the status of ALL msgs to not be deleted.
        //  This method automatically closes the connection to the server.

        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 reset: " . _("No connection to server");
            return false;
        }
        $reply = $this->send_cmd("RSET");
        if(!$this->is_ok($reply))
        {
            //  The POP3 RSET command -never- gives a -ERR
            //  response - if it ever does, something truely
            //  wild is going on.

            $this->ERROR = "POP3 reset: " . _("Error ") . "[$reply]";
            @error_log("POP3 reset: ERROR [$reply]",0);
        }
        $this->quit();
        return true;
    }

    function send_cmd ( $cmd = "" )
    {
        //  Sends a user defined command string to the
        //  POP server and returns the results. Useful for
        //  non-compliant or custom POP servers.
        //  Do NOT includ the \r\n as part of your command
        //  string - it will be appended automatically.

        //  The return value is a standard fgets() call, which
        //  will read up to $this->BUFFER bytes of data, until it
        //  encounters a new line, or EOF, whichever happens first.

        //  This method works best if $cmd responds with only
        //  one line of data.

        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 send_cmd: " . _("No connection to server");
            return false;
        }

        if(empty($cmd))
        {
            $this->ERROR = "POP3 send_cmd: " . _("Empty command string");
            return "";
        }

        $fp = $this->FP;
        $buffer = $this->BUFFER;
        $this->update_timer();
        fwrite($fp,"$cmd\r\n");
        $reply = fgets($fp,$buffer);
        $reply = $this->strip_clf($reply);
        if($this->DEBUG) { @error_log("POP3 SEND [$cmd] GOT [$reply]",0); }
        return $reply;
    }

    function quit() {
        //  Closes the connection to the POP3 server, deleting
        //  any msgs marked as deleted.

        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 quit: " . _("connection does not exist");
            return false;
        }
        $fp = $this->FP;
        $cmd = "QUIT";
        fwrite($fp,"$cmd\r\n");
        $reply = fgets($fp,$this->BUFFER);
        $reply = $this->strip_clf($reply);
        if($this->DEBUG) { @error_log("POP3 SEND [$cmd] GOT [$reply]",0); }
        fclose($fp);
        unset($this->FP);
        return true;
    }

    function popstat () {
        //  Returns an array of 2 elements. The number of undeleted
        //  msgs in the mailbox, and the size of the mbox in octets.

        $PopArray = $this->last("array");

        if($PopArray == -1) { return false; }

        if( (!$PopArray) or (empty($PopArray)) )
        {
            return false;
        }
        return $PopArray;
    }

    function uidl ($msgNum = "")
    {
        //  Returns the UIDL of the msg specified. If called with
        //  no arguments, returns an associative array where each
        //  undeleted msg num is a key, and the msg's uidl is the element
        //  Array element 0 will contain the total number of msgs

        if(!isset($this->FP)) {
            $this->ERROR = "POP3 uidl: " . _("No connection to server");
            return false;
        }

        $fp = $this->FP;
        $buffer = $this->BUFFER;

        if(!empty($msgNum)) {
            $cmd = "UIDL $msgNum";
            $reply = $this->send_cmd($cmd);
            if(!$this->is_ok($reply))
            {
                $this->ERROR = "POP3 uidl: " . _("Error ") . "[$reply]";
                return false;
            }
            list ($ok,$num,$myUidl) = preg_split('/\s+/',$reply);
            return $myUidl;
        } else {
            $this->update_timer();

            $UIDLArray = array();
            $Total = $this->COUNT;
            $UIDLArray[0] = $Total;

            if ($Total < 1)
            {
                return $UIDLArray;
            }
            $cmd = "UIDL";
            fwrite($fp, "UIDL\r\n");
            $reply = fgets($fp, $buffer);
            $reply = $this->strip_clf($reply);
            if($this->DEBUG) { @error_log("POP3 SEND [$cmd] GOT [$reply]",0); }
            if(!$this->is_ok($reply))
            {
                $this->ERROR = "POP3 uidl: " . _("Error ") . "[$reply]";
                return false;
            }

            $line = "";
            $count = 1;
            $line = fgets($fp,$buffer);
            while ( !ereg("^\.\r\n",$line)) {
                if(ereg("^\.\r\n",$line)) {
                    break;
                }
                list ($msg,$msgUidl) = preg_split('/\s+/',$line);
                $msgUidl = $this->strip_clf($msgUidl);
                if($count == $msg) {
                    $UIDLArray[$msg] = $msgUidl;
                }
                else
                {
                    $UIDLArray[$count] = 'deleted';
                }
                $count++;
                $line = fgets($fp,$buffer);
            }
        }
        return $UIDLArray;
    }

    function delete ($msgNum = "") {
        //  Flags a specified msg as deleted. The msg will not
        //  be deleted until a quit() method is called.

        if(!isset($this->FP))
        {
            $this->ERROR = "POP3 delete: " . _("No connection to server");
            return false;
        }
        if(empty($msgNum))
        {
            $this->ERROR = "POP3 delete: " . _("No msg number submitted");
            return false;
        }
        $reply = $this->send_cmd("DELE $msgNum");
        if(!$this->is_ok($reply))
        {
            $this->ERROR = "POP3 delete: " . _("Command failed ") . "[$reply]";
            return false;
        }
        return true;
    }

    //  *********************************************************

    //  The following methods are internal to the class.

    function is_ok ($cmd = "") {
        //  Return true or false on +OK or -ERR

        if( empty($cmd) )
            return false;
        else
            return( ereg ("^\+OK", $cmd ) );
    }

    function strip_clf ($text = "") {
        // Strips \r\n from server responses

        if(empty($text))
            return $text;
        else {
            $stripped = str_replace("\r",'',$text);
            $stripped = str_replace("\n",'',$stripped);
            return $stripped;
        }
    }

    function parse_banner ( $server_text ) {
        $outside = true;
        $banner = "";
        $length = strlen($server_text);
        for($count =0; $count < $length; $count++)
        {
            $digit = substr($server_text,$count,1);
            if(!empty($digit))             {
                if( (!$outside) && ($digit != '<') && ($digit != '>') )
                {
                    $banner .= $digit;
                }
                if ($digit == '<')
                {
                    $outside = false;
                }
                if($digit == '>')
                {
                    $outside = true;
                }
            }
        }
        $banner = $this->strip_clf($banner);    // Just in case
        return "<$banner>";
    }

}   // End class
?>
