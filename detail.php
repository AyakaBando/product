<?php
require_once( dirname(__FILE__) . '/../inc/config.php' );
require_once( dirname(__FILE__) . '/../inc/contentsConfig.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.systemSaveDB.class.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.systemDB.class.php' );
require_once( dirname(__FILE__) . '/../inc/lib/DB/.contentsDB.class.php' );
//print_r($_SESSION['member']);
define( 'THE_DIR_NAME',   'product' );
define( 'THE_FILE_NAME',  'detail' );

$evaluationArray = evaluationArray( 1 );
$query           = new contentsDB();
$systemquery     = new systemDB();

$data = $query->ContentsDetail( $_GET['id'] );

//print_r($data);
//print_r($_SESSION);

/*
if( $_SESSION['member']['flg'] && $_SERVER['REDIRECT_HTTPS'] != 'on' )
{
    header( 'Location: https://www.moritaalumi.co.jp/product/detail.php?id=' . $_GET['id'] );
    die;
}
*/

if( $_POST['rdel'] )
{
    $query->_setQuery( 'query', "DELETE FROM `product_review` WHERE `rId` = ? LIMIT 1", array( $_POST['r'] ) );
    header( 'Location: ./detail.php?id=' . $_GET['id'] );
    die;
}

if( $_SESSION['member']['flg'] )
{

    $query->_setQuery( 'query', "INSERT `memberPageView` ( `id`, `memberId`, `dateTime` ) VALUES ( ?, ?, ? )",
        array( $_GET['id'],$_SESSION['member']['memberId'], date( 'Y-m-d H:i:s' ) ) );
}

$form = new HTML_QuickForm( 'login', 'post' );

$form->addElement( 'select',      'evaluation',    '評価',       $evaluationArray );
$form->addElement( 'text',        'title',         'タイトル',   array( 'class' => 'wS imeOn' ) );
$form->addElement( 'textarea',    'comment',       '内容',       array( 'rows' => 8 ) );

$form->addElement( 'submit',      'submitReg',     '投稿する',   array( 'class' => 'kakunin' ) );

$form->setDefaults();

//$form->addRule( 'evaluation',   '評価を選択してください。',     'nonzero',  null );
$form->addRule( 'title',        'タイトルを入力してください。', 'required', null );
$form->addRule( 'comment',      '内容を入力してください。',     'required', null );

if( isset( $_POST['submitReg'] ) )
{
}

$form->setRequiredNote( '<span style="font-size:80%; color:#ff0000;">下記</span><span style="font-size:80%;">の項目は必ず入力してください。</span>' );
$form->setJsWarnings( '下記の項目は必ず入力してください。', "\n\n" . TITLE );

if( $form->validate() && isset( $_POST['submitReg'] ) )
{
    $_POST['memberId'] = $_SESSION['member']['memberId'];
    $_POST['name']     = $_SESSION['member']['name'];
    $_POST['dateTime'] = date( 'Y-m-d H:i:s' );
    $_POST['id']       = $_GET['id'];
    $_POST['dispFlg']  = 0;
    //DB登録修正処理
    $saveParam = array(
        'tableName'     => 'product_review',
        'data'          => $_POST,
        'anData'        => array( 'submitConf', 'submitReg', 'submitReturn', 'reset', 'rId', 'MAX_FILE_SIZE', 'imageDel', 'fileName' ),
        'connectionKey' => array(),
        'timeKey'       => array(),
        'dateKey'       => array(),
        'dateTimeKey'   => array( ),
        'fileArray'     => /*$fileArray*/array(),
        'fileAnData'    => array( /*'imageDel', 'pictureOutsideFlg'*/ ),
        'lastFlg'       => 1,
        'id'            => $_POST['rId'],
        'idName'        => 'rId',
        'limitFlg'      => 1,
    );

    $save = new CreatQueryDB();
    $save->_setParam( $saveParam );
    $id = $save->Save();


    $adminBody  = '========================================='       . "\n";
    $adminBody .= $data['name'] . 'にレビュー投稿がありました。'    . "\n";
    $adminBody .= '========================================='       . "\n";
    $adminBody .= $_POST['name'] . '様よりの投稿内容は'             . "\n";
    $adminBody .= '以下の通りです。'                                . "\n";
    $adminBody .= '========================================='       . "\n";
    $adminBody .= $_POST['comment']                                 . "\n";
    $adminBody .= '========================================='       . "\n";

    //$mailFrom    = base64_encode( '' ) . '?= <' . COMPANY_MAIL_FROM . '>';

    $mailHeader   = "From: "        . COMPANY_MAIL_FROM . "\n";
    //$mailHeader  .= "Return-Path: " . COMPANY_MAIL_FROM . "\n";
    $mailHeader  .= "MIME-Version: 1.0\n";
    $mailHeader  .= 'Content-Type: text/plain; charset=UTF-8' . "\n";
    $mailHeader  .= "Content-Transfer-Encoding: 8bit\n";
    $mailHeader  .= "X-mailer: PHP/" . phpversion();

    $adminMailSubject = $data['name'] . 'にレビュー投稿がありました。';
    $adminMailSubject = "=?UTF-8?B?" . base64_encode( $adminMailSubject ) . "?=";

    //管理者メール
    //mail( 'yonekura@queserser.co.jp', $adminMailSubject, $adminBody, $mailHeader, ERROR_MAIL );
    mail( 'info-dl@moritaalumi.co.jp', $adminMailSubject, $adminBody, $mailHeader, $errorMail );

    header( 'Location: ./detail.php?id=' . $_GET['id'] . '&r=1' );
    die;
}

$smarty->template_dir = SMARTY_TEMPLATE_PATH    . THE_DIR_NAME;
$smarty->compile_dir  = SMARTY_TEMPLATE_C_PATH  . THE_DIR_NAME;

$renderer = new HTML_QuickForm_Renderer_ArraySmarty( $smarty );
$form->accept( $renderer );
$smarty->assign( 'form', $renderer->toArray() );

$smarty->assign( 'passwordStr',  $passwordStr );
$smarty->assign( 'flg',          $flg );
$smarty->assign( 'data',          $data );

$smarty->display( THE_FILE_NAME . '.html' );
?>