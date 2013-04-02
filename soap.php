<?php
/**
 * Supports switching soap versions without much effort.
 * Make all calls like you would with version 1, leaving out the session;
 * 
 * $soap = MultiVersionSoapClient("http://example.com/", "user", "password");
 * $soap->call("catalog_product.update", $arg1, $arg2, ...);
 */
class MultiVersionSoapClient {

    private $session = NULL;
    private $client  = NULL;
    private $version = 2;
    public  $verbose = true;

    /**
     * Starts the soap connection
     * 
     * @param string $url
     * @param string $user
     * @param string $pass
     * @param int    $version (Default: 2)
     * @param bool   $verbose (Default: true)
     * 
     * @return MultiVersionSoapClient
     */
    public function __construct($url, $user, $pass, $version = 2, $verbose = true) {

        try {

            $this->verbose = $verbose;
            $this->version = $version;

            self::note("Connecting to $url... (Soap API Version " . $this->version . ")");

            $this->client = new SoapClient($url);

            self::note("Connected.");

            $this->session = $this->client->login($user, $pass);

            self::note("Logged in as user '$user'");
        }
        catch (SoapFault $e) {

            self::note("Connection Error: " . $e->getMessage());
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
                    $func = '';

                    foreach ($words as $key => $word) {

                        if ($key > 0) $func .= ucfirst($word);
                        else $func .= $word;
                    }

                    self::note("Calling $func()...");

                    $result = call_user_func_array(
                        array($this->client, $func),
                        array_merge(
                            array($this->session),
                            $args
                        )
                    );
                    break;
                default:

                    $result = NULL;
            }

            if (is_null($result)) {

                self::note("Call failed.  No handler for Soap API Version " . $this->version);
                return false;
            }

            self::note("Call was completed successfully.");
            return $result;
        }
        catch (Exception $e) {

            self::note("Call failed. " . $e->getMessage());
        }
    }

    /**
     * For my purposes, this calls the note function from my tools.php
     * 
     * @param string $message
     */
    private function note() {

        if ($this->verbose) return call_user_func_array("note", func_get_args());
    }
}