<?php
if (isset($_GET['dev'])) $isDev = true;

if ($isDev) {
	
	$html_card_tech .= "<pre><span style='white-space: pre-wrap; text-align: left;'><BR><HR>";
	$html_card_tech .= "<HR>layer1<BR>" . json_encode($layer1, JSON_PRETTY_PRINT);
	$html_card_tech .= "<HR>layer2<BR>" . json_encode($layer2, JSON_PRETTY_PRINT);
	$html_card_tech .= "<HR>layer3<BR>" . json_encode($layer3, JSON_PRETTY_PRINT);
	$html_card_tech .= "</span>";
}
	
// hey we need somewhere to put the common code for any HIP17 NFT, eg...

// 
// data is in cache already.. from $tokenInstances

//or use https://labs.gomint.me/api/token/getOwnerNFT.php?network=mainnet&tokenId=0.0.751036-1&pre

//removed https://v2.explorer.kabuto.sh/id/$tokenId?network=$network

echo <<<EOD_TEMPLATE

<tr><td>Token ID</td><td><a href='https://hashscan.io/#/$network/token/$tokenId' target='$tokenId'>$tokenId</a>-$serial  <span class='smaller_grey'> on $network</span></td></tr>
<tr><td>Type</td><td>{$tokenTypeLabel}</td></tr>
<tr><td>Collection</td><td>{$tokenClass['name']}<BR><span class='smaller_grey'>{$tokenClass['symbol']}</span></td></tr>
<tr><td>Fees</td><td>$tokenFees</td></tr>

<tr><td>Supply</td><td>Serial #{$serial} of {$max_supply}</td></tr>
<tr><td>Admin</td><td><span class='smaller_grey'>$link_treasury<BR>$keys $flags</span></td></tr>

<tr><td>Current holder</td><td>$tokenHolder</td></tr>

<tr><td>Creator</td><td>$tokenCreator</td></tr>

<tr><td>Token&nbspname</td><td>$tokenInstanceName</td></tr>
<tr><td>Description</td><td>$tokenDescription</td></tr>



<tr><td>Asset<br><br>$tweetLink</td><td>$tokenAsset<BR></td></tr>
<tr><td>Links</td><td>$tokenPluginData</td></tr>


<tr><td>IPFS CID</td><td><a href='$tokenAssetURL' target='_blank'><pre>$CID</pre></a></td></tr>
<tr><td>Meta data</td><td><pre>$tokenMetadata</pre></td></tr>
<tr><td>Decoded</td><td><pre>{$layer1['decode']}</pre></td></tr>
<!--<tr><td>Type</td><td><pre>$tokenMetadataType</pre></td></tr>-->
<tr><td>Unwrapped</td><td><pre>$metadataUnwrapped<BR><span style='color:red'>$tokenWarning</span></pre></td></tr>
<tr><td></td><td>$html_card_tech</td></tr>

</table>
<BR>


EOD_TEMPLATE;
