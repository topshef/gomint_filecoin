<?php
$tokenSymbol = $tokenClass['symbol'];
$tokenSymbol = str_replace('roothash=' , 'roothash= ', $tokenSymbol); 

if ($tokenType = 'NFT Limited Edition') 
	$tokenCreator = "<a href='https://gomint.me/gallery/?creator=$tokenCreator' target='$tokenCreator'>$tokenCreator</a>";

$tokenTypeLabel = str_replace('Layer 2 NFT Limited Edition', "<a href='https://gomint.me/edition?network=$network&tokenId=$tokenId' target='editions_$tokenId'>Layer 2 NFT Limited Edition</a>", $tokenTypeLabel);

echo <<<EOD_TEMPLATE


<tr><td>Token ID</td><td><a href='https://hashscan.io/#/$network/token/$tokenId' target='$tokenId'>$tokenId</a>-$serial  <span class='smaller_grey'> on $network</span></td></tr>
<tr><td>Type</td><td>{$tokenTypeLabel}</td></tr>

<tr><td>Token&nbspname</td><td>{$tokenClass['name']}</td></tr>
<tr><td>Token&nbspsymbol</td><td><span class='smaller_grey'>$tokenSymbol</span></td></tr>

<tr><td>Description</td><td>$tokenDescription</td></tr>

<tr><td>Supply</td><td>Serial #{$serial} of {$max_supply}</td></tr>
<tr><td>Admin</td><td><span class='smaller_grey'>$link_treasury<BR>$keys $flags</span></td></tr>

<tr><td>Current holder</td><td>$tokenHolder</td></tr>
<!--<tr><td>Fees</td><td>$tokenFees</td></tr> -->

<!--<tr><td>Category</td><td>$tokenCategory</td></tr>-->
<tr><td>Creator</td><td>$tokenCreator</td></tr>

<tr><td>Asset<br><br>$tweetLink</td><td>$tokenAsset<BR></td></tr>
<tr><td>Links</td><td>$tokenPluginData</td></tr>


<!--<tr><td>IPFS CID</td><td><a href='$tokenAssetURL' target='_blank'><pre>$CID</pre></a></td></tr>-->
<tr><td>Meta data</td><td><pre>$tokenMetadata</pre></td></tr>
<!--<tr><td>Type</td><td><pre>$tokenMetadataType</pre></td></tr>-->
<!--<tr><td>Unwrapped</td><td><pre>$metadataUnwrapped</pre></td></tr>-->

</table>
<BR>
$html_card_tech

EOD_TEMPLATE;
