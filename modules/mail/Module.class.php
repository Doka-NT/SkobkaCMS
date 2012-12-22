<?php

class Mail {

    public static function Init() {
        
    }

    public static function Send($id, $to, $subject, $body) {
        $aMail = array(
            'id' => $id,
            'to' => $to,
            'subject' => $subject,
            'body' => array($body),
        );
        Event::Call('MailAlter', $aMail);
        self::_Send($aMail);
    }

    private static function _Send($aMail) {
        $body = implode("", $aMail['body']);
        $subject = $aMail['subject'];
        $to = $aMail['to'];
        $to = 'r9122249017@gmail.com';
        $template = Variable::Get('mail_template');
        if ($template) {
            ob_start();
            eval("?> " . $template . " <?");
            $body = ob_get_contents();
            ob_end_clean();
        }
        $headers = self::Headers($aMail);
        mail($to, $subject, $body,$headers);
    }

    private static function Headers($aMail) {
        global $site_name;
        $from = Variable::Get('mail_from', 'admin@' . $_SERVER['SERVER_NAME']);
        
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=urf-8";
        $headers[] = "From: $site_name <$from>";
        $headers[] = "Reply-To: $site_name <$from>";
        $headers[] = "Subject: {$aMail['subject']}";
        
        return implode("\r\n",$headers);
    }

}