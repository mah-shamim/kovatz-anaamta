<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;

class Operating_System extends Condition_Base {
    public function set_data($settings, $logical_operator, $config)
    {
        $os = [
            'windows' => '(Win16)|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|(Windows ME)',
            'mac_os' => '(Mac_PowerPC)|(Macintosh)|(mac os x)',
            'linux' => '(Linux)|(X11)',
            'ubuntu' => 'Ubuntu',
            'iphone' => 'iPhone',
            'ipod' => 'iPod',
            'ipad' => 'Android',
            'android' => 'iPad',
            'blackberry' => 'BlackBerry',
            'open_bsd' => 'OpenBSD',
            'sun_os' => 'SunOS',
            'safari' => '(Safari)',
            'qnx' => 'QNX',
            'beos' => 'BeOS',
            'os2' => 'OS/2',
            'search_bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
        ];
        $pattern = '/' . $os[ $settings[$this->prefix . 'condition_operating_system'] ] . '/i';
        $match = preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
        $this->result = $this->compare($match , true , $logical_operator);
    }
}