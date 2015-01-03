<?php

ini_set('max_execution_time', 1200);
ini_set('memory_limit', '512M');


// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        // echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        // echo "  Fatal error on line $errline in file $errfile";
        // echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        // echo "Aborting...<br />\n";
        Application_Model_Logger::log('My ERROR: ' . $errno . ' ' . $errstr);
        Application_Model_Logger::log('Fatal error on line $errline in file: ' . $errfile);
        exit(1);
        break;

    case E_USER_WARNING:
        // echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
    	Application_Model_Logger::log('My WARNING: ' . $errno . ' ' . $errstr);
        break;

    case E_USER_NOTICE:
        // echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
    	Application_Model_Logger::log('My NOTICE: ' . $errno . ' ' . $errstr);
        break;

    default:
        // echo "Unknown error type: [$errno] $errstr<br />\n";
    	Application_Model_Logger::log('Unknown error type: ' . $errno . ' ' . $errstr);
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}





// Comparison function
function entries_date_sort($a, $b) {
    if ($a['date'] == $b['date']) {
        if($a['id'] == $b['id']) {
            return 0;
        }
        return ($a['id'] < $b['id']) ? -1 : 1;
    }
    return ($a['date'] < $b['date']) ? -1 : 1;
}

function toCamelCase($string)  {    
    if(!function_exists('lcfirst')) {
        function lcfirst($str) {
            return strtolower(substr($str, 0, 1)) . substr($str, 1);
        }
    }

    return lcfirst(str_replace(' ', '', ucwords(strtr($string, '_-', ' '))));    
}

function myCheckDate( $postedDate ) {
   if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $postedDate, $datebit)) {
      return checkdate($datebit[2] , $datebit[3] , $datebit[1]);
   } else {
      return false;
   }
} 

function sanitizeValue($value) 
{
  return (isset($value) ? (is_array($value) ? array_map("trim", 
    array_map("strip_tags", array_map('stripslashes', $value))) : 
    trim(strip_tags(stripslashes($value)))) : NULL);
}

function sanitizeNestedArrays($value) 
{
  if ( !isset($value) ) {
      return NULL;
  } else if (!is_array($value)) {
      return trim(strip_tags(stripslashes($value)));
  } else {
      foreach($value as &$val) {
          $val = sanitizeNestedArrays($val);
      }
      return $value;
  }
}

function scaleImage($imgFile, $size, $sig, $imgMiniature = null)
{
	$imageSig = array(
		'image/pjpeg' => 'imagecreatefromjpeg',
		'image/jpeg' => 'imagecreatefromjpeg',
		'image/jpg' => 'imagecreatefromjpeg',
		'image/png' => 'imagecreatefrompng',
		'image/gif' => 'imagecreatefromgif',
		'image/x-png' => 'imagecreatefrompng'
	);
	$createImage = $imageSig[$sig];
  	$image = is_null($createImage) ? false : $createImage($imgFile);

	if (!$image)
    	return false;
    	
	list($width, $height) = getimagesize($imgFile);
	if ($height >= $width) {
		// vertical images
		$iw = $size['vertical']['iw'];
		$ih = $size['vertical']['ih'];
	} else {
		//horizontal images
		$iw = $size['horizontal']['iw'];
		$ih = $size['horizontal']['ih'];
	}

	if ($width > $iw || $height > $ih) {
    	if ($width / $height > $iw / $ih) {
      		$nw = $iw;
      		$nh = round($height * ($iw / $width));
    	} else {
      		$nh = $ih;
      		$nw = round($width * ($ih / $height));
    	}
	} else {
		$nw = $width;
		$nh = $height;
	}
	
   	$imageSmall = imagecreatetruecolor($nw, $nh);
	imagecopyresampled($imageSmall, $image, 0, 0, 0, 0, $nw, $nh, $width, $height);

        if ($imgMiniature !== null) {
            $url = explode('/',$imgMiniature);
            if (count($url)){
                $i = 2;
                if ($url[0] == '.')
                    $i = 3;
                if (!file_exists('files/'.$url[$i-1]))
                    mkdir('files/'.$url[$i-1]);
                
                if (!file_exists('files/'.$url[$i-1].'/'.$url[$i]))
                    mkdir('files/'.$url[$i-1].'/'.$url[$i]);
            }
                
        }
        
	if (!imagejpeg($imageSmall, ($imgMiniature == null ? $imgFile : $imgMiniature), 100))
       	return false;

	return true; 
}

