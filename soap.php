<?php

class MultiSoapClient {

    private $session = NULL;
    private $client  = NULL;
    private $version = 2;
    public  $verbose = true;

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
    }

    public function call() {

        $args = array_reverse(func_get_args());

        $module = array_pop($args);
        $method = array_pop($args);

        try {

            switch ($this->version) {

                case 1:

                    $func = "$module.$method";

                    self::note("Calling $func()...");



                    $result = call_user_func_array(
                        array($this->client, "call"),
                        array_merge(
                            array($this->session, $func),
                            array_reverse($args)
                        )
                    );
                    break;
                case 2:

                    $words = explode(" ", str_replace("_", " ", $module . " " . $method));

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
                            array_reverse($args)
                        )
                    );
                    break;
                default:

                    $result = NULL;
                    self::note("Call failed.");
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

    private function note() {

        if ($this->verbose) call_user_func_array("note", func_get_args());
    }
}