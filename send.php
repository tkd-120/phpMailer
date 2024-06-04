<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match('/[\r\n]/', $email);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    if (!isValidEmail($email)) {
        die('Invalid email address');
    }

    $mail = new PHPMailer(true);

    try {
        // SMTPサーバー設定
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];  // 環境変数からSMTPサーバーを指定
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];  // 環境変数からSMTPユーザー名を指定
        $mail->Password = $_ENV['SMTP_PASSWORD'];  // 環境変数からSMTPパスワードを指定
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->CharSet = 'UTF-8';

        // 送信者情報
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'お問い合わせフォーム');

        // 視聴者への自動返信メール
        $mail->addAddress($email, $name);  // 視聴者
        $mail->isHTML(true);
        $mail->Subject = 'お問い合わせありがとうございます';
        $mail->Body    = "こんにちは $name さん,<br><br>お問い合わせありがとうございます。以下の内容で受け付けました。<br><br>---<br>名前: $name<br>メールアドレス: $email<br>本文: $message<br>---<br><br>折り返しご連絡いたしますので、しばらくお待ちください。";
        $mail->AltBody = "こんにちは $name さん,\n\nお問い合わせありがとうございます。以下の内容で受け付けました。\n\n---\n名前: $name\nメールアドレス: $email\n本文: $message\n---\n\n折り返しご連絡いたしますので、しばらくお待ちください。";
        $mail->send();

        // メール設定のリセット
        $mail->clearAddresses();
        $mail->clearAttachments();

        // 自分への通知メール
        $mail->addAddress($_ENV['SMTP_HOSTNAME'], 'TAKADA');
        $mail->Subject = '新しいお問い合わせがありました';
        $mail->Body    = "新しいお問い合わせがあります。以下の内容をご確認ください。<br><br>---<br>名前: $name<br>メールアドレス: $email<br>本文: $message<br>---";
        $mail->AltBody = "新しいお問い合わせがあります。以下の内容をご確認ください。\n\n---\n名前: $name\nメールアドレス: $email\n本文: $message\n---";
        $mail->send();

        echo 'メッセージが送信されました';
    } catch (Exception $e) {
        echo "メッセージを送信できませんでした。エラー: {$mail->ErrorInfo}";
    }
}
?>