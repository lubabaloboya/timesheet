<p>Dear <? echo ucwords($user->name) ?></p>
<p>Welcome to Professional Body for NDT. Please find below your account details</p>

<p>USERNAME: <? echo $username ?></p>
<p>PASSWORD: <? echo $password ?></p>
<p>Be sure to keep your account details safe and do not share it with others as this may create a security breach of the system. Click the <a href="http://professional-body-ndt.org.za"/>here</a> to visit the Professional Body for Non-Destructive Testing (NDT)</p>

<p>Please click the link below to confirm your email address, if you did not request this registration please ignore this email. This email confirmation will only be valid for 7 days at which point this registration request will be deleted.</p>
<div style="background:green; width:90px; text-align:center; padding:10px;"><a style="color:white" href="http://professional-body-ndt.org.za/index/account-activation?user=<? echo $user->ID ?>&token=<? echo $user->code ?>">Activate</a></div>
<p>Regards,</p>
<p>The PBNDT Team</p>
