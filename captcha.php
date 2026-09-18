<?php
include('include/session.php');
header("Content-type: image/jpeg");

$length = 6;
$ret_var = "";
if (isset($_SESSION['captcha'])) {
	unset($_SESSION['captcha']);
}


$num1 = rand(0, 9);
$num2 = rand(0, 99);

//$captcha_num = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
$captcha_num = $num1 . "+" . $num2 . "=";
$_SESSION['captcha'] = $num1 + $num2;

if (isset($_SESSION['captcha'])) {

	print_r($_SESSION['captcha']);exit;

	$img = imagecreate(70, 25);

	$background_color = imagecolorallocate($img, 0, 0, 0);
	imagefilledrectangle($img, 0, 0, 200, 50, $background_color);


	$textbgcolor = imagecolorallocate($img, 5, 5, 5);
	$textcolor = imagecolorallocate($img, 255, 255, 255);

	if ($_SESSION['captcha'] != '') {
		$txt = $captcha_num;
		imagestring($img, 30, 8, 0, $txt, $textcolor);
		ob_start();
		imagepng($img);
		printf('<img src="data:image/png;base64,%s"/ width="120">', base64_encode(ob_get_clean()));
	}
}
