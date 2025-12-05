<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Parser;

use DeviceDetector\ClientHints;
/**
 * Class OperatingSystem
 *
 * Parses the useragent for operating system information
 *
 * Detected operating systems can be found in self::$operatingSystems and /regexes/oss.yml
 * This class also defined some operating system families and methods to get the family for a specific os
 */
class OperatingSystem extends \DeviceDetector\Parser\AbstractParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/oss.yml';
    /**
     * @var string
     */
    protected $parserName = 'os';
    /**
     * Known operating systems mapped to their internal short codes
     *
     * @var array
     */
    protected static $operatingSystems = ['AIX' => 'AIX', 'AND' => 'Android', 'ADR' => 'Android TV', 'ALP' => 'Alpine Linux', 'AMZ' => 'Amazon Linux', 'AMG' => 'AmigaOS', 'ARM' => 'Armadillo OS', 'ARO' => 'AROS', 'ATV' => 'tvOS', 'ARL' => 'Arch Linux', 'AOS' => 'AOSC OS', 'ASP' => 'ASPLinux', 'AZU' => 'Azure Linux', 'BTR' => 'BackTrack', 'SBA' => 'Bada', 'BYI' => 'Baidu Yi', 'BEO' => 'BeOS', 'BLB' => 'BlackBerry OS', 'QNX' => 'BlackBerry Tablet OS', 'PAN' => 'blackPanther OS', 'BOS' => 'Bliss OS', 'BMP' => 'Brew', 'BSN' => 'BrightSignOS', 'CAI' => 'Caixa Mágica', 'CES' => 'CentOS', 'CST' => 'CentOS Stream', 'CLO' => 'Clear Linux OS', 'CLR' => 'ClearOS Mobile', 'COS' => 'Chrome OS', 'CRS' => 'Chromium OS', 'CHN' => 'China OS', 'COL' => 'Coolita OS', 'CYN' => 'CyanogenMod', 'DEB' => 'Debian', 'DEE' => 'Deepin', 'DFB' => 'DragonFly', 'DVK' => 'DVKBuntu', 'ELE' => 'ElectroBSD', 'EUL' => 'EulerOS', 'FED' => 'Fedora', 'FEN' => 'Fenix', 'FOS' => 'Firefox OS', 'FIR' => 'Fire OS', 'FOR' => 'Foresight Linux', 'FRE' => 'Freebox', 'BSD' => 'FreeBSD', 'FRI' => 'FRITZ!OS', 'FYD' => 'FydeOS', 'FUC' => 'Fuchsia', 'GNT' => 'Gentoo', 'GNX' => 'GENIX', 'GEO' => 'GEOS', 'GNS' => 'gNewSense', 'GRI' => 'GridOS', 'GTV' => 'Google TV', 'HPX' => 'HP-UX', 'HAI' => 'Haiku OS', 'IPA' => 'iPadOS', 'HAR' => 'HarmonyOS', 'HAS' => 'HasCodingOS', 'HEL' => 'HELIX OS', 'IRI' => 'IRIX', 'INF' => 'Inferno', 'JME' => 'Java ME', 'JOL' => 'Joli OS', 'KOS' => 'KaiOS', 'KAL' => 'Kali', 'KAN' => 'Kanotix', 'KIN' => 'KIN OS', 'KNO' => 'Knoppix', 'KTV' => 'KreaTV', 'KBT' => 'Kubuntu', 'LIN' => 'GNU/Linux', 'LEA' => 'LeafOS', 'LND' => 'LindowsOS', 'LNS' => 'Linspire', 'LEN' => 'Lineage OS', 'LIR' => 'Liri OS', 'LOO' => 'Loongnix', 'LBT' => 'Lubuntu', 'LOS' => 'Lumin OS', 'LUN' => 'LuneOS', 'VLN' => 'VectorLinux', 'MAC' => 'Mac', 'MAE' => 'Maemo', 'MAG' => 'Mageia', 'MDR' => 'Mandriva', 'SMG' => 'MeeGo', 'MET' => 'Meta Horizon', 'MCD' => 'MocorDroid', 'MON' => 'moonOS', 'EZX' => 'Motorola EZX', 'MIN' => 'Mint', 'MLD' => 'MildWild', 'MOR' => 'MorphOS', 'NBS' => 'NetBSD', 'MTK' => 'MTK / Nucleus', 'MRE' => 'MRE', 'NXT' => 'NeXTSTEP', 'NWS' => 'NEWS-OS', 'WII' => 'Nintendo', 'NDS' => 'Nintendo Mobile', 'NOV' => 'Nova', 'OS2' => 'OS/2', 'T64' => 'OSF1', 'OBS' => 'OpenBSD', 'OVS' => 'OpenVMS', 'OVZ' => 'OpenVZ', 'OWR' => 'OpenWrt', 'OTV' => 'Opera TV', 'ORA' => 'Oracle Linux', 'ORD' => 'Ordissimo', 'PAR' => 'Pardus', 'PCL' => 'PCLinuxOS', 'PIC' => 'PICO OS', 'PLA' => 'Plasma Mobile', 'PSP' => 'PlayStation Portable', 'PS3' => 'PlayStation', 'PVE' => 'Proxmox VE', 'PUF' => 'Puffin OS', 'PUR' => 'PureOS', 'QTP' => 'Qtopia', 'PIO' => 'Raspberry Pi OS', 'RAS' => 'Raspbian', 'RHT' => 'Red Hat', 'RST' => 'Red Star', 'RED' => 'RedOS', 'REV' => 'Revenge OS', 'RIS' => 'risingOS', 'ROS' => 'RISC OS', 'ROC' => 'Rocky Linux', 'ROK' => 'Roku OS', 'RSO' => 'Rosa', 'ROU' => 'RouterOS', 'REM' => 'Remix OS', 'RRS' => 'Resurrection Remix OS', 'REX' => 'REX', 'RZD' => 'RazoDroiD', 'RXT' => 'RTOS & Next', 'SAB' => 'Sabayon', 'SSE' => 'SUSE', 'SAF' => 'Sailfish OS', 'SCI' => 'Scientific Linux', 'SEE' => 'SeewoOS', 'SER' => 'SerenityOS', 'SIR' => 'Sirin OS', 'SLW' => 'Slackware', 'SOS' => 'Solaris', 'SBL' => 'Star-Blade OS', 'SYL' => 'Syllable', 'SYM' => 'Symbian', 'SYS' => 'Symbian OS', 'S40' => 'Symbian OS Series 40', 'S60' => 'Symbian OS Series 60', 'SY3' => 'Symbian^3', 'TEN' => 'TencentOS', 'TDX' => 'ThreadX', 'TIZ' => 'Tizen', 'TIV' => 'TiVo OS', 'TOS' => 'TmaxOS', 'TUR' => 'Turbolinux', 'UBT' => 'Ubuntu', 'ULT' => 'ULTRIX', 'UOS' => 'UOS', 'VID' => 'VIDAA', 'VIZ' => 'ViziOS', 'WAS' => 'watchOS', 'WER' => 'Wear OS', 'WTV' => 'WebTV', 'WHS' => 'Whale OS', 'WIN' => 'Windows', 'WCE' => 'Windows CE', 'WIO' => 'Windows IoT', 'WMO' => 'Windows Mobile', 'WPH' => 'Windows Phone', 'WRT' => 'Windows RT', 'WPO' => 'WoPhone', 'XBX' => 'Xbox', 'XBT' => 'Xubuntu', 'YNS' => 'YunOS', 'ZEN' => 'Zenwalk', 'ZOR' => 'ZorinOS', 'IOS' => 'iOS', 'POS' => 'palmOS', 'WEB' => 'Webian', 'WOS' => 'webOS'];
    /**
     * Operating system families mapped to the short codes of the associated operating systems
     *
     * @var array
     */
    protected static $osFamilies = ['Android' => ['AND', 'CYN', 'FIR', 'REM', 'RZD', 'MLD', 'MCD', 'YNS', 'GRI', 'HAR', 'ADR', 'CLR', 'BOS', 'REV', 'LEN', 'SIR', 'RRS', 'WER', 'PIC', 'ARM', 'HEL', 'BYI', 'RIS', 'PUF', 'LEA', 'MET'], 'AmigaOS' => ['AMG', 'MOR', 'ARO'], 'BlackBerry' => ['BLB', 'QNX'], 'Brew' => ['BMP'], 'BeOS' => ['BEO', 'HAI'], 'Chrome OS' => ['COS', 'CRS', 'FYD', 'SEE'], 'Firefox OS' => ['FOS', 'KOS'], 'Gaming Console' => ['WII', 'PS3'], 'Google TV' => ['GTV'], 'IBM' => ['OS2'], 'iOS' => ['IOS', 'ATV', 'WAS', 'IPA'], 'RISC OS' => ['ROS'], 'GNU/Linux' => ['LIN', 'ARL', 'DEB', 'KNO', 'MIN', 'UBT', 'KBT', 'XBT', 'LBT', 'FED', 'RHT', 'VLN', 'MDR', 'GNT', 'SAB', 'SLW', 'SSE', 'CES', 'BTR', 'SAF', 'ORD', 'TOS', 'RSO', 'DEE', 'FRE', 'MAG', 'FEN', 'CAI', 'PCL', 'HAS', 'LOS', 'DVK', 'ROK', 'OWR', 'OTV', 'KTV', 'PUR', 'PLA', 'FUC', 'PAR', 'FOR', 'MON', 'KAN', 'ZEN', 'LND', 'LNS', 'CHN', 'AMZ', 'TEN', 'CST', 'NOV', 'ROU', 'ZOR', 'RED', 'KAL', 'ORA', 'VID', 'TIV', 'BSN', 'RAS', 'UOS', 'PIO', 'FRI', 'LIR', 'WEB', 'SER', 'ASP', 'AOS', 'LOO', 'EUL', 'SCI', 'ALP', 'CLO', 'ROC', 'OVZ', 'PVE', 'RST', 'EZX', 'GNS', 'JOL', 'TUR', 'QTP', 'WPO', 'PAN', 'VIZ', 'AZU', 'COL'], 'Mac' => ['MAC'], 'Mobile Gaming Console' => ['PSP', 'NDS', 'XBX'], 'OpenVMS' => ['OVS'], 'Real-time OS' => ['MTK', 'TDX', 'MRE', 'JME', 'REX', 'RXT'], 'Other Mobile' => ['WOS', 'POS', 'SBA', 'TIZ', 'SMG', 'MAE', 'LUN', 'GEO'], 'Symbian' => ['SYM', 'SYS', 'SY3', 'S60', 'S40'], 'Unix' => ['SOS', 'AIX', 'HPX', 'BSD', 'NBS', 'OBS', 'DFB', 'SYL', 'IRI', 'T64', 'INF', 'ELE', 'GNX', 'ULT', 'NWS', 'NXT', 'SBL'], 'WebTV' => ['WTV'], 'Windows' => ['WIN'], 'Windows Mobile' => ['WPH', 'WMO', 'WCE', 'WRT', 'WIO', 'KIN'], 'Other Smart TV' => ['WHS']];
    /**
     * Contains a list of mappings from OS names we use to known client hint values
     *
     * @var array<string, array<string>>
     */
    protected static $clientHintMapping = ['GNU/Linux' => ['Linux'], 'Mac' => ['MacOS']];
    /**
     * Operating system families that are known as desktop only
     *
     * @var array
     */
    protected static $desktopOsArray = ['AmigaOS', 'IBM', 'GNU/Linux', 'Mac', 'Unix', 'Windows', 'BeOS', 'Chrome OS', 'Chromium OS'];
    /**
     * Fire OS version mapping
     *
     * @var array
     */
    private $fireOsVersionMapping = ['11' => '8', '10' => '8', '9' => '7', '7' => '6', '5' => '5', '4.4.3' => '4.5.1', '4.4.2' => '4', '4.2.2' => '3', '4.0.3' => '3', '4.0.2' => '3', '4' => '2', '2' => '1'];
    /**
     * Lineage OS version mapping
     *
     * @var array
     */
    private $lineageOsVersionMapping = ['15' => '22', '14' => '21', '13' => '20.0', '12.1' => '19.1', '12' => '19.0', '11' => '18.0', '10' => '17.0', '9' => '16.0', '8.1.0' => '15.1', '8.0.0' => '15.0', '7.1.2' => '14.1', '7.1.1' => '14.1', '7.0' => '14.0', '6.0.1' => '13.0', '6.0' => '13.0', '5.1.1' => '12.1', '5.0.2' => '12.0', '5.0' => '12.0', '4.4.4' => '11.0', '4.3' => '10.2', '4.2.2' => '10.1', '4.0.4' => '9.1.0'];
    /**
     * Returns all available operating systems
     *
     * @return array
     */
    public static function getAvailableOperatingSystems() : array
    {
        return self::$operatingSystems;
    }
    /**
     * Returns all available operating system families
     *
     * @return array
     */
    public static function getAvailableOperatingSystemFamilies() : array
    {
        return self::$osFamilies;
    }
    /**
     * Returns the os name and shot name
     *
     * @param string $name
     *
     * @return array
     */
    public static function getShortOsData(string $name) : array
    {
        $short = 'UNK';
        foreach (self::$operatingSystems as $osShort => $osName) {
            if (\strtolower($name) !== \strtolower($osName)) {
                continue;
            }
            $name = $osName;
            $short = $osShort;
            break;
        }
        return \compact('short', 'name');
    }
    /**
     * @inheritdoc
     */
    public function parse() : ?array
    {
        $this->restoreUserAgentFromClientHints();
        $osFromClientHints = $this->parseOsFromClientHints();
        $osFromUserAgent = $this->parseOsFromUserAgent();
        if (!empty($osFromClientHints['name'])) {
            $name = $osFromClientHints['name'];
            $version = $osFromClientHints['version'];
            // use version from user agent if non was provided in client hints, but os family from useragent matches
            if (empty($version) && self::getOsFamily($name) === self::getOsFamily($osFromUserAgent['name'])) {
                $version = $osFromUserAgent['version'];
            }
            // On Windows, version 0.0.0 can be either 7, 8 or 8.1
            if ('Windows' === $name && '0.0.0' === $version) {
                $version = '10' === $osFromUserAgent['version'] ? '' : $osFromUserAgent['version'];
            }
            // If the OS name detected from client hints matches the OS family from user agent
            // but the os name is another, we use the one from user agent, as it might be more detailed
            if (self::getOsFamily($osFromUserAgent['name']) === $name && $osFromUserAgent['name'] !== $name) {
                $name = $osFromUserAgent['name'];
                if ('LeafOS' === $name || 'HarmonyOS' === $name) {
                    $version = '';
                }
                if ('PICO OS' === $name) {
                    $version = $osFromUserAgent['version'];
                }
                if ('Fire OS' === $name && !empty($osFromClientHints['version'])) {
                    $majorVersion = (int) (\explode('.', $version, 1)[0] ?? '0');
                    $version = $this->fireOsVersionMapping[$version] ?? $this->fireOsVersionMapping[$majorVersion] ?? '';
                }
            }
            $short = $osFromClientHints['short_name'];
            // Chrome OS is in some cases reported as Linux in client hints, we fix this only if the version matches
            if ('GNU/Linux' === $name && 'Chrome OS' === $osFromUserAgent['name'] && $osFromClientHints['version'] === $osFromUserAgent['version']) {
                $name = $osFromUserAgent['name'];
                $short = $osFromUserAgent['short_name'];
            }
            // Chrome OS is in some cases reported as Android in client hints
            if ('Android' === $name && 'Chrome OS' === $osFromUserAgent['name']) {
                $name = $osFromUserAgent['name'];
                $version = '';
                $short = $osFromUserAgent['short_name'];
            }
            // Meta Horizon is reported as Linux in client hints
            if ('GNU/Linux' === $name && 'Meta Horizon' === $osFromUserAgent['name']) {
                $name = $osFromUserAgent['name'];
                $short = $osFromUserAgent['short_name'];
            }
        } elseif (!empty($osFromUserAgent['name'])) {
            $name = $osFromUserAgent['name'];
            $version = $osFromUserAgent['version'];
            $short = $osFromUserAgent['short_name'];
        } else {
            return [];
        }
        $platform = $this->parsePlatform();
        $family = self::getOsFamily($short);
        $androidApps = ['com.hisense.odinbrowser', 'com.seraphic.openinet.pre', 'com.appssppa.idesktoppcbrowser', 'every.browser.inc'];
        if (null !== $this->clientHints) {
            if (\in_array($this->clientHints->getApp(), $androidApps) && 'Android' !== $name) {
                $name = 'Android';
                $family = 'Android';
                $short = 'ADR';
                $version = '';
            }
            if ('org.lineageos.jelly' === $this->clientHints->getApp() && 'Lineage OS' !== $name) {
                $majorVersion = (int) (\explode('.', $version, 1)[0] ?? '0');
                $name = 'Lineage OS';
                $family = 'Android';
                $short = 'LEN';
                $version = $this->lineageOsVersionMapping[$version] ?? $this->lineageOsVersionMapping[$majorVersion] ?? '';
            }
            if ('org.mozilla.tv.firefox' === $this->clientHints->getApp() && 'Fire OS' !== $name) {
                $majorVersion = (int) (\explode('.', $version, 1)[0] ?? '0');
                $name = 'Fire OS';
                $family = 'Android';
                $short = 'FIR';
                $version = $this->fireOsVersionMapping[$version] ?? $this->fireOsVersionMapping[$majorVersion] ?? '';
            }
        }
        $return = ['name' => $name, 'short_name' => $short, 'version' => $version, 'platform' => $platform, 'family' => $family];
        if (\in_array($return['name'], self::$operatingSystems)) {
            $return['short_name'] = \array_search($return['name'], self::$operatingSystems);
        }
        return $return;
    }
    /**
     * Returns the operating system family for the given operating system
     *
     * @param string $osLabel name or short name
     *
     * @return string|null If null, "Unknown"
     */
    public static function getOsFamily(string $osLabel) : ?string
    {
        if (\in_array($osLabel, self::$operatingSystems)) {
            $osLabel = \array_search($osLabel, self::$operatingSystems);
        }
        foreach (self::$osFamilies as $family => $labels) {
            if (\in_array($osLabel, $labels)) {
                return (string) $family;
            }
        }
        return null;
    }
    /**
     * Returns true if OS is desktop
     *
     * @param string $osName OS short name
     *
     * @return bool
     */
    public static function isDesktopOs(string $osName) : bool
    {
        $osFamily = self::getOsFamily($osName);
        return \in_array($osFamily, self::$desktopOsArray);
    }
    /**
     * Returns the full name for the given short name
     *
     * @param string      $os
     * @param string|null $ver
     *
     * @return ?string
     */
    public static function getNameFromId(string $os, ?string $ver = null) : ?string
    {
        if (\array_key_exists($os, self::$operatingSystems)) {
            $osFullName = self::$operatingSystems[$os];
            return \trim($osFullName . ' ' . $ver);
        }
        return null;
    }
    /**
     * Returns the OS that can be safely detected from client hints
     *
     * @return array
     */
    protected function parseOsFromClientHints() : array
    {
        $name = $version = $short = '';
        if ($this->clientHints instanceof ClientHints && $this->clientHints->getOperatingSystem()) {
            $hintName = $this->applyClientHintMapping($this->clientHints->getOperatingSystem());
            foreach (self::$operatingSystems as $osShort => $osName) {
                if ($this->fuzzyCompare($hintName, $osName)) {
                    $name = $osName;
                    $short = $osShort;
                    break;
                }
            }
            $version = $this->clientHints->getOperatingSystemVersion();
            if ('Windows' === $name) {
                $majorVersion = (int) (\explode('.', $version, 1)[0] ?? '0');
                $minorVersion = (int) (\explode('.', $version, 2)[1] ?? '0');
                if (0 === $majorVersion) {
                    $minorVersionMapping = [1 => '7', 2 => '8', 3 => '8.1'];
                    $version = $minorVersionMapping[$minorVersion] ?? $version;
                } elseif ($majorVersion > 0 && $majorVersion < 11) {
                    $version = '10';
                } elseif ($majorVersion > 10) {
                    $version = '11';
                }
            }
            // On Windows, version 0.0.0 can be either 7, 8 or 8.1, so we return 0.0.0
            if ('Windows' !== $name && '0.0.0' !== $version && 0 === (int) $version) {
                $version = '';
            }
        }
        return ['name' => $name, 'short_name' => $short, 'version' => $this->buildVersion($version, [])];
    }
    /**
     * Returns the OS that can be detected from useragent
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function parseOsFromUserAgent() : array
    {
        $osRegex = $matches = [];
        $name = $version = $short = '';
        foreach ($this->getRegexes() as $osRegex) {
            $matches = $this->matchUserAgent($osRegex['regex']);
            if ($matches) {
                break;
            }
        }
        if (!empty($matches)) {
            $name = $this->buildByMatch($osRegex['name'], $matches);
            ['name' => $name, 'short' => $short] = self::getShortOsData($name);
            $version = \array_key_exists('version', $osRegex) ? $this->buildVersion((string) $osRegex['version'], $matches) : '';
            foreach ($osRegex['versions'] ?? [] as $regex) {
                $matches = $this->matchUserAgent($regex['regex']);
                if (!$matches) {
                    continue;
                }
                if (\array_key_exists('name', $regex)) {
                    $name = $this->buildByMatch($regex['name'], $matches);
                    ['name' => $name, 'short' => $short] = self::getShortOsData($name);
                }
                if (\array_key_exists('version', $regex)) {
                    $version = $this->buildVersion((string) $regex['version'], $matches);
                }
                break;
            }
        }
        return ['name' => $name, 'short_name' => $short, 'version' => $version];
    }
    /**
     * Parse current UserAgent string for the operating system platform
     *
     * @return string
     */
    protected function parsePlatform() : string
    {
        // Use architecture from client hints if available
        if ($this->clientHints instanceof ClientHints && $this->clientHints->getArchitecture()) {
            $arch = \strtolower($this->clientHints->getArchitecture());
            if (\false !== \strpos($arch, 'arm')) {
                return 'ARM';
            }
            if (\false !== \strpos($arch, 'loongarch64')) {
                return 'LoongArch64';
            }
            if (\false !== \strpos($arch, 'mips')) {
                return 'MIPS';
            }
            if (\false !== \strpos($arch, 'sh4')) {
                return 'SuperH';
            }
            if (\false !== \strpos($arch, 'sparc64')) {
                return 'SPARC64';
            }
            if (\false !== \strpos($arch, 'x64') || \false !== \strpos($arch, 'x86') && '64' === $this->clientHints->getBitness()) {
                return 'x64';
            }
            if (\false !== \strpos($arch, 'x86')) {
                return 'x86';
            }
        }
        if ($this->matchUserAgent('arm[ _;)ev]|.*arm$|.*arm64|aarch64|Apple ?TV|Watch ?OS|Watch1,[12]')) {
            return 'ARM';
        }
        if ($this->matchUserAgent('loongarch64')) {
            return 'LoongArch64';
        }
        if ($this->matchUserAgent('mips')) {
            return 'MIPS';
        }
        if ($this->matchUserAgent('sh4')) {
            return 'SuperH';
        }
        if ($this->matchUserAgent('sparc64')) {
            return 'SPARC64';
        }
        if ($this->matchUserAgent('64-?bit|WOW64|(?:Intel)?x64|WINDOWS_64|win64|.*amd64|.*x86_?64')) {
            return 'x64';
        }
        if ($this->matchUserAgent('.*32bit|.*win32|(?:i[0-9]|x)86|i86pc')) {
            return 'x86';
        }
        return '';
    }
}
