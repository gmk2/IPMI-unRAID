<?
/* read config files */
$plg_path    = '/boot/config/plugins/ipmi';
$cfg_file    = "$plg_path/ipmi.cfg";
$fancfg_file = "$plg_path/fan.cfg";
if (is_file($cfg_file))
	$cfg    = parse_ini_file($cfg_file);
if (is_file($fancfg_file))
	$fancfg = parse_ini_file($fancfg_file);

/* ipmi network options */
$netsvc   = isset($cfg['NETWORK'])  ? $cfg['NETWORK']  : 'disable';
$ipaddr   = isset($cfg['IPADDR'])   ? $cfg['IPADDR']   : '';
$user     = isset($cfg['USER'])     ? $cfg['USER']     : '';
$password = isset($cfg['PASSWORD']) ? $cfg['PASSWORD'] : '';

/* fan control */
$fanctrl = isset($fancfg['FANCONTROL']) ? $fancfg['FANCONTROL'] :'disable';
$fanpoll = isset($fancfg['FANPOLL'])    ? $fancfg['FANPOLL']    : 1;
$fanip = (isset($fancfg['FANIP']) && ($netsvc == 'enable')) ? $fancfg['FANIP'] : '';

/* check if local ipmi driver is loaded */
if($netsvc == 'disable')
	$mod = shell_exec("modprobe ipmi_si --first-time 2>&1 | grep -q 'Module already in kernel' && echo 1 || echo 0 2> /dev/null");

/* options for network access */
$netopts = ($netsvc == 'enable') ? "--always-prefix -h $ipaddr -u $user -p ".
	base64_decode($password)." --session-timeout=5000 --retransmission-timeout=1000" : '';
$fanopts = ($netsvc == 'enable') ? "-h $fanip -u $user -p ".
	base64_decode($password)." --session-timeout=5000 --retransmission-timeout=1000" : '';
?>