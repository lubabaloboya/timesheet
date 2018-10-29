<p>Dear <?php echo ucfirst($client["companyName"]); ?>,</p>
    
<p>This serves as a reminder that the visas for the following expatriates are due to expire in 90 days:</p>

    <?php  
    foreach ($client["expats"] as $expat) {

        switch ($expat["days"]){
            case ($expat["days"] <= 90 && $expat["days"] > 60):
                $status = "<strong style='color: green'>Positive</strong> - application is still within the prescribed submission timeframe (60 to 90 days)";
            break;
            case ($expat["days"] <= 60 && $expat["days"] > 30):
                $status = "<strong style='color: orange'>Priority</strong> - application is nearing the end of the prescribed time frame (30 to 60 days)";
            break;
            case ($expat["days"] <= 30 && $expat["days"] > 0):
                $status = "<strong style='color: red'>Negative</strong> - application has missed the deadline and the visa is expired/due to expire soon.(0 to 30 days)";
            break;
    }?>
            
        <p>Name : <?php echo $expat["name"] ?> <br>
        Surname : <?php echo $expat["surname"] ?> <br>
        Visa Type : <?php echo $expat["VisaType"] ?> <br>
        Visa expiry date: <?php echo $expat["ExpiryDate"] ?> <br>
        Visa Status: <?php echo $status ?> <br>
        </p>
    <?php } ?>

<p>Kindly contact your Xpatweb immigration consultant with any queries on 011 467 0810</p>
    
<p>Regards</p>
<p>Immigration Team</p>
