<?php
/* **********************************************************************
 *
 * Copyright (C) 2003 - 2007 Alejandro Garcia Gonzalez.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 * **********************************************************************
 *
 *	Class:          Nomad MIME Mail ('nomad_mimemail.inc.php')
 *	Version:        1.5
 *	Site:           http://www.developarts.com/nomad_mimemail
 *	Author:         Alejandro Garcia Gonzalez <nexus at developarts.com>
 *
 * Contributors:    Pawel Tomicki <p.tomicki at digitalone.pl>
 *                  Enrique Garcia M. <egarcia at egm.as>
 *                  Ulises Hernandez <megazoidz at gmail.com>
 *
 * Description:
 *  A class for sending MIME based e-mail messages whit SMTP and Auth
 *  SMTP support.
 *
 *  + Plain Text
 *  + HTML
 *  + Plain Text with Attachments
 *  + HTML with Attachments
 *  + HTML with Embedded Images
 *  + HTML with Embedded Images and Attachments
 *  + Send email messages via SMTP and Auth SMTP
 *
 * ********************************************************************** */

class nomad_mimemail {

	/**
 	 * Vars
	 */
	var $debug_status	= "yes";			// "yes" | "no" | "halt"
	var $charset		= "ISO-8859-1";
	var $mail_subject	= "No subject";
	var $mail_from		= "Anonymous <noreply@fake.com>";
	var $mail_to;
	var $mail_cc;
	var $mail_bcc;
	var $mail_text;
	var $mail_html;
	var $mail_type;
	var $mail_header;
	var $mail_body;
	var $mail_reply_to;
	var $mail_return_path;
	var $attachments_index;
	var $attachments = array();
	var $attachments_img = array();
	var $boundary_mix;
	var $boundary_rel;
	var $boundary_alt;
	var $sended_index;
	var $smtp_conn;
	var $smtp_host;
	var $smtp_port;
	var $smtp_user;
	var $smtp_pass;
	var $smtp_log = false;
	var $smtp_msg;

	var $error_msg = array(
			1	=>	'Mail was not sent',
			2	=>	'Body Build Incomplete',
			3	=>	'Need a mail recipient in mail_to',
			4	=>	'No valid Email Address: ',
			5	=>	'Could not Open File',
			6	=>	'Could not connect to SMTP server.',
			7	=>	'Unespected SMTP answer: '
	);

	var $mime_types = array(
			'gif'	=> 'image/gif',
			'jpg'	=> 'image/jpeg',
			'jpeg'	=> 'image/jpeg',
			'jpe'	=> 'image/jpeg',
			'bmp'	=> 'image/bmp',
			'png'	=> 'image/png',
			'tif'	=> 'image/tiff',
			'tiff'	=> 'image/tiff',
			'swf'	=> 'application/x-shockwave-flash',
			'doc'	=> 'application/msword',
			'xls'	=> 'application/vnd.ms-excel',
			'ppt'	=> 'application/vnd.ms-powerpoint',
			'pdf'	=> 'application/pdf',
			'ps'	=> 'application/postscript',
			'eps'	=> 'application/postscript',
			'rtf'	=> 'application/rtf',
			'bz2'	=> 'application/x-bzip2',
			'gz'	=> 'application/x-gzip',
			'tgz'	=> 'application/x-gzip',
			'tar'	=> 'application/x-tar',
			'zip'	=> 'application/zip',
			'html'	=> 'text/html',
			'htm'	=> 'text/html',
			'txt'	=> 'text/plain',
			'css'	=> 'text/css',
			'js'	=> 'text/javascript'
	);


	/**
	 * Constructor
	 * void nomad_mimemail()
	 */
	function nomad_mimemail(){
		$this->boundary_mix			= "=-nxs_mix_" . md5(uniqid(rand()));
		$this->boundary_rel			= "=-nxs_rel_" . md5(uniqid(rand()));
		$this->boundary_alt			= "=-nxs_alt_" . md5(uniqid(rand()));
		$this->attachments_index	= 0;
		$this->sended_index			= 0;
		if(!defined('BR')){
			define('BR', (strstr(PHP_OS, 'WIN') ? "\r\n" : "\n"), TRUE);
		}
	}


