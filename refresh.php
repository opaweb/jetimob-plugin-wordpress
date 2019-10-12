<?php
define('WP_USE_THEMES', false);
(string)$caminho = dirname(__FILE__);
require_once( $caminho.'/../../../wp-blog-header.php' );

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


$jetimob_options = get_option( 'jetimob_option_name' ); 
$api = $jetimob_options['api'];
if(function_exists('exec')){
	$file = exec('curl https://www.jetimob.com/services/'.$api.'/imoveis?a='.generateRandomString().' -o '.$caminho.'/jetimob.json');
    var_dump(generateRandomString());
}
else {
	$file = file_get_contents('https://www.jetimob.com/services/'.$api.'/imoveis?a='.generateRandomString());
	file_put_contents($caminho.'/jetimob.json', $file);    
}

date_default_timezone_set('America/Sao_Paulo');
$date = date('d/m/Y h:i:s a', time());
echo $date.' - operação de download realizada.'.PHP_EOL;
?>