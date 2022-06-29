<?php
include "header.php";
// if ($serial > 1) $serial_prev = $serial - 1; 
// $serial_next = $serial + 1;

if ($tokenId != null)
	$jumpto = " <BR><span style='position: relative; bottom: -6px;'><a href='./?tokenId={$tokenId}-{$serial_prev}&network=$network'> <input type='button' value='Previous'> </a>  <a href='./?tokenId={$tokenId}-{$serial_next}&network=$network'> <input type='button' value='Next'> </a></span>";


	//$jumpto = " <BR><a href='./?tokenId={$tokenId}-{$serial_prev}'> < prev </a>  <a href='./?tokenId={$tokenId}-{$serial_next}'>  next ></a>";


	//$jumpto = " <a href='./?tokenId={$tokenId}-{$serial_prev}'>{$serial_prev} </a> < $serial > <a href='./?tokenId={$tokenId}-{$serial_next}'> {$serial_next}";


if ($tokenId != null) {
	if (substr($tokenId,1,1) != '.') $tokenId = "0.0.$tokenId"; // allow lazy keying
	$defaultTokenId = "{$tokenId}-{$serial}";
}


?>
<?php if (isset($_GET['commentsubmitted'])) echo '<BR><B>Thanks for your feedback 💚</B><BR>';?>

<table>
<tr><td colspan="2"></td></tr>
<tr><td>Token ID</td><td>
<form action="./" method="get">
 <input type="text" name="tokenId" size="14" value="<?php echo $defaultTokenId; ?>">

<select name="network" class="inputbox" style="width: 100px;">
	<option <?php if ($network == 'mainnet') echo 'selected="selected"'; ?> value="mainnet">mainnet</option>
	<option <?php if ($network == 'testnet') echo 'selected="selected"'; ?>value="testnet">testnet</option>
	<option <?php if ($network == 'previewnet') echo 'selected="selected"'; ?>value="previewnet">previewnet</option>	
</select>

    <input type="submit" value="Search"> <?php echo $jumpto; ?> 
	</form> 
</td></tr>

<?php
//https://stackoverflow.com/questions/3518002/how-can-i-set-the-default-value-for-an-html-select-element