	/**
	 * void set_from(string mail_from, [string name])
	 * Set the "from" email address. "Anonymous <fake@mail.com>" by default
	 * @access public
	 * @param string mail_from The email from address
	 * @param string name Optional name contact
	 * @return void
	 */
	function set_from($mail_from, $name = ""){
		if ($this->_validate_mail($mail_from)){
			$this->mail_from = !empty($name) ? "$name <$mail_from>" : $mail_from;
		}
		else {
			$this->mail_from = "Anonymous <noreply@fake.com>";
		}
	}


	/**
	 * bool set_to(string mail_to, [string name])
	 * Set the recipient email address
	 * @access public
	 * @param string mail_to The recipient email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function set_to($mail_to, $name = ""){
		if ($this->_validate_mail($mail_to)){
			$this->mail_to = !empty($name) ? "$name <$mail_to>" : $mail_to;
			return true;
		}
		return false;
	}


	/**
	 * bool set_cc(string mail_cc, [string name])
	 * Set the carbon copy recipient email address
	 * @access public
	 * @param string mail_cc The carbon copy recipient email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function set_cc($mail_cc, $name = ""){
		if ($this->_validate_mail($mail_cc)){
			$this->mail_cc = !empty($name) ? "$name <$mail_cc>" : $mail_cc;
			return true;
		}
		return false;
	}


	/**
	 * bool set_bcc(string mail_bcc, [string name])
	 * Set the blind carbon copy recipient email address
	 * @access public
	 * @param string mail_bcc The blind carbon copy recipient email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function set_bcc($mail_bcc, $name = ""){
		if ($this->_validate_mail($mail_bcc)){
			$this->mail_bcc = !empty($name) ? "$name <$mail_bcc>" : $mail_bcc;
			return true;
		}
		return false;
	}


	/**
	 * bool set_reply_to(string mail_reply_to, [string name])
	 * Set the reply email address. If this var is not set, the reply mail are the "from" email address
	 * @access public
	 * @param string mail_reply_to The reply email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function set_reply_to($mail_reply_to, $name = ""){
		if ($this->_validate_mail($mail_reply_to)){
			$this->mail_reply_to = !empty($name) ? "$name <$mail_reply_to>" : $mail_reply_to;
			return true;
		}
		return false;
	}


	/**
	 * bool add_to(string mail_to, [string name])
	 * Set or add a new recipient email address
	 * @access public
	 * @param string mail_to The recipient email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function add_to($mail_to, $name = ""){
		if ($this->_validate_mail($mail_to)){
			$mail_to = !empty($name) ? "$name <$mail_to>" : $mail_to;
			$this->mail_to = !empty($this->mail_to) ? $this->mail_to . ", " . $mail_to : $mail_to;
			return true;
		}
		return false;
	}


	/**
	 * bool add_cc(string mail_cc, [string name])
	 * Set or add a new carbon copy recipient email address
	 * @access public
	 * @param string mail_cc The carbon copy recipient email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function add_cc($mail_cc, $name = ""){
		if ($this->_validate_mail($mail_cc)){
			$mail_cc = !empty($name) ? "$name <$mail_cc>" : $mail_cc;
			$this->mail_cc = !empty($this->mail_cc) ? $this->mail_cc . ", " . $mail_cc : $mail_cc;
			return true;
		}
		return false;
	}


	/**
	 * bool add_bcc(string mail_bcc, [string name])
	 * Set or add a new blind carbon copy recipient email address
	 * @access public
	 * @param string mail_bcc The blind carbon copy recipient email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function add_bcc($mail_bcc, $name = ""){
		if ($this->_validate_mail($mail_bcc)){
			$mail_bcc = !empty($name) ? "$name <$mail_bcc>" : $mail_bcc;
			$this->mail_bcc = !empty($this->mail_bcc) ? $this->mail_bcc . ", " . $mail_bcc : $mail_bcc;
			return true;
		}
		return false;
	}


	/**
	 * bool add_reply_to(string mail_reply_to, [string name])
	 * Set or add a new reply email address. If this var is not set, the reply mail are the "from" email address
	 * @access public
	 * @param string mail_reply_to The reply email address
	 * @param string name Optional name contact
	 * @return bool
	 */
	function add_reply_to($mail_reply_to, $name = ""){
		if ($this->_validate_mail($mail_reply_to)){
			$mail_reply_to = !empty($name) ? "$name <$mail_reply_to>" : $mail_reply_to;
			$this->mail_reply_to = !empty($this->mail_reply_to) ? $this->mail_reply_to . ", " . $mail_reply_to : $mail_reply_to;
			return true;
		}
		return false;
	}


