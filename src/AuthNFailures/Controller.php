<?php
namespace AuthNFailures;

class Controller
{

    public $output = null;

    public static $url = '';

    /**
     * Options array
     * Will include $_GET vars
     */
    public $options = array(
        'model'  => false,
        'format' => 'html',
    );

    protected static $auth = false;

    protected static $user;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->authenticate();

        try {
            if (!empty($_POST)) {
                $this->handlePost();
            }
            $this->run();
        } catch (\Exception $e) {
            $this->output = $e;
        }
    }

    /**
     * Log in the current user
     *
     * @return void
     */
    public static function authenticate($logoutonly = false)
    {
        if (isset($_GET['logout'])) {
            self::$auth = \UNL_Auth::factory('SimpleCAS');
            self::$auth->logout();
        }
        if ($logoutonly) {
            return true;
        }

        self::$auth = \UNL_Auth::factory('SimpleCAS');
        self::$auth->login();

        if (!self::$auth->isLoggedIn()) {
            throw new Exception('You must log in to view this resource!', 401);
            exit();
        }

        self::$user = self::$auth->getUser();

        if (false == self::$user) {
            throw new Exception('You\'re not an authenticated user. Talk to an administrator.');
        }

        return self::$user;
    }

    /**
     * Populate the actionable items according to the view map.
     *
     * @throws Exception if view is unregistered
     */
    public function run()
    {
         if (!isset($this->options['model'])
             || false === $this->options['model']) {
             throw new Exception('Un-registered view', 404);
         }

         if (is_callable($this->options['model'])) {
             $this->output = call_user_func($this->options['model'], $this->options);
         } else {
             $this->output = new $this->options['model']($this->options);
         }
    }

    /**
     * get the currently logged in user
     *
     * @return User
     */
    public static function getUser($forceAuth = false)
    {
        if (self::$user) {
            return self::$user;
        }

        if ($forceAuth) {
            self::authenticate();
        } elseif (self::isLoggedIn()) {
            self::$user = User::getByUID(self::$auth->getUser());
        }

        return self::$user;
    }

    public static function isLoggedIn()
    {
        if (self::$auth === null) {
            self::$auth = \UNL_Auth::factory('SimpleCAS');
        }

        return self::$auth->isLoggedIn();
    }

    /**
     * Set the currently logged in user
     *
     * @return User
     */
    public static function setUser(User $user)
    {
        self::$user = $user;
    }

    public function getURL()
    {
        return self::$url;
    }

    /**
     * Add a file extension to the existing URL
     *
     * @param string $url       The URL
     * @param string $extension The file extension to add, e.g. csv
     */
    public static function addURLExtension($url, $extension)
    {
        $extension = trim($extension, '.');

        return preg_replace('/^([^?]+)(\.[\w]+)?(\?.*)?$/', '$1.'.$extension.'$3', $url);
    }

    /**
     * Add unique querystring parameters to a URL
     *
     * @param string $url               The URL
     * @param array  $additional_params Additional querystring parameters to add
     *
     * @return string
     */
    public static function addURLParams($url, $additional_params = array())
    {
        $params = self::getURLParams($url);

        $params = array_merge($params, $additional_params);

        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }

        $url .= '?';

        foreach ($params as $option=>$value) {
            if ($option == 'driver') {
                continue;
            }
            if ($option == 'format'
                && $value == 'html') {
                continue;
            }
            if (isset($value)) {
                if (is_array($value)) {
                    foreach ($value as $arr_value) {
                        $url .= "&{$option}[]=$arr_value";
                    }
                } else {
                    $url .= "&$option=$value";
                }
            }
        }
        $url = str_replace('?&', '?', $url);

        return trim($url, '?;=');
    }

    /**
     * Get an associative array of the querystring parameters in a URL
     *
     * @param string $url
     *
     * @return array
     */
    public static function getURLParams($url)
    {
        $params = array();

        $query = parse_url($url, PHP_URL_QUERY);
        if (!is_null($query)) {
            parse_str($query, $params);
        }

        return $params;
    }

    public function handlePost()
    {
        $handler = new PostHandler($this->options, $_POST, $_FILES);

        return $handler->handle();
    }

    public static function redirect($url, $exit = true)
    {
        header('Location: '.$url);
        if (!defined('CLI')
            && false !== $exit) {
            exit($exit);
        }
    }

}
