<?php
// based on http://innvo.com/1311865299-htpasswd-manager/

// Save as .htpasswd.php
class htpasswd {
	var $fp;
	var $filename;
 
	function htpasswd($filename) {
		if (!file_exists($filename))
			die('File not there');
		@$this->fp = fopen($filename,'r+') or die('Invalid file name');
		$this->filename = $filename;
	}
 
	function user_exists($username) {
		rewind($this->fp);
		while(!feof($this->fp)) {
			$line = rtrim(fgets($this->fp));
			if(!$line)
				continue;
			$lusername = explode(":",$line);
			$lusername = $lusername[0];
			if($lusername == $username)
				return true;
		}
		return false;
	}
 
	function user_add($username,$password) {
		if($this->user_exists($username))
			$this->user_delete($username);
		fseek($this->fp,0,SEEK_END);
		fwrite($this->fp,$username.':'.crypt($password,substr(str_replace('+','.',base64_encode(pack('N4', mt_rand(),mt_rand(),mt_rand(),mt_rand()))),0,22))."\n");
		return $this->user_exists($username);
	}
 
	function user_delete($username) {
		$data = '';
		rewind($this->fp);
		while(!feof($this->fp)) {
			$line = rtrim(fgets($this->fp));
			if(!$line)
				continue;
			$lusername = explode(":",$line);
			$lusername = $lusername[0];
			if($lusername != $username)
				$data .= $line."\n";
		}
		$this->fp = fopen($this->filename,'w');
		fwrite($this->fp,rtrim($data).(trim($data) ? "\n" : ''));
		fclose($this->fp);
		$this->fp = fopen($this->filename,'r+');
		return true;
	}
 
	function user_update($username,$password) {
		rewind($this->fp);
			while(!feof($this->fp)) {
				$line = rtrim(fgets($this->fp));
				if(!$line)
					continue;
				$lusername = explode(":",$line);
				$lusername = $lusername[0];
				if($lusername == $username) {
					fseek($this->fp,(-15 - strlen($username)),SEEK_CUR);
					fwrite($this->fp,$username.':'.crypt($password,substr(str_replace('+','.',base64_encode(pack('N4', mt_rand(),mt_rand(),mt_rand(),mt_rand()))),0,22))."\n");
					return true;
				}
			}
		return false;
	}
}
