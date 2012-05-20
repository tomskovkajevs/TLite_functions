<?php
error_reporting(1);
set_time_limit (0);

/**
 *
 *
 * @const	DEFAULT_STORAGE
 * @access	public
 */
define ("DEFAULT_STORAGE", "");
/**
 *
 *
 * @const	DEFAULT_BUF_SIZE
 * @access	public
 */
define ("DEFAULT_BUF_SIZE", 800192);
/**
 * Display directly
 *
 * @const	CD_DISPLAY
 * @access	public
 */
define ("CD_DISPLAY", "inline");
/**
 * Save to disk
 *
 * @const	CD_SAVE
 * @access	public
 */
define ("CD_SAVE", "attachment");
/**
 *
 *
 * @const	CT_APP_OS
 * @access	public
 */
define ("CT_APP_OS", "application/octet-stream");
/**
 *
 *
 * @const	HDR_X_SCRIPT
 * @access	public
 */
define ("HDR_X_SCRIPT", "X-Script: No Direct Links! v0.5 hayk@mail.ru");
/**
 *
 *
 * @const	CON_STATUS_NORMAL
 * @access	private
 */
define ("CON_STATUS_NORMAL", 0);

/**
 * NDL - No Direct Links!
 *
 *
 *
 * @package		NDL
 * @author		hayk@mail.ru
 * @copyright	(c) 2002 hayk@mail.ru
 * @version		0.5
 * @access		public
 * @since		PHP 4.3.0
 */
class NDL
{

	/**
	 *
	 *
	 * @var
	 */
	var $vars;

	/**
	 *
	 *
	 * @var
	 */
	var $server;

	/**
	 *
	 *
	 * @var
	 */
	var $fileName;

	/**
	 *
	 *
	 * @var
	 */
	var $fileTime;

	/**
	 *
	 *
	 * @var
	 */
	var $storedFileName;

	/**
	 *
	 *
	 * @var
	 */
	var $contentSize;

	/**
	 *
	 *
	 * @var
	 */
	var $storageDir;

	/**
	 *
	 *
	 * @var
	 */
	var $storedFileSize;

	/**
	 *
	 *
	 * @var
	 */
	//var $contentSize;

	/**
	 *
	 *
	 * @var
	 */
	var $httpContentDisposition;

	/**
	 *
	 *
	 * @var
	 */
	var $httpContentDescription;

	/**
	 *
	 *
	 * @var
	 */
	var $httpContentType;

	/**
	 *
	 *
	 * @var
	 */
	var $bufSize;

	/**
	 * NDL class constructor.
	 *
	 * @param 	$file	string
	 * @param 	$storage	string
	 * @param 	$description	string
	 * @param 	$type	integer
	 * @param 	$content	string
	 * @access	public
	 * @final
	 */
	function NDL ($file, $storage=DEFAULT_STORAGE, $description=false, $type=CD_SAVE, $content=CT_APP_OS)
	{
//	echo "taisu<br>";
		$this->storageDir = $storage;
		$this->bufSize = DEFAULT_BUF_SIZE;
		$this->fileName = $file;
		$this->storedFileName = $file;
		$this->httpContentType = $content;
		$this->httpContentDisposition = $type;
		$this->httpContentDescription = $description;

		if (isset($HTTP_GET_VARS))
		{ $this->vars = array_merge($_GET, $_POST, $_COOKIE, $_FILES); }
		else
		{ $this->vars = &$_REQUEST; }

		if (isset($_SERVER))
		{ $this->server = &$_SERVER; }
		else
		{ $this->server = &$GLOBALS["_SERVER"]; }

	} // end function NDL

	/**
	 * NDL class destructor.
	 *
	 * @access	public
	 * @final
	 */
	function _NDL()
	{

	} // end function _NDL

