<?php
/**
*
* @package API cache extending
^>@version $Id: cache.php v0.0.1 13h37 03/08/2014 Geolim4 Exp $
* @copyright (c) 2012 - 2014 Geolim4.com http://geolim4.com
* @bug/function request: http://geolim4.com/tracker
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace phpbb_api;
//Import special phpBB classes
use cache;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Class for grabbing/handling cached entries, extends acm_file or acm_db depending on the setup
* @package acm
*/
final class api_cache extends cache //Grab the cache class from the root namespace scope
{
	/**
	* Set cache path
	*/
	function __construct()
	{
		global $phpbb_root_path, $phpEx;
		$this->cache_dir = API_CACHE_PATH;
		$this->cache_ext = $phpEx;//define file ext for cached ressources
	}

	/**
	* Rebuild the cache (Mainly used in cron task)
	*/
	function rebuild()
	{
		$this->purge();
		$this->obtain_database_mapping();
		$this->obtain_api_hooks();
	}

	/**
	* Get an array that represents directory tree
	* @param string $directory		Directory path
	* @param bool $recursive		Include sub directories
	* @param bool $listDirs		Include directories on listing
	* @param bool $listFiles		Include files on listing
	* @param regex $exclude		Exclude paths that matches this regex
	*/
	function directory_to_array($directory, $recursive = true, $list_dirs = false, $list_files = true, $exclude = '') 
	{
		$array_items = array();
		$skip_by_exclude = false;
		$handle = opendir($directory);
		if ($handle) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
				if ($exclude)
				{
					preg_match($exclude, $file, $skip_by_exclude);
				}
				if (!$skip && !$skip_by_exclude) 
				{
					if (is_dir($directory . DIRECTORY_SEPARATOR . $file) ) 
					{
						if ($recursive) 
						{
							$array_items = array_merge($array_items, $this->directory_to_array($directory . DIRECTORY_SEPARATOR . $file, $recursive, $list_dirs, $list_files, $exclude));
						}
						if ($list_dirs)
						{
							$file = $directory . DIRECTORY_SEPARATOR . $file;
							$array_items[] = $file;
						}
					} 
					else 
					{
						if ($list_files)
						{
							$file = $directory . DIRECTORY_SEPARATOR . $file;
							$array_items[] = $file;
						}
					}
				}
			}
		}
		closedir($handle);
		return $array_items;
	}

	/**
	* Obtain hooks...
	*/
	function obtain_api_hooks($obtain_uninstalled = false)
	{
		global $phpbb_root_path, $phpEx;
		if ($obtain_uninstalled)
		{
			// Now search for uninstalled hooks...
			$hook_files = $this->directory_to_array($phpbb_root_path . 'includes/api/store', false, true, false);
		}
		else
		{
			if (($hook_files = $this->get('_api_hooks')) === false)
			{
				$hook_files = array();

				// Now search for hooks...
				$dh = @opendir($phpbb_root_path . 'includes/api/hooks/');

				if ($dh)
				{
					while (($file = readdir($dh)) !== false)
					{
						if (strpos($file, 'hook_') === 0 && substr($file, -(strlen($phpEx) + 1)) === '.' . $phpEx)
						{
							$hook_files[] = substr($file, 0, -(strlen($phpEx) + 1));
						}
					}
					closedir($dh);
				}

				$this->put('_api_hooks', $hook_files);
			}
		}

		return $hook_files;
	}

	/**
	* Obtain cached file count...
	*/
	public function obtain_cached_files_count()
	{
		global $phpbb_root_path;
		return directory_files_count($phpbb_root_path . 'includes/api/cache/');
	}

	/**
	* Obtain database mapping
	* That function will return something like http://images.geolim4.com/Tr
	*/
	public function obtain_database_mapping()
	{
		$mapping_filename = '_api_database_mapping';
		if (($database_mapping = $this->get($mapping_filename)) === false)
		{
			global $db, $phpbb_root_path, $phpEx;
			if (!class_exists('phpbb_db_tools'))
			{
				include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
			}
			$db_tools = new \phpbb_db_tools($db);
		
			$database_mapping = array();
			$tables = $db_tools->sql_list_tables();

			foreach ($tables AS $tables_)
			{
				$database_mapping[$tables_] = $db_tools->sql_list_columns($tables_);
			}

			//Cache that huge database mapping
			$this->put($mapping_filename, $database_mapping);
		}
		return $database_mapping;
	}
}
?>