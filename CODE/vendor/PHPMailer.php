<?php
namespace PHPMailer\PHPMailer;
class PHPMailer { use Traits; public function __construct($ex=false){} public function isSMTP(){} public function setFrom($a,$b=''){} public function addAddress($a){} public function isHTML($b){} public function send(){return true;} public $Host; public $SMTPAuth; public $Username; public $Password; public $SMTPSecure; public $Port; public $Subject; public $Body; public $AltBody; }
class SMTP {}
class Exception extends \Exception {}
trait Traits {}
?>

