<?php

final class DatabaseUtils {

    /**
     * DatabaseUtils::arrayMaxDepth() Static Method
     *
     * calculates depth of an array (how many nested arrays need/can be traversed)
     *
     * @param array $array - Array on which to calculate depth
     * @return integer - Maximum depth of the array
     */
    public static function arrayMaxDepth (array $array) {

        //starting maximum depth is 1
        $maxDepth = 1;

        //detect depth with recursive iteration
        foreach ($array as $value) { //loop each element at this level of the array
            if (is_array($value)) { //for each element that is an array...
                //TODO: Implement stack-depth limiting right here.
                $depth = self::arrayMaxDepth($value) + 1; //recurse this array (call self) and add 1 to its output
                if ($depth > $maxDepth) { //if deeper than previously encountered
                    $maxDepth = $depth; //set maximum depth to current depth
                }
            }
        }

        //return calculated maximum depth
        return $maxDepth;

    }

    /**
     * DatabaseUtils::arrayMinDepth() Static Method
     *
     * calculates the minimum depth of nested arrays in an array
     *
     * @param array $array - Array on which to calculate depth
     * @return integer - Minimum depth of the array
     */
    public static function arrayMinDepth (array $array) {

        //starting minimum depth is 1
        $maxDepth = 1;

        //map depth with recursive iteration and store it in a depth map
        $depthMap = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                //TODO: Implement stack-depth limiting right here.
                $depthMap[] = self::arrayMinDepth($value) + 1;
            } else {
                $depthMap[] = 1;
            }
        }

        //return the minimum in the depth map (the minimum depth of the array)
        return min($depthMap);

    }

    /**
     * DatabaseUtils::arrayPath() Static Method
     *
     * Follows an array of keys that represent the path through a multidimensional
     * array to a value. This function returns a reference to the array element, and
     * therefore, it is also possible to set the value in the given array.
     *
     * NOTE: Because this returns a reference, be careful not to overwrite values
     * unless you intend to do so.
     *
     * @param array $array (required) - the array through which to navigate
     * @param array $path (required) - the path (ordered array of keys) by which to navigate
     * @throws DatabaseException
     * @return unknown - reference to the value found by the path (can literally be anything)
     */
    public static function &arrayPath (array &$array, array $path) {

        //require both arguments
        if (!isset($array) || !isset($path)) {
            throw new DatabaseException(
                __CLASS__,
                __METHOD__.'() failed: missing required argument(s).',
                DatabaseException::EXCEPTION_MISSING_REQUIRED_ARGUMENT
            );
            return null; //in case that exception was caught
        }

        //follow the path into the array
        foreach ($path as $key) { //loop each level of the path
            if (array_key_exists($key, $array)) { //if next path segment exists...
                $current = &$current[$key]; //set $current to the location of the next path segment
            } else { //next path segment doesn't exist...
                throw new DatabaseException(
                    __CLASS__,
                    __METHOD__.'() failed: unable to follow given path, as the path was not found.',
                    DatabaseException::EXCEPTION_INPUT_NOT_VALID
                );
                return null; //in case that exception was caught
            }
        }

        //return the result
        return $current;

    }

    /**
     * DatabaseUtils::isValidHost() Static Method
     *
     * tests if a string is a valid IP address or hostname.
     *
     * @param string $host (required) - hostname/ip-address to test
     * @return boolean - is it valid (true) or not (false)?
     */
    public static function isValidHost ($host) {

        //generate regex to match IPv4 addresses
        $ipv4SegRegex = '(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])';
        $ipv4Regex =    "($ipv4SegRegex\.){3,3}$ipv4SegRegex";

        //generate regex to match IPv6 addresses
        $ipv6SegRegex = '[0-9a-fA-F]{1,4}';
        $ipv6Regex =    "($ipv6SegRegex:){7,7}$ipv6SegRegex|".          // 1:2:3:4:5:6:7:8
                        "($ipv6SegRegex:){1,7}:|".                      // 1::                                          1:2:3:4:5:6:7::
                        "($ipv6SegRegex:){1,6}:$ipv6SegRegex|".         // 1::8                 1:2:3:4:5:6::8          1:2:3:4:5:6::8
                        "($ipv6SegRegex:){1,5}(:$ipv6SegRegex){1,2}|".  // 1::7:8               1:2:3:4:5::7:8          1:2:3:4:5::8
                        "($ipv6SegRegex:){1,4}(:$ipv6SegRegex){1,3}|".  // 1::6:7:8             1:2:3:4::6:7:8          1:2:3:4::8
                        "($ipv6SegRegex:){1,3}(:$ipv6SegRegex){1,4}|".  // 1::5:6:7:8           1:2:3::5:6:7:8          1:2:3::8
                        "($ipv6SegRegex:){1,2}(:$ipv6SegRegex){1,5}|".  // 1::4:5:6:7:8         1:2::4:5:6:7:8          1:2::8
                        "$ipv6SegRegex:((:$ipv6SegRegex){1,6})|".       // 1::3:4:5:6:7:8       1::3:4:5:6:7:8          1::8
                        ":((:$ipv6SegRegex){1,7}|:)|".                  // ::2:3:4:5:6:7:8      ::2:3:4:5:6:7:8         ::8                         ::
                        "fe80:(:$ipv6SegRegex){0,4}%[0-9a-zA-Z]{1,}|".  // fe80::7:8%eth0       fe80::7:8%1             (link-local IPv6 addresses with zone index)
                        "::(ffff(:0{1,4}){0,1}:){0,1}$ipv4Regex|".      // ::255.255.255.255    ::ffff:255.255.255.255  ::ffff:0:255.255.255.255    (IPv4-mapped IPv6 addresses and IPv4-translated addresses)
                        "($ipv6SegRegex:){1,4}:$ipv4Regex";             //Wow, IPv6, way to be hard to validate. This is the most complex regex I've ever written.

        //combine regex to match IPv4 OR IPv6 addresses
        $ipRegex = "(($ipv4Regex)|($ipv6Regex))";

        //try to match IPv4 or IPv6 address with regex
        $ipMatch = preg_match($ipRegex, $host/*, $ipMatchArray*/);
        if ($ipMatch) {
            $validIp = true;
        } else {
            $validIp = false;
        }

        //try to match as a hostname
        $hostRegex = '^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?)*\.?$';
        $hostMatch = preg_match($hostRegex, $host/*, $hostMatchArray*/);
        if ($hostMatch) {
            $validHost = true;
        } else {
            $validHost = false;
        }

        if ($validIp || $validHostname) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * DatabaseUtils::recurseArray() Static Method
     *
     * simple recursive self-invoking script that uses an anonymous function / lambda to
     * execute arbitrary code recursively through an entire array. Do not call this on an
     * array with references to places that contain themselves, or you will cause an
     * infinite recursion, which will run until PHP times out.
     *
     * @param array $array
     * @param callable $function
     */
    public static function recurseArray (array $array, callable $function) {

        foreach ($array as $key => $value) {
            $typeof_value = gettype($value);
            if ($typeof_value == 'array') {
                return self::recurseArray($value, $function);
            } else {
                return $function($value, $key);
            }
        }

    }

    /**
     * DatabaseUtils::replaceOnce() Static Method
     *
     * Replaces the first occurrence of a string within another string. Think of it as
     * str_replace() function with a replacement limit of 1.
     *
     * @param string $needle
     * @param string $replacement
     * @param string $haystack
     * @return string|boolean
     */
    public static function replaceOnce ($needle, $replacement, $haystack) {

        $pos = strpos($haystack,$needle); //get starting position of match

        if ($pos !== false) { //check if we found matches...
            return substr_replace($haystack,$replace,$pos,strlen($needle)); //do the replacement
        } else {
            return false;
        }

    }

}

?>
