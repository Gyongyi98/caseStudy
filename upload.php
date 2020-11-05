<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__ . '/quickstart.php';
include('ScreenshotMachine.php');

$customer_key = "my secret phrase :)";
$secret_phrase = ""; //leave secret phrase empty, if not needed

$machine = new ScreenshotMachine($customer_key, $secret_phrase);

$folder_id = "1LEk0T1XEM6TUsvcovhagO7VtSbwHFv66";
$folder_name = "photos";

//The $sources associative array contains the urls, where the screenshot has to be taken, and the main part of the file name
$sources= array("https://ifunded.de/en/" => "iFunded","https://www.propertypartner.co" => "PropertyPartner",
  "https://propertymoose.co.uk" => "PropertyMoose", "https://www.homegrown.co.uk" => "Homegrown","https://www.realtymogul.com" => RealtyMogul);

//Parameters for the screenshot
$options['dimension'] = "1920x1080";
$options['device'] = "desktop";
$options['format'] = "jpg";
$options['cacheLimit'] = "0";
$options['delay'] = "400";
$options['zoom'] = "100";

//Get all of the URLs and names from $sources in order to set the url of every website
//and set the main part of the filname to every screenshot
$id = 0;
foreach($sources as $url => $name) {
  $id = $id + 1;
  $options['url'] = $url; //the most important and necessary parameter of the screenshot, it's source

  $file_name = $id . "_" . $name . ".jpg";  //creating the full filename: ID_name.jpg

  $api_url = $machine->generate_screenshot_api_url($options); //generating screenshot url

  $success = insert_file_to_drive( $api_url , $file_name, $folder_id);  //uploading screenshot to Google Drive

  //check if uplading was successful
  if( $success ){
      echo $file_name . " uploaded successfully\n" ;
  } else {
      echo "Something went wrong.";
  }
}

function insert_file_to_drive( $file_path, $file_name, $parent_file_id = null){
    $service = new Google_Service_Drive( $GLOBALS['client'] );
    $file = new Google_Service_Drive_DriveFile();

    $file->setName( $file_name );

    if( !empty( $parent_file_id ) ){
        $file->setParents( [ $parent_file_id ] );
    }

    $result = $service->files->create(
        $file,
        array(
            'data' => file_get_contents($file_path),
            'mimeType' => 'application/octet-stream',
        )
    );

    $is_success = false;

    if( isset( $result['name'] ) && !empty( $result['name'] ) ){
        $is_success = true;
    }

    return $is_success;
}
?>
