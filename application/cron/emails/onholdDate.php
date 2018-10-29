<style>
    p {
        font-family:"Calibri (Body)";
        font-size:14px;
    }
    .date{
        color: orange;
    }
    table, td, th {
        border: 1px solid black;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th {
        text-align: left;
    }
</style>
<div>
    
    <p>Dear <?php echo ucfirst($user->name); ?></p>

     <p>This serves as a reminder you that the following cases are on hold. </p>
     
    <p>Names are as follows:</p>
    <?php
           echo " <table>";
               echo " <tr>";
                      echo " <th>Name</th>";
                      echo "<th>Surname</th>";
                      echo "<th>Date</th>";
              echo "</tr>";
                      foreach ($client["expatriates"]  as $exp){
                            echo "<tr>";
                                echo "<td>".$exp['name']."</td>";
                                echo " <td>".$exp['userSurname']."</td>";
                                echo " <td>".$exp['visaDateOnhold']."</td>";
                            echo " </tr>";
                      }
           echo "</table>";

    ?> 
    <p>Please contact your Immigration Team with any queries.</p>
    
    <p>Regards</p>
    <p>Immigration Team</p>
</div>
