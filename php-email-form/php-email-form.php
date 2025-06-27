<?php
// ✅ Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// ✅ Use PHPMailer classes at the top (outside any function/class)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!class_exists('PHP_Email_Form')) {
  class PHP_Email_Form {
    public $to = '';
    public $from_name = '';
    public $from_email = '';
    public $subject = '';
    public $smtp = array();
    public $ajax = false;
    public $messages = array();

    function add_message($content, $label = '', $length = 0) {
      if (!empty($length) && strlen($content) > $length) {
        $content = substr($content, 0, $length) . '...';
      }
      $this->messages[] = array(
        'label' => $label,
        'content' => $content
      );
    }

    function send() {
      $mail_content = "You have received a new message from your website contact form:\n\n";

      foreach ($this->messages as $msg) {
        $mail_content .= "{$msg['label']}: {$msg['content']}\n";
      }

      $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
      $headers .= "Reply-To: {$this->from_email}\r\n";

      if ($this->ajax) {
        header('Content-Type: text/plain');
      }

      // ✅ Use PHPMailer if SMTP is set
      if (!empty($this->smtp['host'])) {
        $mail = new PHPMailer(true);

        try {
          $mail->isSMTP();
          $mail->Host = $this->smtp['host'];
          $mail->SMTPAuth = true;
          $mail->Username = $this->smtp['username'];
          $mail->Password = $this->smtp['password'];
          $mail->SMTPSecure = $this->smtp['secure'];
          $mail->Port = $this->smtp['port'];

          $mail->setFrom($this->from_email, $this->from_name);
          $mail->addAddress($this->to);
          $mail->Subject = $this->subject;
          $mail->Body = $mail_content;

          $mail->send();
          return 'Message has been sent';
        } catch (Exception $e) {
          return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
      } else {
        // Fallback: PHP mail()
        $result = mail($this->to, $this->subject, $mail_content, $headers);
        return $result ? 'Message sent successfully!' : 'Failed to send message.';
      }
    }
  }
}