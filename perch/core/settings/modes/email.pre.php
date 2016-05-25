<?php

    $Form 	= new PerchForm('email', false);

    $Email = new PerchEmail('settings-test');


    $req = array();
    $req['email']      = "Required";

    $Form->set_required($req);
    
    

    if ($Form->posted() && $Form->validate()) {

		$data		= array();
		$postvars 	= array('email');
		$data = $Form->receive($postvars);

		
		
        $Email->subject('Email settings test message');
        
        $Email->recipientEmail($data['email']);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);

        if ($Email->send()) {
            $Alert->set('success', PerchLang::get('The email has been successfully sent.'));
        }else{
            $Alert->set('error', $Email->errors);
        }
		
		
		

    }
    

?>