// function to rotate the image
function rotateImage ($imgFile, $angle, $sig)
{	
	$date_test = date('m/d/Y h:i:s a', time());
	Application_Model_Logger::log('Rotate function timing 3.1rd_image: '.$date_test);

	$imageSig = array(
			'image/pjpeg' => 'imagecreatefromjpeg',
			'image/jpeg' => 'imagecreatefromjpeg',
			'image/jpg' => 'imagecreatefromjpeg',
			'image/png' => 'imagecreatefrompng',
			'image/gif' => 'imagecreatefromgif',
			'image/x-png' => 'imagecreatefrompng'
	);

	$date_test = date('m/d/Y h:i:s a', time());
	Application_Model_Logger::log('Rotate function timing 3.2rd_image: '.$date_test);
	
	$createImage = $imageSig[$sig];
	
	$image = is_null($createImage) ? false : $createImage($imgFile);
	
	if (!$image)
		return false;
	
	$date_test = date('m/d/Y h:i:s a', time());
	Application_Model_Logger::log('Rotate function timing 3.3rd_image: '.$date_test);

	$rotateImage = imagerotate($image, $angle, 0);
	imagejpeg($rotateImage,$imgFile);

	$date_test = date('m/d/Y h:i:s a', time());
	Application_Model_Logger::log('Rotate function timing 3.4rd_image: '.$date_test);

}

// function to update the rotated image in entry edit

function rotateEditImage($imgFile, $angle)
{
	$fileMime = null;

	// check file type
	if (Zend_Registry::get('config')->fileMime->permanentlySet) {
		$fileMime = 'image/jpeg';
	} else {
		// get the real photo type
		if (floatval(phpversion()) < 5.3)
			$finfo = finfo_open(FILEINFO_MIME, "/usr/share/misc/magic");
		else
			// changed because of PHP >= 5.3.11
			$finfo = finfo_open(FILEINFO_MIME);
			
		$fileMime = explode(';', finfo_file($finfo, $imgFile));
		$fileMime = $fileMime[0];
		finfo_close($finfo);
	}
	rotateImage($imgFile, $angle, $fileMime);
}

// function to sort a multidimensional array alphabetically
function multidimensionalArraySort (&$array, $key) {
	$sorter=array();
	$ret=array();
	reset($array);
	foreach ($array as $ii => $va) {
		$sorter[$ii]=$va[$key];
	}
	asort($sorter);
	foreach ($sorter as $ii => $va) {
		$ret[$ii]=$array[$ii];
	}
	$array=$ret;
}

function scaleCropImage($imgFile, $size, $cropSize, $sig, $imgMiniature = null)
{
	$imageSig = array(
		'image/pjpeg' => 'imagecreatefromjpeg',
		'image/jpeg' => 'imagecreatefromjpeg',
		'image/jpg' => 'imagecreatefromjpeg',
		'image/png' => 'imagecreatefrompng',
		'image/gif' => 'imagecreatefromgif',
		'image/x-png' => 'imagecreatefrompng'
	);
	
	$createImage = $imageSig[$sig];
  	$image = is_null($createImage) ? false : $createImage($imgFile);

	if (!$image)
    	return false;
    	
	list($width, $height) = getimagesize($imgFile);

	if ($height >= $width) {
		// vertical images
		$iw = $size['vertical']['iw'];
		$ih = $size['vertical']['ih'];
		
		// crop size
		$ciw = $cropSize['vertical']['iw'];
		$cih = $cropSize['vertical']['ih'];
	} else {
		//horizontal images
		$iw = $size['horizontal']['iw'];
		$ih = $size['horizontal']['ih'];

		// crop size
		$ciw = $cropSize['horizontal']['iw'];
		$cih = $cropSize['horizontal']['ih'];
	}

	// calculate thumbnail size
	if ($width > $iw || $height > $ih) {
    	if ($width / $height > $iw / $ih) {
      		$nw = $iw;
      		$nh = round($height * ($iw / $width));
    	} else {
      		$nh = $ih;
      		$nw = round($width * ($ih / $height));
    	}
	} else {
		$nw = $width;
		$nh = $height;
	}
	
	// check the cropped thumbnail size
	// width
	if ($ciw > $nw)
		$ciw = $nw;
	// height
	if ($cih > $nh)
		$cih = $nh;
		
	// prepare small image	
   	$imageSmall = imagecreatetruecolor($nw, $nh);
	imagecopyresampled($imageSmall, $image, 0, 0, 0, 0, $nw, $nh, $width, $height);
	
	// crop small image
   	$croppedImage = imagecreatetruecolor($ciw, $cih);
	imagecopyresampled($croppedImage, $imageSmall, 0, 0, 0, ($nh - $cih) / 2, $ciw, $cih, $ciw, $cih);	

	imagedestroy($imageSmall);

	if (!imagejpeg($croppedImage, ($imgMiniature == null ? $imgFile : $imgMiniature), 100))
       	return false;

	return true; 
}