	/**
	 * bool set_return_path(string mail_return_path)
	 * Set the devilvery error return email address
	 * @access public
	 * @param string mail_return_path The delivery error email account
	 * @return bool
	 */
	function set_return_path($mail_return_path){
		if ($this->_validate_mail($mail_return_path)){
			$this->mail_return_path = $mail_return_path;
			return true;
		}
		return false;
	}


	/**
	 * void set_subject(string subject)
	 * Set the email subject string. "No subject" by default
	 * @access public
	 * @param string subject
	 * @return void
	 */
	function set_subject($subject){
		$this->mail_subject = !empty($subject) ? trim($subject) : "No subject";
	}


	/**
	 * void set_text(string text)
	 * Set the plain text message in body of email
	 * @access public
	 * @param string text The plain text message
	 * @return void
	 */
	function set_text($text){
		if (!empty($text)){
			$this->mail_text = $text;
		}
	}


	/**
	 * void set_html(string html)
	 * Set the HTML message in body of email
	 * @access public
	 * @param string html The HTML message
	 * @return void
	 */
	function set_html($html){
		if (!empty($html)){
			$this->mail_html = $html;
		}
	}


	/**
	 * void set_charset(string charset)
	 * Set the charset if email
	 * @access public
	 * @param string charset The CharSet
	 * @return void
	 */
	function set_charset($charset){
		if (!empty($charset)){
			$this->charset = $charset;
		}
	}


	/**
	 * bool set_smtp_host(string host, [int port])
	 * Set the SMTP host and port, if you call this method with valid parameters, the class sends email through SMTP
	 * @access public
	 * @param string host The Hostname/IP of the SMTP server
	 * @param int port Optional, the port to connect to SMTP server
	 * @return bool
	 */
	function set_smtp_host($host, $port = 25){
		if (!empty($host) && is_numeric($port)){
			$this->smtp_host = $host;
			$this->smtp_port = $port;
			return true;
		}
		return false;
	}


	/**
	 * bool set_smtp_host(string host, [int port])
	 * Set the Auth SMTP user and password, you need to call method set_smtp_host before
	 * @access public
	 * @param string user The Username Authentication account
	 * @param string pass The Password Authentication account
	 * @return bool
	 */
	function set_smtp_auth($user, $pass){
		if(!empty($user) && !empty($pass)){
			$this->smtp_user = $user;
			$this->smtp_pass = $pass;
			return true;
		}
		return false;
	}


	/**
	 * string get_eml()
	 * Get the EML format message of the email
	 * @access public
	 * @return mixed string if message has build, false if not
	 */
	function get_eml(){
		if ($this->_build_body()){
			return
				$this->mail_header . BR .
				'Subject: ' . $this->mail_subject . BR .
				$this->mail_body;
		}
		return false;
	}


	/**
	 * bool add_attachment(mixed file, string name, [string type])
	 * Add a file attachment
	 * @access public
	 * @param string file
	 * @param string name
	 * @param string type
	 * @return bool
	 */
	function add_attachment($file, $name, $type = ""){
		if (($content = $this->_open_file($file))){
			$this->attachments[$this->attachments_index] = array(
				'content' => chunk_split(base64_encode($content), 76, BR),
				'name' => $name,
				'type' => (empty($type) ? $this->_get_mimetype($name): $type),
				'embedded' => false
			);
			$this->attachments_index++;
		}
	}


	/**
	 * bool add_content_attachment(mixed file, string name, [string type])
	 * Add a content to an attachment
	 * @access public
	 * @param string content
	 * @param string name
	 * @param string type
	 * @return bool
	 */
	function add_content_attachment($content, $name, $type = ""){
		$this->attachments[$this->attachments_index] = array(
			'content' => chunk_split(base64_encode($content), 76, BR),
			'name' => $name,
			'type' => (empty($type) ? $this->_get_mimetype($name): $type),
			'embedded' => false
		);
		$this->attachments_index++;
	}


