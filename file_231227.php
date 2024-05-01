<?php
require_once( dirname(__FILE__) . '/../inc/config.php' );
require_once( dirname(__FILE__) . '/../inc/contentsConfig.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.contentsDB.class.php' );

$query = new contentsDB();

$fileName = $query->GetCulumnContents( 'product', 'dlfileName', 'id', $_GET['id'], 1 );

$file = file_get_contents( dirname(__FILE__) . '/../upImage/product/' . $_GET['fileName'] );

$finfo = new finfo( FILEINFO_MIME_TYPE );
$finfofile = $finfo->file( dirname(__FILE__) . '/../upImage/product/' . $_GET['fileName'] );

if( $_GET['mode'] == 1 )
{
    $num = $query->GetCulumnContents( 'product_catalog', 'priority', 'imgId', $_GET['imgId'], 1 );
    $changeFlg = $query->GetCulumnContents( 'product_catalog', 'changeFlg', 'imgId', $_GET['imgId'], 1 );
    $changeFlg = $query->GetCulumnContents( 'product_catalog', 'changeFlg', 'imgId', $_GET['imgId'], 1 );
    $dwFileName = $query->GetCulumnContents( 'product_catalog', 'dwFileName', 'imgId', $_GET['imgId'], 1 );
    $genre = 'catalog';
}
if( $_GET['mode'] == 2 )
{
    $num = $query->GetCulumnContents( 'product_drawing', 'priority', 'imgId', $_GET['imgId'], 1 );
    $changeFlg = $query->GetCulumnContents( 'product_drawing', 'changeFlg', 'imgId', $_GET['imgId'], 1 );
    $dwFileName = $query->GetCulumnContents( 'product_drawing', 'dwFileName', 'imgId', $_GET['imgId'], 1 );
    $genre = 'drawing';
}

if ($_SESSION['member']['eMail']) {
    $name = $_GET['name'];
    $imageAlt = $_GET['imageAlt'];
    $url = 'https://ma.moritaalumi.co.jp/l/1048092/2023-12-11/cvn';
    $data = array(
      'email' => $_SESSION['member']['eMail'],
      'dl_document_name' => $name . $imageAlt,
    );
    $options = array('http' => array(
      'method' => 'POST',
      'content' => http_build_query($data),
    ));
    $contents = file_get_contents
    ($url, false, stream_context_create($options));
}

if( $_SESSION['member']['flg'] )
{
    if (!$_GET['fileName'] || $changeFlg) {
        header('Location: ' . $dwFileName);
        die();
    } else {
        $query->SetDownloadMember( $_SESSION['member']['memberId'], $_GET );

        @preg_match( '/\.[a-z]+$/i', $_GET['fileName'], $matches );
        $dlFileName = ( $fileName ) ? $fileName . '_' . $genre . '_' . $num . $matches[0] : $_GET['fileName'];
        if( $dwFileName )$dlFileName = $dwFileName . $matches[0];

        header( "Cache-Control: public" );
        header( "Pragma: public" );
        header( "Content-Type: " . $finfofile );
        header( "Content-Disposition: attachment; filename=" . $dlFileName );
        header( "Content-Length:" . strlen( $file ) );
        @readfile( $_GET['fileName'] );

        echo $file;
    }
}
else
{
    header( 'Location: ./detail.php?id=' . $_GET['id'] );
    die;
}
?>
