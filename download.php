<?php
require_once( dirname(__FILE__) . '/../inc/config.php' );
require_once( dirname(__FILE__) . '/../inc/contentsConfig.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.contentsDB.class.php' );
require_once( dirname(__FILE__) . '/./zip.lib.php' );

//unset($_POST);
//$_POST['productNum'] = '01';

//$_POST['download']['701297.jpg|007-1-pdf6n.pdf|008-1-pdf6n.pdf|008-2-pdf6n.pdf|008-3-pdf6n.pdf||'] = 1;
//$_POST['download']['011-1-pdf13n.dxf'] = 1;
//$_POST['download']['011-1-pdf13n.jww'] = 1;

//ƒJƒ^ƒƒO mode -> 1
//}–Ê     mode -> 2

$query           = new contentsDB();

$fileRootPath = ROOT_PATH . '/upImage/product/';


$fileArray = $query->GetCaseImg( $_GET['id'] );


//print_r($fileArray);

$zipFile = new zipfile();

foreach( $fileArray AS $key => $value )
{
    if( file_exists( $fileRootPath . $value['fileName'] ) )
    {
        $handle = fopen( $fileRootPath . $value['fileName'], "rb" );
        $targetFile = fread( $handle, filesize( $fileRootPath . $value['fileName'] ) );

        $zipFile -> addFile( $targetFile, 'data/' . $value['fileName'] );
    }
}

/*
$fileName = '008-2-pdf6n.pdf';
$handle = fopen( $fileRootPath . $fileName, "rb" );
$targetFile = fread($handle,filesize( $fileRootPath . $fileName ) );
$zipFile->addFile( $targetFile, './data/' . $fileName );
*/
if( $_SESSION['member']['flg'] )
{
    $query->SetZipDownloadMember( $_SESSION['member']['memberId'], $_GET );

    header( "Content-type: application/zip" );
    //header( "Content-Type: application/octet-stream" );
    header( "Content-Disposition: attachment; filename=data_" . $_GET['id'] . ".zip" );
    header( 'Content-Length: ' . strlen( $zipFile->file() ) );

    print $zipFile->file();
}
else
{
    header( 'Location: ./detail.php?id=' . $_GET['id'] );
    die;
}
?>