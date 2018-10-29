<script>
	$(document).ready(function() {
		var tab = new isarray_tabs();
		$(".navbar").off('click', "#downloadCheckList");
		$(".navbar").on('click', "#downloadCheckList", function(e) {
			
			var request = new isarray_request();
			if(request.isRequestReady(e)) {
				request.init({
					url:"/immigration/download-visa-documents-checklist",
					type:"read",
					action:"immigration",
					id: tab.getActiveTab().attr("data-value"),
					success: function(data) {
						if(data.status == true) {
							messages.confirm("Please click Ok to open report in new tab", function() {
								window.open(data.href, "_blank");
							});
						} else {
							messages.applicationMessage("We were unable to create the PDF, please try again", "danger");
						}							
					}
				});
			}
		});
	});
</script>


<style>
	
  .header {
    padding: 8px 0;
    margin-bottom: 0px;
  }
  
  .header h1 {
    border-top: 1px solid  #5D6975;
    border-bottom: 1px solid  #5D6975;
    color: #5D6975;
    font-size: 1.8em;
    font-weight: normal;
    text-transform: uppercase;
    text-align: center;
    margin: 2px;
    /* background: url('../../../public_html/images/checklist-pdf.png') */
  }
 
  #logo {
    text-align: right;
    margin-bottom: 4px;
  }
  
  #logo img {
    width: 250px;
  }
	
	.awaiting{
		color:grey;
	}

	.accepted{
		color:darkgreen;
	}

	.not-required{
		color:grey;
	}

	.accepted img{
		width:16px;
	}
  
  table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin: 0 0 10px 0;
  }
  
  table tr:nth-child(2n-1) td {
    background: #F5F5F5;
  }
  
  table th,
  table td {
    text-align: center;
  }
  
  table th {
    color: #5D6975;
    border-bottom: 1px solid #C1CED9;
    font-weight: normal;
  }
  
  table .name,
  table .description {
    text-align: left;
  }
  
  table td {
    padding: 5px;
		text-align: right;
  }
  
  table td.name,
  table td.expiry,
	table td.status,
	table td.description{
    font-size: 0.9em;
  }
		
	#notices {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
	}
  #notices .notice {
    color: #5D6975;
    font-size: 1em;
    margin: 16px 0 0 0;    
		text-transform:capitalize;
		
  }

  #notices .notice-title {
    text-transform:uppercase;
	}
	
  #notices .notice-title, #notices .note {
    text-decoration: underline;
    margin-bottom: 0.4em;
    font-weight: bold;
	}

	#notices .notice p.last-word {
		margin: 0px;
	}
	
</style>

<?
	$pages = array_chunk($this->visa_documentation_types, 20);
?>
	
<div class="document printable" style="font-size:16px;">

	<? foreach($pages as $i => $page)  { ?>

	<div class="page portrait">

		<div class="header">

			<div id="logo">
				<img src="/images/xpatweb-logo.png" />
			</div>

			<h1>
				<? echo $this->visa_type_name ?> APPLICATION <?= $this->user->surname . " " . $this->user->name;?>
			</h1>
			<p>
				Please see below the list of documentation we will require in order to draw up 
				<?= $this->user->surname . " " . $this->user->name;?>’s 
				<?= $this->visa_type_name ?> application in terms of current immigration rules: 
			</p>

		</div>

		<table>
			<thead>
				<tr>
					<th class="name">Name</th>
					<th class="description">Description</th>
					<th class="status">Status</th> 
				</tr>
			</thead>
			<tbody>

				<?
					foreach ($page as $visa_document){
				?>
					<tr>
						<td class="name">
							<? echo $visa_document['visaDocumentationTypeName'] ?>
						</td>
						<td class="description">
							<? echo $visa_document['visaDocumentationTypeDescription'] ?>
						</td>
						<td class="status"><?
							if($visa_document['visaDocumentationNotRequired'] === 0) {
								switch($visa_document['visaDocumentationStatus']) {
									case Null:
									case 0: echo '<span class="awaiting">Awaiting upload</span>';
											break;
									case 1: echo '<span class="accepted"><img runat="server" 	src="../../../images/icons/file-check-icon.png"></span>';
											break;
								}
							} else {
								echo '<span class="not-required">Not Required</span>';
							}
						?></td> 
					</tr>
				<?php } ?>  

			</tbody>
		</table>
			
		<? if(count($pages) === $i + 1) { ?>
		<div id="notices" >

			<div class="note">				
				<p>* Please note </p>
			</div>

			<div class="notice">
				<div class="notice-title">EXPIRY OF DOCUMENTS:</div>				
				Medical, Radiological and Police Clearance certificates are only valid for six months from their 
				date of issuance. All other documents expire after three calendar months of their issuance. 
				However, please note, that Birth, Marriage, Death and Divorce Certificates / Decrees have no 
				expiry date. 
			</div>
		
			<div class="notice">				
				<div class="notice-title">TRANSLATIONS OF FOREIGN LANGUAGE DOCUMENTS:</div>
				<p>All documents must be original. Any and all documentation not in the English language must be 
				accompanied by sworn English translations thereof. We can assist with these where required. </p>

				<p>We look forward to assisting <?= $this->user->surname . " " . $this->user->name;?> with his 
				<?= $this->visa_type_name ?> application.</p>

				<p>Please feel free to contact us should you require any additional information. </p>
			</div>

			<div class="notice">				
				<p>Kindest Regards, </p>
				<p class="last-word">Xpatweb Team </p>
			</div>

		</div>
							
		<? } ?>

	</div>

	<? } ?>

</div>