function scaleCropCenterImage($imgFile, $size, $cropSize, $sig, $imgMiniature = null)
{
	$imageSig = array(
		'image/pjpeg' => 'imagecreatefromjpeg',
		'image/jpeg' => 'imagecreatefromjpeg',
		'image/jpg' => 'imagecreatefromjpeg',
		'image/png' => 'imagecreatefrompng',
		'image/gif' => 'imagecreatefromgif',
		'image/x-png' => 'imagecreatefrompng'
	);
	
	$createImage = $imageSig[$sig];
  	$image = is_null($createImage) ? false : $createImage($imgFile);

	if (!$image)
    	return false;
    	
	list($width, $height) = getimagesize($imgFile);

	if ($height >= $width) {
		// vertical images
		$iw = $size['vertical']['iw'];
		$ih = $size['vertical']['ih'];
		
		// crop size
		$ciw = $cropSize['vertical']['iw'];
		$cih = $cropSize['vertical']['ih'];
	} else {
		//horizontal images
		$iw = $size['horizontal']['iw'];
		$ih = $size['horizontal']['ih'];

		// crop size
		$ciw = $cropSize['horizontal']['iw'];
		$cih = $cropSize['horizontal']['ih'];
	}

	// calculate thumbnail size
	if ($width > $iw || $height > $ih) {
    	if ($width / $height > $iw / $ih) {
      		$nw = $iw;
      		$nh = round($height * ($iw / $width));
    	} else {
      		$nh = $ih;
      		$nw = round($width * ($ih / $height));
    	}
	} else {
		$nw = $width;
		$nh = $height;
	}
	
	// check the cropped thumbnail size
	// width
	if ($ciw > $nw)
		$ciw = $nw;
	// height
	if ($cih > $nh)
		$cih = $nh;
		
	// prepare small image	
   	$imageSmall = imagecreatetruecolor($nw, $nh);
	imagecopyresampled($imageSmall, $image, 0, 0, 0, 0, $nw, $nh, $width, $height);
	
	// crop small image
   	$croppedImage = imagecreatetruecolor($ciw, $cih);
	imagecopyresampled($croppedImage, $imageSmall, 0, 0, ($nw - $ciw) / 2, ($nh - $cih) / 2, $ciw, $cih, $ciw, $cih);	

	imagedestroy($imageSmall);

	if (!imagejpeg($croppedImage, ($imgMiniature == null ? $imgFile : $imgMiniature), 100))
       	return false;

	return true; 
}

function validateEmailAddress($emailAddress)
{
	return (!preg_match('/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-z]{2,6}$/', $emailAddress) ?
		false : true);
}

function getAllowedExtensions($fileMimeTypes)
{
	// list of valid extensions		
	// documents
	$allowedExtensions = array_values($fileMimeTypes['documents']);
	// images
	$allowedExtensions = array_merge($allowedExtensions, array_values($fileMimeTypes['images']));
	// videos
	$videoExts = array();
	foreach (array_values($fileMimeTypes['videos']) as $e) {
		// special fix for mov and qt ext
		if (strpos($e, ' ') !== false) {
			$fe = explode(' ', $e);

			foreach ($fe as $f)
				$videoExts[] = $f;
		} else 
			$videoExts[] = $e;
	}	
	
	$allowedExtensions = array_unique(array_merge($allowedExtensions, $videoExts));

	return $allowedExtensions;
}

