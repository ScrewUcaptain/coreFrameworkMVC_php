<?php 

namespace suc\phpmvc;

class Request
{
	public function getPath() : string
	{
		$path = $_SERVER['REQUEST_URI'] ?? '/';
		$position = strpos($path, '?');
		if(!$position){
			return $path;
		}
		$path = substr($path, 0, $position);
		return $path;
	}

	public function method() : string
	{
		return strtolower($_SERVER["REQUEST_METHOD"]);
	}

	public function isGet() : bool
	{
		return $this->method() === 'get';
	}

	public function isPost(): bool
	{
		return $this->method() === 'post';
	}

	public function getBody()
	{
		$body = [];
		if ($this->isGet())
		{
			foreach($_GET as $key => $val) 
			{
				$body[$key] = filter_input(INPUT_GET,$key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		if ($this->isPost()) {
			foreach ($_POST as $key => $val) {
				$body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}

		return $body;
	}
}