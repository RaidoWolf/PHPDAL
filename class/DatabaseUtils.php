<?php

class DatabaseTools {

    /**
     * DatabaseTools::arrayDepth() Static Method
     *
     * calculates depth of an array (how many nested arrays need/can be traversed)
     *
     * @param array $array - Array on which to calculate depth
     * @return integer - Maximum depth of the array
     */
    public static function arrayDepth (array $array) {

        $maxDepth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::arrayDepth($value) + 1;
                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;

    }

    /**
     * DatabaseTools::isValidHost() Static Method
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

}

?>