	/**
	 * void new_mail([mixed from], [mixed to], [string subject], [string text], [string html])
	 * Method shortcut to create an email
	 * @access public
	 * @return void
	 */
	function new_mail($from = "", $to = "", $subject = "", $text = "", $html = ""){

		// First, clear all vars
		$this->mail_subject = "";
		$this->mail_from = "";
		$this->mail_to = "";
		$this->mail_cc = "";
		$this->mail_bcc = "";
		$this->mail_text = "";
		$this->mail_html = "";
		$this->mail_header = "";
		$this->mail_body = "";
		$this->mail_reply_to = "";
		$this->mail_return_path = "";
		$this->attachments_index = 0;
		$this->sended_index = 0;

		// Clear Array Vars
		$this->attachments = array();
		$this->attachments_img = array();

		// Asign vars
		if (is_array($from)){
			$this->set_from($from[0],$from[1]);
			$this->set_return_path($from[0]);
		}
		else {
			$this->set_from($from);
			$this->set_return_path($from);
		}

		if (is_array($to)){
			$this->set_to($to[0],$to[1]);
		}
		else {
			$this->set_to($to);
		}

		$this->set_subject($subject);
		$this->set_text($text);
		$this->set_html($html);
	}


	/**
	 * bool send()
	 * Send the email message
	 * @access public
	 * @return bool
	 */
	function send(){
		if ($this->sended_index == 0 && !$this->_build_body()){
			$this->_debug(1);
			return false;
		}

		if (empty($this->smtp_host) && !empty($this->mail_return_path) && $this->_php_version_check('4.0.5') && !($this->_php_version_check('4.2.3') && ini_get('safe_mode'))){
			return mail($this->mail_to, $this->mail_subject, $this->mail_body, $this->mail_header, '-f'.$this->mail_return_path);
		}
		elseif (empty($this->smtp_host)) {
			return mail($this->mail_to, $this->mail_subject, $this->mail_body, $this->mail_header);
		}
		elseif (!empty($this->smtp_host)){
			return $this->_smtp_send();
		}
		else {
			return false;
		}
	}


	/**
	 * void _build_header()
	 * Build all the headers of email
	 * @access private
	 * @param text content_type The Content Type of email
	 * @return void
	 */
	function _build_header($content_type){
		if (!empty($this->mail_from)){
			$this->mail_header .= "From: " . $this->mail_from . BR;
			$this->mail_header .= !empty($this->mail_reply_to) ? "Reply-To: " . $this->mail_reply_to . BR : "Reply-To: " . $this->mail_from . BR;
		}
		if (!empty($this->mail_to)){	// FixBug: http://www.developarts.com/version_14_de_nomad_mime_mail#comment-294
			$this->mail_header .= "To: " . $this->mail_to . BR;
		}
		if (!empty($this->mail_cc)){
			$this->mail_header .= "Cc: " . $this->mail_cc . BR;
		}
		if (!empty($this->mail_bcc) && empty($this->smtp_host)){
			$this->mail_header .= "Bcc: " . $this->mail_bcc . BR;
		}
		if (!empty($this->mail_return_path)){
			$this->mail_header .= "Return-Path: " . $this->mail_return_path . BR;
		}
		$this->mail_header .= "MIME-Version: 1.0" . BR;
		$this->mail_header .= "X-Mailer: Nomad MIME Mail ". $this->get_version() . BR;
		$this->mail_header .= $content_type;
	}