/**
 * Write data to csv file. This function is original used to export reports to csv file
 * @param string $filePath The file path. If null, a file with random name will be created instead
 * @param type $headers Header of csv file
 * @param type $data Row data array(array(<data of row1>), array(<data of row2>), ...)
 * @return string The file path of csv file
 */
function writeToCsv($filePath=null, $headers=array(), $data=array()) {
    if (empty($filePath)) {
	// Create a new temp file
	$filePath = 'files/'.uniqid().'.csv';
	
    }
    if (empty($data)) {
	return null;
    }
    $handle = fopen($filePath, 'w');
    
    // Write headers first
    if (!empty($headers)) {
	fputcsv($handle, $headers);
    }
    foreach($data as $row) {
	fputcsv($handle, $row);
    }
    fclose($handle);
    return $filePath;
}

/**
 * Force download a file. Used originally to export reports to csv files
 * @param type $filePath Path to the file to download
 * @param type $deleteFileAfterDownload Whether to delete file after download or not. Default to false
 */
function downloadFile($filePath, $deleteFileAfterDownload=false) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($filePath));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    ob_clean();
    flush();
    readfile($filePath);
    // Delete csv file
    if ($deleteFileAfterDownload) {
	unlink($filePath);
    }
    exit;
}

// generate password
function generatePassword($length = 8)
{
	$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789abdefhiknrstyz23456789';

	$charsLength = strlen($chars);

	$password = '';
	for ($i = 0; $i < $length; $i++)
		$password .= substr($chars, rand(1, $charsLength) - 1, 1);

	return $password;
}

function getExcerpt($content, $limit = 50)
{
	$textfield = strtok($content, " ");
	while ($textfield) {
		$text .= " $textfield";
		$words++;
		if (($words >= $limit) && ((substr($textfield, -1) == "!") || (substr($textfield, -1) == "."))) {
	 		break;
		}

		$textfield = strtok(" ");
	}

	if (strlen($text) < strlen($content))
		$text .= ' [...]';

	return ltrim($text);
}

function sendEmail($to, $message, $subject, $from, $postmarkKey)
{

	require_once(dirname(__FILE__) . '/../Postmark/postmark.php');

	// $from = $from ? $from : 'Tweekaboo <support@tweekaboo.com>';
	// $subject = $subject ? $subject : 'Someone has shared a moment with you!';

	$postmark = new Postmark($postmarkKey, 'Tweekaboo <support@tweekaboo.com>', $from);
	
	try {

		$result = $postmark->to($to)
					   	   ->subject($subject)
						   ->plain_message($message['text'])
						   ->html_message($message['html'])
						   ->send();
	
	if($result === true)

		echo "Message sent\n";

	else

		echo "Not Send !!!!!!!!!!!!!!!\n";
	}

	catch(Exception $e){
		echo "error: " . $e . "\n";
	}
	

	return $result === true ? true : false;

}

// S3 Upload/Download/Bulk Upload or Local Store for localhost Development
///////////////////////////////////////////////////////////////////////////
function getObject($s3BucketName, $s3AccessKey, $s3Secret, $destFileName, $sourceFileName, $storeRemotely) {

	if ($storeRemotely) {
		require 'AWSSDKforPHP/aws.phar';
		$client = Aws\S3\S3Client::factory(array(
			'key'    => $s3AccessKey,
			'secret' => $s3Secret
		));

		try {
		    $result = $client->getObject(array(
			'Bucket' => $s3BucketName,
			'Key'    => $sourceFileName,
			'SaveAs' =>	$destFileName
			));
		} catch (Exception $e) {
		   // I can put a nicer error message here  
			Application_Model_Logger::log('Error: getObject: '.$e);
		}
	}
}