	/**
	 *
	 *
	 * @access	public
	 * @final
	 */
	function send ()
	{

		if ( !$this->isAllowed() )
		{
			$this->http403 ();
			$this->updateStat ("403");
		}
		elseif ( (!isset($this->storedFileName)) || empty($this->storedFileName) || (! file_exists( $this->storageDir . $this->storedFileName)) )
		{
			$this->http404 ();
			$this->updateStat ("404");
		}
		else
		{
			$this->fileTime = filemtime ($this->storageDir . $this->storedFileName);
			$this->storedFileSize = filesize ( $this->storageDir . $this->storedFileName);
			$fd = fopen ($this->storageDir.$this->storedFileName, "rb"); // fb
			if ( isset($this->server["HTTP_RANGE"]) )
			{
				preg_match ("/bytes=(\d+)-/", $this->server["HTTP_RANGE"], $m);
				$offset = intval($m[1]);
				$this->contentSize = $this->storedFileSize - $offset;
				fseek ($fd, $offset);
				$this->updateStat ("206");
				$this->http206 ();
			}
			else
			{
				$this->contentSize = $this->storedFileSize;
				$this->updateStat ("200");
				$this->http200 ();
			}
			while ( !feof($fd) && (connection_status() == CON_STATUS_NORMAL) )
			{
				$contents = fread ($fd, $this->bufSize);
				echo $contents;
			}
			fclose ($fd);
		}
	} // end function send

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http200 ()
	{
		header ("HTTP/1.1 200 OK");
		header ("Date: " . $this->getGMTDateTime ());
		header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
		header ("Last-Modified: " . $this->getGMTDateTime ($this->fileTime) );
		header ("Cache-Control: None");
		header ("Pragma: no-cache");
		header ("Accept-Ranges: bytes");
		header ("Content-Disposition: " . $this->httpContentDisposition . "; filename=\"" . $this->fileName . "\"");
		header ("Content-Type: " . $this->httpContentType);
		if ($this->httpContentDescription)
			header ("Content-Description: " . $this->httpContentDescription );
		header ("Content-Length: " . $this->contentSize);
		header ("Proxy-Connection: close");
//		header ("");
	} // end function http200

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http206 ()
	{
		$p1 = $this->storedFileSize - $this->contentSize;
		$p2 = $this->storedFileSize - 1;
		$p3 = $this->storedFileSize;

		header ("HTTP/1.1 206 Partial Content");
		header ("Date: " . $this->getGMTDateTime ());
		header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
		header ("Last-Modified: " . $this->getGMTDateTime ($this->fileTime) );
		header ("Cache-Control: None");
		header ("Pragma: no-cache");
		header ("Accept-Ranges: bytes");
		header ("Content-Disposition: " . $this->httpContentDisposition . "; filename=\"" . $this->fileName . "\"");
		header ("Content-Type: " . $this->httpContentType);
		if ($this->httpContentDescription)
			header ("Content-Description: " . $this->httpContentDescription );
		header ("Content-Range: bytes " . $p1 . "-" . $p2 . "/" . $p3);
		header ("Content-Length: " . $this->contentSize);
		header ("Proxy-Connection: close");
//		header ("");
	} // end function http206

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http404 ()
	{
		header ("HTTP/1.1 404 Object Not Found");
		header ("X-Powered-By: PHP/" . phpversion());
	} // end function http404

	/**
	 *
	 *
	 * @access	private
	 * @final
	 */
	function http403 ()
	{
		header ("HTTP/1.1 403 Forbidden");
		header ("X-Powered-By: PHP/" . phpversion());
	//	header ("");
	} // end function http403

	/**
	 *
	 * @param	int		$time	UNIX timestamp
	 * @return	string	GMT formated time
	 * @access	public
	 * @final
	 */
	function getGMTDateTime ($time=NULL)
	{
		$offset = date("O");
		$roffset = "";
		if ($offset[0] == "+")
		{
			$roffset = "-";
		}
		else
		{
			$roffset = "+";
		}
		$roffset .= $offset[1].$offset[2];
		if (!$time)
		{
			$time = Time();
		}
		return (date ("D, d M Y H:i:s", $time+$roffset*3600 ) . " GMT");
	} // end function getGMTDateTime

	/**
	 *
	 *
	 * @return	bool
	 * @access	public
	 * @abstract
	 */
	function isAllowed ()
	{
		return true;
	} // end function isAllowed

	/**
	 *
	 * @param	string	$code HTTP code
	 * @access	public
	 * @abstract
	 */
	function updateStat ($code)
	{
		return true;
	} // end function updateStat

} // end class NDL

?>