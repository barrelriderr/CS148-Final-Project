<?php

function send_mail($to, $sub, $content, $send_info) {

	$site_email = "";

	$from = null;
    $reply_to = null;
    $cc = null;
    $bcc = null;
    
    // Implode any arrays.
    if ($send_info !== null) {
		foreach ($send_info as $key => $item) {
		    if (is_array($item))
				$send_info[$key] = implode(', ', $item);
		}

		// Extract vars.
		$from = $send_info['from'];
		$reply_to = $send_info['reply_to'];
		$cc = $send_info['cc'];
		$bcc = $send_info['bcc'];
    }

	$headers = "From: Buildr Post <" . $site_email . ">\r\n";
	
	$headers .= "Reply-To: " . $site_email . "\r\n";

    // CC:
    $headers .= ($cc != null ? ("CC: " . $cc . "\r\n") : null);

    // BCC:
    $headers .= ($bcc != null ? ("Bcc: " . $bcc . "\r\n") : null);

    $msg = wordwrap($content, 60);

    return mail($to, $sub, $msg, $headers);
}