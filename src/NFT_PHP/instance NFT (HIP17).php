<?php

	// ┌─┐┌─┐┌┬┐  ╔╗╔╔═╗╔╦╗  ┬┌┐┌┌─┐┌┬┐┌─┐┌┐┌┌─┐┌─┐
	// │ ┬├┤  │   ║║║╠╣  ║   ││││└─┐ │ ├─┤││││  ├┤ 
	// └─┘└─┘ ┴   ╝╚╝╚   ╩   ┴┘└┘└─┘ ┴ ┴ ┴┘└┘└─┘└─┘
	// get NFT instance (serial)
	
	$limit = 1; // lookup 1 NFT only
	if ($network == 'mainnet') $endpoint = "https://mainnet-public.mirrornode.hedera.com";
	else $endpoint = "https://{$network}.mirrornode.hedera.com";

	$url = "$endpoint/api/v1/tokens/$tokenId/nfts/{$serial}?limit=$limit";

	$json = getCacheData($url, 60);
	
	if ($serial == null) 
		$tokenInstance = json_decode($json, true)['nfts'];
		else $tokenInstance = array(json_decode($json, true));
		
	// check if the metadata is unique
	// later move this to function / cache as will be slow
	$url = "$endpoint/api/v1/tokens/$tokenId/nfts/?limit=1000";
	$json = getCacheData($url, 60*60*24*7); // 7 day cache as this info shouldn't change unless new minting
	$tokenInstances = json_decode($json, true)['nfts'];
	$metadata = array_column( $tokenInstances, 'metadata');
	$frequency = array_count_values($metadata);
	if (max($frequency) != 1) $tokenTypeLabel .= ' with non-unique metadata';
	if (max($frequency) == 1) $tokenTypeLabel .= ' unique metadata';
	
	
	// ┌─┐┌─┐┬─┐┌─┐┌─┐  ┌┬┐┌─┐┬┌─┌─┐┌┐┌  ┬┌┐┌┌─┐┌┬┐┌─┐┌┐┌┌─┐┌─┐
	// ├─┘├─┤├┬┘└─┐├┤    │ │ │├┴┐├┤ │││  ││││└─┐ │ ├─┤││││  ├┤ 
	// ┴  ┴ ┴┴└─└─┘└─┘   ┴ └─┘┴ ┴└─┘┘└┘  ┴┘└┘└─┘ ┴ ┴ ┴┘└┘└─┘└─┘
	// parse token instance (serials)
	
	$nft = $tokenInstance[0];
	$layer1['raw'] =  $nft['metadata'];
	if (substr($nft['metadata'],0,4) != 'http') $layer1['decode'] = base64_decode($nft['metadata']);
	else $layer1['decode'] = $nft['metadata'];
	
	
	$layer1['URL'] = $layer1['decode'];
	$layer1['CID'] = getCIDfromURL($layer1['URL']);

	//if we have a CID but no URL, then generate a URL
	if (substr($layer1['URL'],0,4) != 'http' && $layer1['CID'] != null) 
		$layer1['URL'] = getPathIFPS($layer1['CID']);
		//$layer1['URL'] = "https://cloudflare-ipfs.com/ipfs/{$layer1['CID']}";
	
	if (strpos(strtolower($layer1['decode']),'ipfs://') !== false) {
		$layer1['filename'] = basename($layer1['decode']);
		if ($layer1['filename'] == $layer1['CID']) $layer1['filename'] = null; // ignore if filename is the cid
		//$layer1['URL'] = "https://cloudflare-ipfs.com/ipfs/{$layer1['CID']}/{$layer1['filename']}";
		$layer1['URL'] = getPathIFPS($layer1['CID'], $layer1['filename']);
	}

	
	//if (substr($layer1['URL'],0,4) != 'http') 
		
	
	//$layer2['URL'] = "https://cloudflare-ipfs.com/ipfs/{$layer1['CID']}";
	$layer2['URL'] = getPathIFPS($layer1['CID']);
	
	//skip cache for now
	
	// if not yet set (eg from cache) then read from URL
	if ($layer2['raw'] == null)
		$layer2['raw'] = getCacheData($layer1['URL']);
		//$layer2['raw'] = getCacheIPFS($layer1['CID']);	 
	
	
	
	$layer2['json'] = json_decode($layer2['raw'], true);
	
	
	if ($layer2['json'] == null) {
		//$layer2['CID']  = $layer1['CID'];
		//$layer2['raw'] = getCacheData($layer1['URL']);
	} else {
		$CID = getCIDfromURL($layer2['json']['CID']);
	
		// where's CID?
		if (isset($layer2['json']['CID']))
			$layer2['CID'] =  getCIDfromURL($layer2['json']['CID']);

		if (isset($layer2['json']['image']['description'])) {
			$tmp = dissectPathIPFS($layer2['json']['image']['description']);
			$layer2['CID'] = $tmp['CID'];
			$layer2['filename'] = $tmp['filename'];
		}
		
		if (isset($layer2['json']['image']) && !is_array($layer2['json']['image'])) {
			$tmp = dissectPathIPFS($layer2['json']['image']);
			$layer2['CID'] = $tmp['CID']; //getCIDfromURL($layer2['json']['image']);
			$layer2['filename'] = $tmp['filename'];
		}
	}
	
	
	//$layer2['URL'] = "https://cloudflare-ipfs.com/ipfs/{$layer2['CID']}";
	$layer2['URL'] = getPathIFPS($layer2['CID'], $layer2['filename']);
	
	
	if (!isset($layer3['raw']))
		$layer3['raw'] = getCacheData($layer2['URL']);	
		//$layer3['raw'] = getCacheIPFS($layer2['CID']);	
	
	$in = [];
	if (strpos($layer3['raw'] , '<!DOCTYPE html>') === 0) {
		$layer3['type'] = 'html';
		$in['keyword_url'] = "{$layer2['CID']}/";
		//$in['url'] = "https://cloudflare-ipfs.com/ipfs/{$layer2['CID']}";
		$in['url'] = getPathIFPS($layer2['CID'], $layer2['filename']);

		$layer3['links'] = getLinksFromHTML($in);
		
		//unset($layer3['raw']);
		//unset($in);
	} 
		
	

	$tokenInstanceName =  $layer2['json']['name'];
	
	$tokenDescription =  $layer2['json']['description'];
	if (is_array($tokenDescription)) {
		$tokenDescription = $layer2['json']['description']['description'];
		$tokenWarning = "warning: metadata is non-compliant";
	}

	$tokenCategory = $layer2['json']['category'];
	$tokenCreator  = $layer2['json']['creator'];
	$CID = $layer2['CID'];
	$filename = $layer2['filename'];
	$tokenMetadata	= $nft['metadata'];
	//$tokenAssetURL = "https://cloudflare-ipfs.com/ipfs/$CID";
	$tokenAssetURL = getPathIFPS($CID,$filename);
	

	

	$metadataUnwrapped = json_encode($layer2['json'], JSON_PRETTY_PRINT);

	// if folder only, replace CID found inside HTML.. 
	$fileCID = getFileCID($tokenAssetURL); 
	if ($fileCID != false) {
		$CID = $fileCID;
		$tokenAssetURL = getPathIFPS($CID, $filename); //"https://cloudflare-ipfs.com/ipfs/$CID";
	}

	$tokenAsset = "<img id='dynamic_image' src='$tokenAssetURL' width='300'>";
	
	// check if the image is embedded as a json (must be a better way to do this)
