<style>
    p {
        font-family:"Calibri (Body)";
        font-size:14px;
    }
    table, td, th {
        border: 1px solid black;
    }
    table tr {
        padding:2px;
    }
    table {
        width: 60%;
        background-color:#f2f2f2;
        table-layout:fixed;
    }
    th {
        text-align: left;
        width: 25%;
    }
</style>

<p>Dear <?php echo ucfirst($user['name']); ?>,</p>
    
<p>Please note this is your reminder for the following : </p>
      
<p>
    <?php 
        foreach($reminders as $key => $reminder){ 
        echo '<div><table>';
        echo '<tr><th>Expatriate:</th><td>'; echo $reminder['name']; echo'&nbsp;&nbsp;';  echo $reminder['userSurname']; echo'</td></td></tr>';
        echo '<tr><th>Visa:</th><td>';  echo $reminder['visaTypeName']; echo'</td></td></tr>';
        echo '<tr><th>Document:</th><td>'; echo $reminder['visaDocumentationTypeName']; ; echo'</td></td></tr>';
        echo '<tr><th>Reminder:</th><td>' ;        
            if($category == model_documentReminder::FIRST_DATE) {
                echo "First Reminder";
            } else if($category == model_documentReminder::SECOND_DATE){
                echo "Final Reminder";
            } 
            echo'</td></td></tr>';
        echo '</table></div><br/>';
    }?>
</p>
    
<p>Regards</p>
<p>Xpatweb Team</p>