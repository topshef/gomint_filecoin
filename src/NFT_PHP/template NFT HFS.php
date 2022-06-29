<?php
echo <<<EOD_TEMPLATE


<tr><td>Token ID</td><td><a href='https://v2.explorer.kabuto.sh/id/$tokenId?network=$network' target='$tokenId'>$tokenId</a>-$serial  <span class='smaller_grey'>on $network</span></td></tr>
<tr><td>Type</td><td>{$tokenTypeLabel}</td></tr>

<tr><td>Collection</td><td>{$tokenClass['name']}<BR><span class='smaller_grey'>{$tokenClass['symbol']}</span></td></tr>

<tr><td>Fees</td><td>$tokenFees</td></tr>

<tr><td>Supply</td><td>Serial #{$serial} of {$max_supply}</td></tr>
<tr><td>Admin</td><td><span class='smaller_grey'>$link_treasury<BR>$keys $flags</span></td></tr>
<tr><td>Creator</td><td>$tokenCreator</td></tr>

<tr><td>Token&nbspname</td><td>$tokenInstanceName</td></tr>
<tr><td>Description</td><td>$tokenDescription</td></tr>

<tr><td>Asset<br><br>$tweetLink</td><td>$tokenAsset<BR></td></tr>


<tr><td>HFS file ID</td><td>$fileId</td></tr>
<tr><td>Meta data</td><td><pre>$tokenMetadata</pre></td></tr>
<!--<tr><td>Type</td><td><pre>$tokenMetadataType</pre></td></tr>-->
<tr><td>Unwrapped</td><td><pre>$metadataUnwrapped</pre></td></tr>

</table>
<BR>
$html_card_tech

EOD_TEMPLATE;
