<? 
    $rows = $this->dashboard->getExpatriateTableBreakdown();
    if (count($rows) > 0 ) { ?>
<table rules="all" class="database table table-striped" style="margin-bottom: 0">
    <thead>
        <tr>
            <th>Country</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <? 
            foreach($rows as $v) {
        ?>
        <tr>
            <td><?
                echo $v["countryName"];
            ?></td>
            <td class='tab-title'><?
                echo $v["totalExpats"];
            ?></td>
           
        </tr> 
        <?  } ?>
    </tbody>
</table>
<? } else { ?> 
<div class="well well-sm" style="margin:20px;">No expatriates found</div>
<? } ?> 