<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Validate XML</title>
    </head>
    <body>
        <form action="validatexml.php" method="post" enctype="multipart/form-data">
            <label for="file">Filename:</label>
            <input type="file" name="file" id="file"/>
            <input type="hidden" name="upload" id="upload" value="1" />
            <br/>
            <input type="submit" name="submit" value="Submit"/>
        </form>
    </body>
</html>


<?php

##############################
## Develop by LuciferUltraM
##############################

##################### Function Display XML Error ###########################
function libxml_display_error($error) {
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .= " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}



##################### Upload XML file ###########################
if ($_POST['upload'] == '1' && !empty($_FILES["file"]["name"])) {

    if ($_FILES["file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br/>";
    } else {
        echo "Upload: " . $_FILES["file"]["name"] . "<br/>";
        echo "Type: " . $_FILES["file"]["type"] . "<br/>";
        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br/>";
        echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br/>";
        copy($_FILES['file']['tmp_name'], "upload/".$_FILES["file"]["name"]);
        echo "Copy file: " ."upload/".$_FILES["file"]["name"]. "<br/>";
        $xmlname = "upload/".$_FILES["file"]["name"];

// Enable user error handling
        libxml_use_internal_errors(true);

        $xml = new DOMDocument();
        $xml->load($xmlname);
        libxml_display_errors();
    
##################### Read file & Check Special Character ###########################
$handle = @fopen($xmlname, "r");
if ($handle) {
    $line = 0;
    //$pattern = '/[^\w^\d^\s^\.^\-^\+^\/^\=^\#^\(^\)^\<^\>^\"^\,^\%^\&^\?^\;^ก-ฮ]{1}/';
    $pattern = '/[^\w^\d^\s^\.^\-^\+^\/^\=^\#^\(^\)^\<^\>^\"^ก-ฮ]{1}/';
    while (($buffer = fgets($handle)) !== false) {
        $line++;
        if(preg_match($pattern, $buffer, $matches, PREG_OFFSET_CAPTURE))
        {
            //print_r($matches);
            
            echo "Line : ".$line." =>  ";
            foreach($matches as $found)
            {
                echo $found[0]." col : ".$found[1];
            }
            echo "<br/>";
        }
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}
    
    
    }
}




?>