	/**
	 * bool _build_body()
	 * Build body email message
	 * @access private
	 * @return bool
	 */
	function _build_body(){
		switch ($this->_parse_elements()){
			case 1:
				$this->_build_header("Content-Type: text/plain; charset=\"$this->charset\"");
				$this->mail_body = $this->mail_text;
				break;
			case 3:
				$this->_build_header("Content-Type: multipart/alternative; boundary=\"$this->boundary_alt\"");
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/plain; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_text . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/html; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_html . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . "--" . BR;
				break;
			case 5:
				$this->_build_header("Content-Type: multipart/mixed; boundary=\"$this->boundary_mix\"");
				$this->mail_body .= "--" . $this->boundary_mix . BR;
				$this->mail_body .= "Content-Type: text/plain; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_text . BR . BR;
				foreach($this->attachments as $value){
					$this->mail_body .= "--" . $this->boundary_mix . BR;
					$this->mail_body .= "Content-Type: " . $value['type'] . "; name=\"" . $value['name'] . "\"" . BR;
					$this->mail_body .= "Content-Disposition: attachment; filename=\"" . $value['name'] . "\"" . BR;
					$this->mail_body .= "Content-Transfer-Encoding: base64" . BR . BR;
					$this->mail_body .= $value['content'] . BR . BR;
				}
				$this->mail_body .= "--" . $this->boundary_mix . "--" . BR;
				break;
			case 7:
				$this->_build_header("Content-Type: multipart/mixed; boundary=\"$this->boundary_mix\"");
				$this->mail_body .= "--" . $this->boundary_mix . BR;
				$this->mail_body .= "Content-Type: multipart/alternative; boundary=\"$this->boundary_alt\"" . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/plain; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_text . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/html; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_html . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . "--" . BR . BR;
				foreach($this->attachments as $value){
					$this->mail_body .= "--" . $this->boundary_mix . BR;
					$this->mail_body .= "Content-Type: " . $value['type'] . "; name=\"" . $value['name'] . "\"" . BR;
					$this->mail_body .= "Content-Disposition: attachment; filename=\"" . $value['name'] . "\"" . BR;
					$this->mail_body .= "Content-Transfer-Encoding: base64" . BR . BR;
					$this->mail_body .= $value['content'] . BR . BR;
				}
				$this->mail_body .= "--" . $this->boundary_mix . "--" . BR;
				break;
			case 11:
				$this->_build_header("Content-Type: multipart/related; type=\"multipart/alternative\"; boundary=\"$this->boundary_rel\"");
				$this->mail_body .= "--" . $this->boundary_rel . BR;
				$this->mail_body .= "Content-Type: multipart/alternative; boundary=\"$this->boundary_alt\"" . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/plain; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_text . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/html; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_html . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . "--" . BR . BR;
				foreach($this->attachments as $value){
					if ($value['embedded']){
						$this->mail_body .= "--" . $this->boundary_rel . BR;
						$this->mail_body .= "Content-ID: <" . $value['embedded'] . ">" . BR;
						$this->mail_body .= "Content-Type: " . $value['type'] . "; name=\"" . $value['name'] . "\"" . BR;
						$this->mail_body .= "Content-Disposition: attachment; filename=\"" . $value['name'] . "\"" . BR;
						$this->mail_body .= "Content-Transfer-Encoding: base64" . BR . BR;
						$this->mail_body .= $value['content'] . BR . BR;
					}
				}
				$this->mail_body .= "--" . $this->boundary_rel . "--" . BR;
				break;
			case 15:
				$this->_build_header("Content-Type: multipart/mixed; boundary=\"$this->boundary_mix\"");
				$this->mail_body .= "--" . $this->boundary_mix . BR;
				$this->mail_body .= "Content-Type: multipart/related; type=\"multipart/alternative\"; boundary=\"$this->boundary_rel\"" . BR . BR;
				$this->mail_body .= "--" . $this->boundary_rel . BR;
				$this->mail_body .= "Content-Type: multipart/alternative; boundary=\"$this->boundary_alt\"" . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/plain; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_text . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . BR;
				$this->mail_body .= "Content-Type: text/html; charset=\"$this->charset\"" . BR;
				$this->mail_body .= "Content-Transfer-Encoding: 7bit" . BR . BR;
				$this->mail_body .= $this->mail_html . BR . BR;
				$this->mail_body .= "--" . $this->boundary_alt . "--" . BR . BR;
				foreach($this->attachments as $value){
					if ($value['embedded']){
						$this->mail_body .= "--" . $this->boundary_rel . BR;
						$this->mail_body .= "Content-ID: <" . $value['embedded'] . ">" . BR;
						$this->mail_body .= "Content-Type: " . $value['type'] . "; name=\"" . $value['name'] . "\"" . BR;
						$this->mail_body .= "Content-Disposition: attachment; filename=\"" . $value['name'] . "\"" . BR;
						$this->mail_body .= "Content-Transfer-Encoding: base64" . BR . BR;
						$this->mail_body .= $value['content'] . BR . BR;
					}
				}
				$this->mail_body .= "--" . $this->boundary_rel . "--" . BR . BR;
				foreach($this->attachments as $value){
					if (!$value['embedded']){
						$this->mail_body .= "--" . $this->boundary_mix . BR;
						$this->mail_body .= "Content-Type: " . $value['type'] . "; name=\"" . $value['name'] . "\"" . BR;
						$this->mail_body .= "Content-Disposition: attachment; filename=\"" . $value['name'] . "\"" . BR;
						$this->mail_body .= "Content-Transfer-Encoding: base64" . BR . BR;
						$this->mail_body .= $value['content'] . BR . BR;
					}
				}
				$this->mail_body .= "--" . $this->boundary_mix . "--" . BR;
				break;
			default:
				return $this->_debug(2);
		}
		$this->sended_index++;
		return true;
	}