function saveObject($s3BucketName, $s3AccessKey, $s3Secret, $destFileName, $sourceFileName, $storeRemotely) {	
	if ($storeRemotely) {
		require 'AWSSDKforPHP/aws.phar';
		$client = Aws\S3\S3Client::factory(array(
			'key'    => $s3AccessKey,
			'secret' => $s3Secret
		));
	    // FIXME: Test this result
		$result = $client->putObject(array(
		    'Bucket'     => $s3BucketName,
		    'Key'        => $destFileName,
		    'SourceFile' => $sourceFileName
		));
		$client->waitUntilObjectExists(array(
	        'Bucket' => $s3BucketName,
	        'Key' => $destFileName
	    ));     
	    unlink($sourceFileName);
    }
}

function uploadDirectory($s3BucketName, $s3AccessKey, $s3Secret, $s3UploadConcurrency, $s3UploadFolder, $s3SourceFolder, $storeRemotely, $entryId) {

	// always check the entryId not be null.
	// in case two people in the same family upload the photos at the same time
	assert($entryId != null);

	if ($storeRemotely) {
		
		require 'AWSSDKforPHP/aws.phar';
		
		$client = Aws\S3\S3Client::factory(array(
			'key'    => $s3AccessKey,
			'secret' => $s3Secret
		));

		$options = array(
			'params'      => array('ACL' => 'public-read'),
			'concurrency' => $s3UploadConcurrency,
			'debug'       => false,
		);

		try {
			// Generate temp folder
			// security hash for a folder name
			$folderNameHash = sha1(mt_rand() . time());
			$s3SourceFolderTemp = $s3SourceFolder . $folderNameHash . '/';
			if (!file_exists($s3SourceFolderTemp))
		 	   mkdir($s3SourceFolderTemp, 0777, true);

			// Get and move files into temp folder
			$fileTemplate = 'entry-' . $entryId . '*.jpg';
	    	$files = glob($s3SourceFolder . $fileTemplate);
			foreach($files as $file) { // iterate files
				if(is_file($file)) {
					$path_parts = pathinfo($file);
					rename($file, $s3SourceFolderTemp . $path_parts['basename']);
				}
			}
			// Upload temp directory into S3
			$client->uploadDirectory($s3SourceFolderTemp, $s3BucketName, $s3UploadFolder, $options);

			// Remove files and temp directory
	    	$files = glob($s3SourceFolderTemp . $fileTemplate);
			foreach($files as $file) { // iterate files
				if(is_file($file)) {
					unlink($file); // delete file
				}
			}
			rmdir($s3SourceFolderTemp);
		}
		catch (Exception $e) {
		   // I can put a nicer error message here  
			Application_Model_Logger::log('uploadDirectory in functions.php: '.$e);
		}
	}
}

function scaleImageTransloadit($imgFile, $size, $imgMiniature = null)
{
      
  list($width, $height) = getimagesize($imgFile);

  if ($height >= $width) {
    // vertical images
    $iw = $size['vertical']['iw'];
    $ih = $size['vertical']['ih'];
  } else {
    //horizontal images
    $iw = $size['horizontal']['iw'];
    $ih = $size['horizontal']['ih'];
  }

  if ($width > $iw || $height > $ih) {
      if ($width / $height > $iw / $ih) {
          $nw = $iw;
          $nh = round($height * ($iw / $width));
      } else {
          $nh = $ih;
          $nw = round($width * ($ih / $height));
      }
  } else {
    $nw = $width;
    $nh = $height;
  }
  
  $res = array();
  $res['thumbWidth'] = $nw;
  $res['thumbHeight'] = $nh;

  return $res;

}

