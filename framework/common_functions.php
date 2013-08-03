<?php

/**
 * Namespace autoload function.
 * 
 * @param String $className
 */
function basicAutoLoad($className) {
    $conf = $GLOBALS['config'];
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $delimiter = (strpos($path, DIRECTORY_SEPARATOR) === 0) ? "" : DIRECTORY_SEPARATOR;
    
    // Check registered namespaces
    if (count($conf['autoload']) && is_array($conf['autoload'])) {
        foreach($conf['autoload'] as $nameSpaces => $nameSpaceDir) {
            $nsPos = strpos($className, $nameSpaces);
            if ($nsPos === 0 || $nsPos === 1) {
                require $conf['paths']['vendor'] . $nameSpaceDir . DIRECTORY_SEPARATOR . $delimiter . $path . '.php';
                return;
            }
        }
    }
    
    // Check others
    if (strpos($className, "Controller") > 0) {
        require $conf['paths']['controllers'] . $className . '.php';
    } elseif (strpos($className, "Service") > 0) {
        require $conf['paths']['services'] . $className . '.php';
    } elseif (strpos($className, "Model") > 0) {
        require $conf['paths']['models'] . $className . '.php';
    } elseif (strpos($className, "\\") !== false) {
        require $conf['paths']['vendor'] . $path . '.php';
    } else {
        if (is_file($conf['paths']['framework'] . $className . '.php')) {
            require $conf['paths']['framework'] . $className . '.php';
        }
    }
}


/**
 * Logs param $data to logfile specified in config
 * 
 * @param string $data
 * 
 * @return bool
 */
function logError($data)
{
    if (!$data) {
        return false;
    }

    // Add backtrace
    $backtrace = debug_backtrace();
    $elems = array();
    foreach ($backtrace as $b) {
        if (!in_array($b['function'], array('include', 'require', 'include_once', 'require_once', 'logError', 'errorHandler'))) {
            $bFile = isset($b['file']) ? $b['file'] : "";
            $bLine = isset($b['line']) ? $b['line'] : "";
            if(isset($b['class'])) {
                $elems[] = '[<'. basename($bFile) . '>:' .$bLine . ' ' . $b['class'] . $b['type'] . $b['function'] . '()]';
            } else {
                $elems[] = '[<'. basename($bFile) . '>:' .$bLine . ' ' . $b['function'] . '()]';
            }
        }
    }

    // Add query parameters
    if (isset($_SERVER) && isset($_SERVER["HTTP_HOST"])) {
        $elems[] = "RU: " . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
        if ($_SERVER['QUERY_STRING'] != '') {
            $elems[] = "QS: " . $_SERVER['QUERY_STRING'];
        }
        $elems[] = "UA: " . $_SERVER['HTTP_USER_AGENT'];
    }

    $timestamp = date('[d-M-Y H:i:s]');
    $indent = str_pad(' ', strlen($timestamp) + 1);

    if ($fp = fopen($GLOBALS['config']['paths']['logs'] . 'error.log', 'a')) {
        flock($fp, LOCK_EX);
        fwrite($fp, $timestamp . ' ' . $data . "\n" . $indent . implode("\n" . $indent, $elems)."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }
    
    return false;
}

/**
 *
 * Generic callback to catch standard PHP runtime errors manually triggered with the user_error function.
 *
 * @access private
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 */
function errorHandler($errno, $errstr, $errfile, $errline)
{
	switch($errno) {
        case E_ERROR:case E_USER_ERROR: //Critical error: log and exit 
			logError(sprintf("Error: %s in %s on line %d",  $errstr, $errfile, $errline));
			exit;
			break;

        case E_WARNING:case E_USER_WARNING: // Warning: log and continue
			logError(sprintf("Warning: %s in %s on line %d",  $errstr, $errfile, $errline));
			break;
	}
    return true;
}
