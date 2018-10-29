<p>Dear <? echo ucwords($user->username) ?></p>

<p>Welcome to the online immigration tracker! This handy tool will keep you posted every step of the way as we assist you in preparing your visa application.</p>

<p>This system has been carefully designed to offer you one central place to upload your documentation, receive updates and information on items outstanding, thus working as your own personal organiser</p>

<p>USERNAME: <? echo $username ?></p>
<p>PASSWORD: <? echo $password ?></p>

<p>Be sure to keep your account details safe and do not share it with others as this may create a security breach of the system. Click <a href="http://<? echo $ini["company"]["url"] ?>"/>here</a> to visit the <? echo $ini["company"]["name"] ?></p>

<p>We look forward to working with you!</p>

<p>Kind Regards,</p>
<p>The Immigration Team.</p>