	/**
	 * bool _php_version_check(string vercheck)
	 * Check if current version of PHP is above than other
	 * @access private
	 * @param string vercheck The compare version of PHP
	 * @return bool
	 */
	function _php_version_check($vercheck){
		$minver = str_replace(".","", $vercheck);
		$curver = str_replace(".","", phpversion());
		if($curver >= $minver){
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * mixed _parse_elements()
	 * Check all email message elements and return a identifier
	 * @access private
	 * @return mixed int|false
	 */
	function _parse_elements(){
		if (empty($this->mail_to)){
			return $this->_debug(3);
		}
		$this->mail_type = 0;
		$this->_search_images();
		if (!empty($this->mail_text)){
			$this->mail_type = $this->mail_type + 1;
		}
		if (!empty($this->mail_html)){
			$this->mail_type = $this->mail_type + 2;
			if (empty($this->mail_text)){
				$this->mail_text = strip_tags(eregi_replace("<br>", BR, $this->mail_html));
				$this->mail_type = $this->mail_type + 1;
			}
		}
		if ($this->attachments_index != 0){
			if (count($this->attachments_img) != 0){
				$this->mail_type = $this->mail_type + 8;
			}
			if ((count($this->attachments) - count($this->attachments_img)) >= 1){
				$this->mail_type = $this->mail_type + 4;
			}
		}
		return $this->mail_type;
	}


	/**
	 * void _search_images()
	 * Search all embeded images in HTML and attachments
	 * @access private
	 * @return void
	 */
	function _search_images(){
		if ($this->attachments_index != 0){
			foreach($this->attachments as $key => $value){
				if (preg_match('/(css|image)/i', $value['type']) && preg_match('/\s(background|href|src)\s*=\s*[\"|\'](' . $value['name'] . ')[\"|\'].*>/is', $this->mail_html)) {
					$img_id = md5($value['name']) . ".nomad@mimemail";
					$this->mail_html = preg_replace('/\s(background|href|src)\s*=\s*[\"|\'](' . $value['name'] . ')[\"|\']/is', ' \\1="cid:' . $img_id . '"', $this->mail_html);
					$this->attachments[$key]['embedded'] = $img_id;
					$this->attachments_img[] = $value['name'];
				}
			}
		}
	}


	/**
	 * bool _validate_mail(string mail)
	 * Validate an email address
	 * @access private
	 * @param string mail The email address string
	 * @return bool
	 */
	function _validate_mail($mail){
		if (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$mail)){
			return true;
		}
		return $this->_debug(4, $mail);
	}


	/**
	 * mixed _extract_email(string parse)
	 * Extract all email addresses from a string. If extracted more than one
	 * return an array. If extraded only one email return string. Else return false
	 * @access private
	 * @param string parse String whit one or more email addresses
	 * @return mixed array|string|false
	 */
	function _extract_email($parse){
		preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $parse, $matches);
		if (count($matches[0]) == 1){
			return $matches[0][0];
		}
		elseif (!count($matches[0])){
			return false;
		}
		else {
			return $matches[0];
		}
	}


	/**
	 * string _get_mimetype(string name)
	 * Search a mime type based in it's extension filename
	 * @access private
	 * @param string name The file name
	 * @return mixed string
	 */
	function _get_mimetype($name){
		$ext_array = explode(".", $name);
		if (($last = count($ext_array) - 1) != 0){
			$ext = $ext_array[$last];
			if (isset($this->mime_types[$ext]))
				return $this->mime_types[$ext];
		}
		return "application/octet-stream";
	}


	/**
	 * mixed _open_file(string file)
	 * Opens a file and returns it's content
	 * @access private
	 * @param string file The file path
	 * @return mixed string|false
	 */
	function _open_file($file){
		if(($fp = @fopen($file, 'r'))){
			$content = fread($fp, filesize($file));
			fclose($fp);
			return $content;
		}
		return $this->_debug(5, $file);
	}


	/**
	 * bool false _debug(int msg, [string element])
	 * Printa a error and returns false
	 * @access private
	 * @param int msg The id error
	 * @param string element Optional The extra message error
	 * @return bool false
	 */
	function _debug($msg, $element=""){
		if ($this->debug_status == "yes"){
			echo "<br><b>Error:</b> " . $this->error_msg[$msg] . " $element<br>";
		}
		elseif ($this->debug_status == "halt"){
			die ("<br><b>Error:</b> " . $this->error_msg[$msg] . " $element<br>");
		}
		return false;
	}