if (isset($_GET['embed'])) {
	$raw_image = file_get_contents($tokenAssetURL);

	if (strpos($raw_image, '{"photo":') === 0) { // we have an inline image
		$json_image = json_decode($raw_image, true);
		$tokenAsset = "<img src='{$json_image['photo']}' width='300'>";
	}
	
	if (strpos($raw_image, 'data:image') === 0) { // we have an inline image
		//data:image/gif;base64,		
		$tokenAsset = "<img src='$raw_image' width='300'>";
	}	
}

if (isset($_GET['findfile'])) {
	$tokenAssetURL = getFilepathFromCID($layer2['CID']);
	
	$layer2['URL2'] = $tokenAssetURL;
	$layer2['filename'] = basename($tokenAssetURL);
	$layer2['comment'] = 'warning: filename not specified - retrived by inspection of the IPFS folder';
}


	// get token owner info
	$url = "https://{$_SERVER['SERVER_NAME']}/api/token/getOwnerNFT.php?network=$network&tokenId={$tokenId}-{$serial}";
	$tokenHolder = getCacheData($url, 60);
	$tokenHolderAccountId = $tokenHolder; // tidy up later as we may want to pull last tx, time etc

	//https://v2.explorer.kabuto.sh/id/0.0.55492/token?network=mainnet
	//$tokenHolder = "<a href='https://v2.explorer.kabuto.sh/id/$tokenHolder/token?network=$network' target = '$tokenHolder'>$tokenHolder</a>";
	$tokenHolder = "<a href='https://hashscan.io/#/{$network}/account/$tokenHolder' target = '$tokenHolder'>$tokenHolder</a>";
	//https://hashscan.io/#/previewnet/token/0.0.2096
	
	if ($treasuryAccountId == $tokenHolderAccountId) $tokenHolder .= " (treasury)";