function scaleCropImageTransloadit($imgFile, $size, $cropSize, $imgMiniature = null)
{
      
  list($width, $height) = getimagesize($imgFile);

  if ($height >= $width) {
    // vertical images
    $iw = $size['vertical']['iw'];
    $ih = $size['vertical']['ih'];
    
    // crop size
    $ciw = $cropSize['vertical']['iw'];
    $cih = $cropSize['vertical']['ih'];
  } else {
    //horizontal images
    $iw = $size['horizontal']['iw'];
    $ih = $size['horizontal']['ih'];

    // crop size
    $ciw = $cropSize['horizontal']['iw'];
    $cih = $cropSize['horizontal']['ih'];
  }

  // calculate thumbnail size
  if ($width > $iw || $height > $ih) {
      if ($width / $height > $iw / $ih) {
          $nw = $iw;
          $nh = round($height * ($iw / $width));
      } else {
          $nh = $ih;
          $nw = round($width * ($ih / $height));
      }
  } else {
    $nw = $width;
    $nh = $height;
  }
  
  // check the cropped thumbnail size
  // width
  if ($ciw > $nw)
    $ciw = $nw;
  // height
  if ($cih > $nh)
    $cih = $nh;

  $res = array();
  $res['cropThumbWidth'] = $ciw;
  $res['cropThumbHeight'] = $cih;

  return $res;

}

function getFileSizeArray($filePath, $fileMime)
{

  $res = array();

  $res['s'] = scaleCropImageTransloadit($filePath, array('vertical' => array('iw' => 260, 'ih' => 600),
              'horizontal' => array('iw' => 260, 'ih' => 600)), array('vertical' => array('iw' => 260, 'ih' => 200),
              'horizontal' => array('iw' => 260, 'ih' => 200)), $fileMime);

  $res['ms'] = scaleImageTransloadit($filePath, array('vertical' => array('iw' => 75, 'ih' => 100),
              'horizontal' => array('iw' => 100, 'ih' => 75)), $fileMime);

  $res['m'] = scaleImageTransloadit($filePath, array('vertical' => array('iw' => 300, 'ih' => 380),
              'horizontal' => array('iw' => 380, 'ih' => 300)), $fileMime);

  $res['mm'] = scaleImageTransloadit($filePath, array('vertical' => array('iw' => 220, 'ih' => 300),
              'horizontal' => array('iw' => 300, 'ih' => 220)), $fileMime);

  $res['n'] = scaleImageTransloadit($filePath, array('vertical' => array('iw' => 600, 'ih' => 800),
              'horizontal' => array('iw' => 800, 'ih' => 600)), $fileMime);

  $res['rs'] = scaleImageTransloadit($filePath, array('vertical' => array('iw' => 640, 'ih' => 960),
              'horizontal' => array('iw' => 640, 'ih' => 960)), $fileMime);

  $res['rs@2x'] = scaleImageTransloadit($filePath, array('vertical' => array('iw' => 1280, 'ih' => 1920),
              'horizontal' => array('iw' => 1280, 'ih' => 1920)), $fileMime);

  $res['rf'] = scaleCropImageTransloadit($filePath, array('vertical' => array('iw' => 279, 'ih' => 837),
              'horizontal' => array('iw' => 279, 'ih' => 624)), array('vertical' => array('iw' => 279, 'ih' => 279),
              'horizontal' => array('iw' => 279, 'ih' => 208)), $fileMime);

  $res['rf@2x'] = scaleCropImageTransloadit($filePath, array('vertical' => array('iw' => 558, 'ih' => 1674),
              'horizontal' => array('iw' => 558, 'ih' => 1251)), array('vertical' => array('iw' => 558, 'ih' => 558),
              'horizontal' => array('iw' => 558, 'ih' => 417)), $fileMime);

  return $res;

}

function translateEmailSubjectHelper($emailSubject, $locale=false)
{
	// Translate the subject of email.
	$registry = Zend_Registry::getInstance();
	if (!$locale){
		$locale = $registry->get('Zend_Locale');	
	}

	$trans=new Zend_Translate('array', 'lang', $locale, array('scan' => Zend_Translate::LOCALE_FILENAME));
	$translatedEmailSubject = $trans->translate($emailSubject);

	return $translatedEmailSubject;

}

function convertLocalisationImg($locale,$imgURL)
{
	
	$path = explode("/", $imgURL);
	$length = count($path);

	$newImgURL = $path[0] . '/' . $locale . '/' . $path[$length-1];

	if (file_exists($newImgURL)) {
	    $res = $newImgURL;
	} else {
	    $res = $imgURL;
	}

	return $res;
}

function setToUtf8($text) {
	//Replace Unicode characters (\u) in UTF-8 characters
	return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $text);
}

?>