	/**
	 * bool _open_smtp_conn()
	 * Opens a socket connection to SMTP server
	 * @access private
	 * @return bool
	 */
	function _open_smtp_conn(){
		if ($this->smtp_conn = @fsockopen ($this->smtp_host, $this->smtp_port)){
			if (in_array($this->_get_smtp_response(), array(220, 250, 354))){
				return true;
			}
		}
		return $this->_debug(6);
	}


	/**
	 * void _close_smtp_conn()
	 * Close SMTP connection
	 * @access private
	 * @return void
	 */
	function _close_smtp_conn(){
		$this->_send_smtp_command("QUIT");
		@fclose($this->smtp_conn);
	}


	/**
	 * bool _send_smtp_command(string command, [array number])
	 * Sends a Command to SMTP server
	 * @access private
	 * @param string command String of Command to send
	 * @param array number Optional array of accepted numbers for response
	 * @return bool
	 */
	function _send_smtp_command($command, $number=""){
		if (@fwrite($this->smtp_conn, $command . BR)){
			$this->smtp_msg .= $this->smtp_log == true ? $command . "\n" : "";
			if (!empty($number)){
				if (!in_array($this->_get_smtp_response(), (array)$number)){
					$this->_close_smtp_conn();
					return $this->_debug(7);
				}
			}
			return true;
		}
		return false;
	}


	/**
	 * int _get_smtp_response()
	 * Check the id number response from SMTP server
	 * @access private
	 * @return int
	 */
	function _get_smtp_response(){
		do {
			$response = chop(@fgets($this->smtp_conn, 1024));
			$this->smtp_msg .= $this->smtp_log == true ? $response . "\n" : "";
		} while($response{3} == "-");
		return intval(substr($response,0,3));
	}


	/**
	 * bool _smtp_send()
	 * Sends the email message via SMTP
	 * @access private
	 * @return bool
	 */
	function _smtp_send(){
		if ($this->_open_smtp_conn()){
			if (!$this->_send_smtp_command("helo {$this->smtp_host}", array(220, 250, 354))){return false;}
			if(!empty($this->smtp_user) && !empty($this->smtp_pass)){
				if (!$this->_send_smtp_command("EHLO {$this->smtp_host}", array(220, 250, 354))){return false;}
				if (!$this->_send_smtp_command("AUTH LOGIN", array(334))){return false;}
				if (!$this->_send_smtp_command(base64_encode($this->smtp_user), array(334))){return false;}
				if (!$this->_send_smtp_command(base64_encode($this->smtp_pass), array(235))){return false;}
			}
			if (!$this->_send_smtp_command("MAIL FROM: " . $this->_extract_email($this->mail_from), array(220, 250, 354))){return false;}	// FixBug: http://www.developarts.com/version_14_de_nomad_mime_mail#comment-19
			$all_email = $this->_extract_email(implode(", ", array($this->mail_to, $this->mail_cc, $this->mail_bcc)));
			foreach ((array)$all_email as $email){
				if (!$this->_send_smtp_command("RCPT TO: {$email}", array(220, 250, 354))){return false;}
			}
			if (!$this->_send_smtp_command("DATA", array(220, 250, 354))){return false;}
			$this->_send_smtp_command($this->mail_header);
			$this->_send_smtp_command("Subject: {$this->mail_subject}");
			$this->_send_smtp_command($this->mail_body);
			if (!$this->_send_smtp_command(".", array(220, 250, 354))){return false;}
			$this->_close_smtp_conn();
			return true;
		}
		return false;
	}


	/**
	 * void set_smtp_log(bool log)
	 * Activate or Deactivate SMTP log messages
	 * @access public
	 * @param bool log True if you can log SMTP messages, false by default
	 * @return void
	 */
	function set_smtp_log($log = false){
		if ($log == true){
			$this->smtp_log = true;
		}
		else {
			$this->smtp_log = false;
		}
	}


	/**
	 * string get_smtp_log()
	 * Get all SMTP log
	 * @access public
	 * @return string
	 */
	function get_smtp_log(){
		if ($this->smtp_log == true){
			return $this->smtp_msg;
		}
		else {
			return "No logs activated";
		}
	}


	/**
	 * string get_version()
	 * Return the version of this class
	 * @access public
	 * @return string
	 */
	function get_version(){
		return "1.5";
	}

}
?>