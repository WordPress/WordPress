<?php

abstract class wfDirectoryIterator {

	abstract public function file($file);

	/**
	 * @var string
	 */
	private $directory;

	/**
	 * @var int
	 */
	private $directory_limit;

	/**
	 * @var callback
	 */
	private $callback;
	/**
	 * @var int
	 */
	private $max_iterations;
	private $iterations;

	/**
	 * @param string $directory
	 * @param int    $max_files_per_directory
	 * @param int    $max_iterations
	 */
	public function __construct($directory = ABSPATH, $max_files_per_directory = 20000, $max_iterations = 1000000) {
		$this->directory = $directory;
		$this->directory_limit = $max_files_per_directory;
		$this->max_iterations = $max_iterations;
	}

	public function run() {
		$this->iterations = 0;
		$this->scan($this->directory);
	}

	protected function scan($dir) {
		$dir = rtrim($dir, DIRECTORY_SEPARATOR);
		$handle = opendir($dir);
		$file_count = 0;
		while ($file = readdir($handle)) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$file_path = $dir . '/' . $file;
			if (is_dir($file_path)) {
				if ($this->scan($file_path) === false) {
					closedir($handle);
					return false;
				}
			} else {
				if ($this->file($file_path) === false) {
					closedir($handle);
					return false;
				}
			}
			if (++$file_count >= $this->directory_limit) {
				break;
			}
			if (++$this->iterations >= $this->max_iterations) {
				closedir($handle);
				return false;
			}
		}
		closedir($handle);
		return true;
	}
}

