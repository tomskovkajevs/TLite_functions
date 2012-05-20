<?php
    // This script has been updated. Please visit: http://www.top-frog.com/archives/2006/12/22/classes_for_file_uploading_in_php

    /*
        this sample is proceedural for those not familiar with OOP. Simply include this file in your
        form processing script and it will handle the uploads. You'll definitely want to make changes to the
        upload directory and to some of the functionality to change it to how you like to work

    !!    This file does no security checking - this solely handles file uploads -
    !!    this file does not handle any security functions. Heed that warning! You use this file at your
    !!    own risk and please do not publically accept files if you don't know what you're doing with
    !!    server security.


        at the end of this script you will have two variables
        $filenames - an array that contains the names of the file uploads that succeeded
        $error - an array of errors that occured while processing files


        if the max file size in the form is more than what is set in php.ini then an addition
        needs to be made to the htaccess file to accomodate this

        add this to  your .htaccess file for this directory
        php_value post_max_size 10M
        php_value upload_max_filesize 10M

        replace 10M to match the value you entered above for $max_file_size

    */

    // images dir - relative from document root
    // this needs to be a folder that is writeable by the server
    ////$image_dir = '/uploads/files/';

    // upload dir
    ////$destination = $_SERVER['DOCUMENT_ROOT'].$image_dir;

    /*
	!!!!!!!!!!!!!!!
	MUS BE SET $destination_path

	C:\destdir\destsmall\ etc

	*/

    if(isset($_FILES))
        {
            // initialize error var for processing
            $error = array();

            // acceptable files
            // if array is blank then all file types will be accepted
            $filetypes = array(
                        'jpg' => 'image/jpeg',
                        'jpe' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'zip' => 'application/zip'
                    );
            $filetypes_complete = array(
                        'ai' => 'application/postscript',
                        'bin' => 'application/octet-stream',
                        'bmp' => 'image/x-ms-bmp',
                        'css' => 'text/css',
                        'csv' => 'text/plain',
                        'doc' => 'application/msword',
                        'dot' => 'application/msword',
                        'eps' => 'application/postscript',
                        'gif' => 'image/gif',
                        'gz' => 'application/x-gzip',
                        'htm' => 'text/html',
                        'html' => 'text/html',
                        'ico' => 'image/x-icon',
                        'jpg' => 'image/jpeg',
                        'jpe' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'js' => 'text/javascript',
                        'mov' => 'video/quicktime',
                        'mp3' => 'audio/mpeg',
                        'mp4' => 'video/mp4',
                        'mpeg' => 'video/mpeg',
                        'mpg' => 'video/mpeg',
                        'pdf' => 'application/pdf',
                        'png' => 'image/x-png',
                        'pot' => 'application/vnd.ms-powerpoint',
                        'pps' => 'application/vnd.ms-powerpoint',
                        'ppt' => 'application/vnd.ms-powerpoint',
                        'qt' => 'video/quicktime',
                        'ra' => 'audio/x-pn-realaudio',
                        'ram' => 'audio/x-pn-realaudio',
                        'rar' => 'application/rar',
                        'rtf' => 'application/rtf',
                        'swf' => 'application/x-shockwave-flash',
                        'tar' => 'application/x-tar',
                        'tgz' => 'application/x-compressed',
                        'tif' => 'image/tiff',
                        'tiff' => 'image/tiff',
                        'txt' => 'text/plain',
                        'xls' => 'application/vnd.ms-excel',
                        'zip' => 'application/zip'
                    );

            // function to check for accpetable file type
            function okFileType($type)
                {
                    // if filetypes array is empty then let everything through
                    if( (isset($GLOBALS['filetypes'])) && (count($GLOBALS['filetypes']) < 1) )
                        {
                            return true;
                        }
                    // if no match is made to a valid file types array then kick it back
                    elseif( (isset($GLOBALS['filetypes'])) && (!in_array($type,$GLOBALS['filetypes'])) )
                        {
                            $_SESSION['MUPerror'][] = $type.' failus nav atļauts augšuplādēt. '.
                                                  $type.' ignorējam.';
                            return false;
                        }
                    // else - let the file through
                    else
                        {
                            return true;
                        }
                }

            // function to check and move file
            function processFile($file, $destination_path)
                {
                    // set full path/name of file to be moved
                    $upload_file = $destination_path.$file['name'];

                    if(file_exists($upload_file))
                        {
                            $_SESSION['MUPerror'][] = $file['name'].' - Fails jau ekssistē. Lūdzu nomainiet faila nosaukumu';
                            return false;
                        }

                    if(!move_uploaded_file($file['tmp_name'], $upload_file))
                        {
                            // failed to move file
                            $_SESSION['MUPerror'][] = 'Kļūda failu augšuplādēšanas procesā pie '.$file['name'].'. Lūdzu mēģiniet vēlreiz';
                            return false;
                        }
                    else
                        {
                            // upload OK - change file permissions
                            chmod($upload_file, 0755);
                            return true;
                        }
                }

            // check to make sure files were uploaded
            $no_files = 0;
            $uploaded = array();
            if (isset($destination_path)) {

            foreach($_FILES as $file)
                {
                    switch($file['error'])
                        {
                            case 0:
                                // file found
                                if($file['name'] != NULL && okFileType($file['type']) != false)
                                    {
                                        // process the file
                                        if(processFile($file,$destination_path) == true)
                                            $uploaded = $file['name'];
                                    }
                                break;

                            case (1|2):
                                // upload too large
                                $_SESSION['MUPerror'][] = 'Faila izmērs ir pārāk liels - '.$file['name'];
                                break;

                            case 4:
                                // no file uploaded
                                break;

                            case (6|7):
                                // no temp folder or failed write - server config errors
                                $_SESSION['MUPerror'][] = 'servera kļūda - pasūdzieties izstrādātājiem par '.$file['name'];
                                break;
                        }
                }
            } // isset $destination_path

        }
?>