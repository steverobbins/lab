<?php
/**
 * Supports switching soap versions without much effort.
 * Make all calls like you would with version 1, leaving out the session;
 * 
 * $soap = MultiVersionSoapClient("http://example.com/", "user", "password");
 * $soap->call("catalog_product.update", $arg1, $arg2, ...);
 */
class MultiVersionSoapClient {

    private $session;
    private $client;
    private $version;
    public  $verbose;
    /**
     * Starts the soap connection
     * 
     * @param string $url
     * @param string $user
     * @param string $pass
     * @param int    $version (Default: 2)
     * @param bool   $verbose (Default: true)
     * 
     * @return bool|MultiVersionSoapClient
     */
    public function __construct($url, $user = false, $pass = false, $version = 2, $verbose = true) {

        try {

            $this->verbose = $verbose;
            $this->version = $version;

            self::note("Connecting to $url... (Soap API Version " . $this->version . ")");

            $this->client = new SoapClient($url);
            
            self::note("Connected.");

            if ($user && $pass) self::login($user, $pass);
        }
        catch (SoapFault $e) {

            self::note("Connection Error (" . $e->faultcode . "): " . $e->getMessage());
            return false;
        }
        catch (Exception $e) {

            self::note("Connection Error: " . $e->getMessage());
            return false;
        }

        return $this;
    }

    /**
     * Makes the soap call.  Any number of arguments are allowed.
     * The first arg must refer to the method call
     * 
     * @param string $method
     * @param mixed  $args (As many as needed)
     * 
     * @return mixed
     */
    public function call() {

        $args = func_get_args();

        $method = array_shift($args);

        try {

            switch ($this->version) {

                case 1:

                    self::note("Calling $method()...");

                    $result = call_user_func_array(
                        array($this->client, "call"),
                        array_merge(
                            array($this->session, $method),
                            $args
                        )
                    );
                    break;
                case 2:

                    $words = explode(" ", str_replace(array("_", "."), " ", $method));
                    $method = '';

                    foreach ($words as $key => $word) {

                        if ($key > 0) $method .= ucfirst($word);
                        else $method .= $word;
                    }

                    self::note("Calling $method()...");

                    $result = call_user_func_array(
                        array($this->client, $method),
                        array_merge(
                            array($this->session),
                            $args
                        )
                    );
                    break;
                default:

                    self::note("Call failed.  No handler for Soap API Version " . $this->version);
                    return false;
            }

            self::note("Call was completed successfully.");
            return $result;
        }
        catch (SoapFault $e) {

            self::note("Call failed, caught SoapFault (" . $e->faultcode . "): " . $e->getMessage());
        }
        catch (Exception $e) {

            self::note("Call failed: " . $e->getMessage());
        }
    }

    public function login($user, $pass) {

        try {

            $this->session = $this->client->login($user, $pass);

            self::note("Logged in as user '$user'");
        }
        catch (SoapFault $e) {

            self::note("Login Error (" . $e->faultcode . "): " . $e->getMessage());
            return false;
        }
        catch (Exception $e) {

            self::note("Login Error: " . $e->getMessage());
        }
    }

    /**
     * For my purposes, this calls the note function from my tools.php
     * 
     * @param string $message
     * 
     * @return bool|void
     */
    private function note() {

        if ($this->verbose) return call_user_func_array("note", func_get_args());
    }
}
