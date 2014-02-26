<?php
$file = 'http://ww2mysql.wildwood.org/elementary/PDFs/Spring_2012_Progress_Reports.zip';
$newfile = $_SERVER['DOCUMENT_ROOT'] . '/elementary/Spring_2012_Progress_Reports.zip';
echo $newfile;
if ( copy($file, $newfile) ) {
    echo "Copy success!";
}else{
    echo "Copy failed.";
}
?>