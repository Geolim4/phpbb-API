<?php

function read_dir($dir, $ext = null)
{
	$list = array();
	$dir .= '/';

	if (($res = opendir($dir)) === false)
	{
		exit(1);
	}

	while (($name = readdir($res)) !== false)
	{
		if ($name == '.' || $name == '..')
		{
			continue;
		}
		$name = $dir . $name;

		if (is_dir($name))
		{
			$list = array_merge($list, read_dir($name, $ext));
		}
		elseif (is_file($name))
		{
			if (!is_null($ext) && substr(strrchr($name, '.'), 1) != $ext)
			{
				continue;
			}
			$list[] = $name;
		}
	}

	return $list;
}

$list = read_dir('.', 'php');
$exit = 0;
foreach ($list as $file)
{
	$output = '';
	
	exec('php -lf ' . $file, $output, $status);
	echo(implode("\n", $output) . "\n");
	if($status != 0)
	{
		$exit = $status;
	}
}

exit($exit);