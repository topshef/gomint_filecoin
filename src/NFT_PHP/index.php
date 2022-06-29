<?php

	ini_set('memory_limit', '512M');

	include '../../generic/pagevisit.php';
	
	
// Lookup any NFT on Hedera

	require_once('functions.php');

	$network = $_GET['network'];
	if ($network == null) $network = 'mainnet';

	$tokenId = $_GET['tokenId'];

	$productId = $_GET['productId'];  // experimental idea
	if ($productId != null) {
		$v = explode(';', $productId);
		$network = $v[0];
		$tokenId = $v[1];
		$seller = $v[2]; // not used here
	}
	if ($productId == null) $productId = "$network;$tokenId;$seller";
	
	$isInstance = strpos($tokenId, '-') !== false;
	if ($isInstance) { // token ID includes serial number
		$parts = explode('-', $tokenId);
		$tokenId = $parts[0];
		$serial = $parts[1];
	}
	
	if ($serial == null) $serial = 1; // for now show first serial if none specified

	if ($tokenId == null) {
		include('form.php');
		exit; //{include('form.php'); exit; } //exit('missing tokenId');
	}

	// ┌─┐┌─┐┌┬┐  ╔╗╔╔═╗╔╦╗  ┌─┐┬  ┌─┐┌─┐┌─┐
	// │ ┬├┤  │   ║║║╠╣  ║   │  │  ├─┤└─┐└─┐
	// └─┘└─┘ ┴   ╝╚╝╚   ╩   └─┘┴─┘┴ ┴└─┘└─┘
	// get NFT class aka collection, token ID
	
	if ($network == 'mainnet') $url = "https://mainnet-public.mirrornode.hedera.com";
	else $url = "https://{$network}.mirrornode.hedera.com";

	$url .= "/api/v1/tokens/$tokenId";
	
	$json = getCacheData($url, 60);
	$tokenClass = json_decode($json, true);
	
	
	// ┌─┐┌─┐┬─┐┌─┐┌─┐  ╔╗╔╔═╗╔╦╗  ┌─┐┬  ┌─┐┌─┐┌─┐
	// ├─┘├─┤├┬┘└─┐├┤   ║║║╠╣  ║   │  │  ├─┤└─┐└─┐
	// ┴  ┴ ┴┴└─└─┘└─┘  ╝╚╝╚   ╩   └─┘┴─┘┴ ┴└─┘└─┘
	// parse NFT class aka collecton
	
	// typeType -> label for webpage
	// this is a work-in-progress
	$tokenTypes = array(
		'NFT (HIP17)' => 'NFT (HIP17)',
		'NFT Limited Edition' => 'Layer 1 fungible<BR>Layer 2 NFT Limited Edition',
		'NFT HFS' => 'NFT (HIP17) with non-unique metadata on HFS',
	);
	
	$tokenType = ($tokenClass['type'] == 'NON_FUNGIBLE_UNIQUE') ? 'NFT (HIP17)' : 'fungible';
	
	if ($tokenType == 'fungible')
		if (strpos($tokenClass['symbol'], '?roothash=') > 0) $tokenType = 'NFT Limited Edition';
	
	if ($tokenType == 'NFT (HIP17)' && strpos(strtoupper($tokenClass['symbol']), 'HEDERA://') !== false) $tokenType = 'NFT HFS';
	
	$tokenTypeLabel = $tokenTypes[$tokenType]; // assign holding variable allows update for edge cases, eg Xact or finding non-unique metadata
	
	$tokenClass['view']['type'] = $tokenType;
	if ($tokenType == 'NFT Limited Edition') $tokenClass['view']['type'] = 'nFT'; // fudge
	
	//exit($tokenType);
	
	// what keys are attached to this token?
	$keyFieldsCamelCase = 'adminKey kycKey freezeKey pauseKey wipeKey supplyKey feeScheduleKey'; // used in HAPI 
	$keyFields_underscore =  strtolower(preg_replace('/(?<=[a-z])([A-Z]+)/', '_$1', $keyFieldsCamelCase));
	$keyFields = explode(' ', $keyFields_underscore);

	//https://docs.hedera.com/guides/docs/sdks/tokens/wipe-a-token
	//https://docs.hedera.com/guides/docs/sdks/tokens/pause-a-token
	$icon_folder = '../../img/key_icons';

	$isImmutable = true;
	foreach($keyFields as $keyField) if ($tokenClass[$keyField] != null) {
		if (strpos(' admin_key wipe_key', $keyField) !== false)  $isImmutable = false;
		if ($tokenClass['type'] == 'FUNGIBLE_COMMON' &&  $keyField == 'supply_key') $isImmutable = false;
		
		$keyLabel = str_replace('_', ' ', $keyField);
		$keys .= "<img src='$icon_folder/$keyField.png' style='position: relative; top: 6px;' height=20>";
		$keys .= " $keyLabel <BR>";
		$tokenClass['keys'][] = $keyField; // for api output.. legacy location
		//maybe later $tokenClass['view']['keys'][] = $keyField; // consolidate under view
	}
	
	if ($isImmutable) {
		$keys .= "<img src='$icon_folder/immutable.png' style='position: relative; top: 6px;' height=20>";
		$keys .= ' immutable<BR>';
	}
	
	if ($keys == null) $keys .= ' (no keys)<BR>';
	
	// hmm unfortunately there's a bug in the Hedera mirror node total_supply missing
	// the correct value can be obtained from Hedera direct, but was hoping to avoid that
	// let's not show this, it's confusing anyway too many stats
	//total {$tokenClass['total_supply']} minted, of 

	$max_supply = ($tokenClass['max_supply'] == 0) ? 'unlimited' : "max {$tokenClass['max_supply']}"; 
	
	if ($tokenClass['initial_supply'] != 0) // ie fungible
		if ($tokenClass['supply_key'] == null) $max_supply = $tokenClass['initial_supply'];
		else $max_supply = 'unlimited';
	
	$divisor = ($tokenClass['decimals'] == 0) ? 1 : pow(10,$tokenClass['decimals']);
	$tokenClass['view']['supply']['decimals'] = $tokenClass['decimals']; //or ? ($tokenType == 'fungible') ? $tokenClass['decimals'] : 'NA';
	$tokenClass['view']['supply']['initial'] = $tokenClass['initial_supply'] / $divisor;
	$tokenClass['view']['supply']['total'] = $tokenClass['total_supply'] / $divisor;
	$tokenClass['view']['supply']['max'] = $tokenClass['max_supply'] / $divisor;	
	
	$treasuryAccountId = $tokenClass['treasury_account_id'];
	
	//$link_treasury = "<a href='https://v2.explorer.kabuto.sh/id/$treasuryAccountId?network=$network' target=$treasuryAccountId'><img src='$icon_folder/treasury.png' style='position: relative; top: 6px;' height=20> treasury $treasuryAccountId</a>";
	$link_treasury = "<a href='https://hashscan.io/#/{$network}/account/$treasuryAccountId' target=$treasuryAccountId'><img src='$icon_folder/treasury.png' style='position: relative; top: 6px;' height=20> treasury $treasuryAccountId</a>";
	
	//exit("<PRE>" . print_r($tokenClass,true));
	
$royalty_fees = $tokenClass['custom_fees']['royalty_fees'];
if ($royalty_fees != null)
	foreach ($tokenClass['custom_fees']['royalty_fees'] as $fee) {
		$collector_account_id = $fee['collector_account_id'];
		$royalty = round(100 * $fee['amount']['numerator']/$fee['amount']['denominator'],1);
		$fallback = $fee['fallback_fee']['amount'];
		if ($fee['fallback_fee']['denominating_token_id'] == null)
			$fallback = $fallback/ 100000000  . ' hbar';
		else $fallback .= ' x token ' . $fee['fallback_fee']['denominating_token_id'];
		$tokenFees .= "{$royalty}% to $collector_account_id, fallback $fallback<BR>";
	}
	
	// placeholder for fixed fees.. parse when we have some examples
	if (!empty($tokenClass['custom_fees']['fixed_fees']))
		$tokenFees .= json_encode($tokenClass['custom_fees']['fixed_fees'], JSON_PRETTY_PRINT);

	
	// ┌─┐┌─┐┌┬┐  ┬┌┐┌┌─┐┌┬┐┌─┐┌┐┌┌─┐┌─┐  ┌┬┐┌─┐┌┬┐┌─┐
	// │ ┬├┤  │   ││││└─┐ │ ├─┤││││  ├┤    ││├─┤ │ ├─┤
	// └─┘└─┘ ┴   ┴┘└┘└─┘ ┴ ┴ ┴┘└┘└─┘└─┘  ─┴┘┴ ┴ ┴ ┴ ┴
	// get instance data, depending on type
	include("instance {$tokenType}.php");
	


	// ┌┬┐┬ ┬┬┌┬┐┌┬┐┌─┐┬─┐  ┌─┐┬ ┬┌─┐┬─┐┌─┐
	//  │ ││││ │  │ ├┤ ├┬┘  └─┐├─┤├─┤├┬┘├┤ 
	//  ┴ └┴┘┴ ┴  ┴ └─┘┴└─  └─┘┴ ┴┴ ┴┴└─└─┘
	// twitter share link
	$tokenName = $tokenInstanceName;
	if ($tokenName == null) $tokenName = $tokenClass['name'];
	$cleanTokenName = str_replace('$', '\$', $tokenName);
	$cleanTokenDescription = str_replace("\n", ' ', $tokenDescription);
	$cleanTokenDescription = str_replace("\r", '', $cleanTokenDescription);
	
	//$cleanTokenDescription = preg_replace("/[^A-Za-z0-9.!?',~]/",' ',$tokenDescription);
	
	$tweetText = urlencode("Check out '{$cleanTokenName}' minted on #Hedera #cleanNFT explore it @gomintme 💚");

	$tweetUrl = urlencode("https://{$_SERVER['SERVER_NAME']}/explore/token/?$tokenId-$serial&network=$network");
	$tweetHashtags = "cleanNFT,gomint,HTS";
	$tweetLink = "<a href='https://twitter.com/share?text={$tweetText}&url={$tweetUrl}&hashtags={$tweetHashtags}' target='$tokenId'><img src='../../img/tweet.png' width='25'></a>";
		

// ┌─┐┌─┐┌─┐┬ ┬┌─┐  ┬┌┬┐┌─┐┌─┐┌─┐
// │  ├─┤│  ├─┤├┤   ││││├─┤│ ┬├┤ 
// └─┘┴ ┴└─┘┴ ┴└─┘  ┴┴ ┴┴ ┴└─┘└─┘
// cache image

	// bit lost here, this is a kinda a mess, along with the rest of the caching
	// maybe move to sofia server and sort it there, then redirect
	//eg explore.gomint.me
	if (strpos($tokenAssetURL,'ipfs') !== false) {
		//$tokenAssetCacheURL = getCacheURL_IPFS($CID);
		//$tokenAssetTwitterImageURL = $tokenAssetCacheURL;
		$cacheURL = getCacheURL_IPFS($CID, false);
		// user input "cache" will trigger caching to occur, bit arsy let's see
		//mainly for admin to test. delete cache to revert to original url
		if (!$cacheURL && isset($_GET['cache'])) $cacheURL = getCacheURL_IPFS($CID); // force download
		if ($cacheURL != null) $tokenAssetURL = $cacheURL;
	}

	$tokenAssetTwitterImageURL = $tokenAssetURL;
	
	//swap for thumbnail if on gomint
	if ($tokenAssetHash == null) $tokenAssetHash = $hash;
	$isGoMintFilename = (strpos($tokenAssetTwitterImageURL, $tokenAssetHash) > 0);
	$isLocalFile = (strpos($_SERVER['SERVER_NAME'], 'gomint.me') !== false);
	$isThumbnail = (strpos($tokenAssetTwitterImageURL, 'thumbnail') !== false);
	
	if ($isGoMintFilename && $isLocalFile && !$isThumbnail) {
	
		$localPath = str_replace("https://{$_SERVER['SERVER_NAME']}", $_SERVER['DOCUMENT_ROOT'], $tokenAssetTwitterImageURL);
		$pathinfo = pathinfo($localPath);				
		$pattern = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.thumbnail.*';
		
		$files = glob($pattern);
		if ($files != null) {
			$tokenAssetTwitterImageURL = $files[0];
			$tokenAssetTwitterImageURL = str_replace($_SERVER['DOCUMENT_ROOT'], "https://{$_SERVER['SERVER_NAME']}", $tokenAssetTwitterImageURL);
		}
	}


//https://labs.gomint.me/explore/NFT/?network=mainnet&tokenId=0.0.690795-25&url_image
	// ┌─┐┬─┐┌─┐┌─┐┌─┐┌┬┐┌─┐┬ ┬  ┌┐┌┌─┐─┐ ┬┌┬┐  ┬ ┬┬─┐┬  
	// ├─┘├┬┘├┤ ├┤ ├┤  │ │  ├─┤  │││├┤ ┌┴┬┘ │   │ │├┬┘│  
	// ┴  ┴└─└─┘└  └─┘ ┴ └─┘┴ ┴  ┘└┘└─┘┴ └─ ┴   └─┘┴└─┴─┘
	// prefetch next url
	
	if ($serial == null) $serial = 1;
	$tmp_max_supply = $tokenClass['initial_supply'];
	if ($tmp_max_supply == 0) $tmp_max_supply = $tokenClass['max_supply'];
	//$tokenClass['token_max_supply'] = $tmp_max_supply;
	$tokenClass['max_supply'] = $tmp_max_supply;  // over-writes 0 for fungible having no supply key (ie fixed supply)
	
	$serial_prev = $serial - 1; if ($serial_prev == 0) $serial_prev = $tmp_max_supply;
		
	$serial_next = $serial + 1; if ($serial_next > $tmp_max_supply) $serial_next = 1;
	$url_prev = "https://{$_SERVER['SERVER_NAME']}/explore/NFT/?network=$network&tokenId=$tokenId-$serial_prev";
	$url_next = "https://{$_SERVER['SERVER_NAME']}/explore/NFT/?network=$network&tokenId=$tokenId-$serial_next";
	

	// ┌─┐┬─┐┌─┐┌─┐┌─┐┌─┐┌─┐  ┌─┐┬  ┬ ┬┌─┐┬┌┐┌┌─┐
	// ├─┘├┬┘│ ││  ├┤ └─┐└─┐  ├─┘│  │ ││ ┬││││└─┐
	// ┴  ┴└─└─┘└─┘└─┘└─┘└─┘  ┴  ┴─┘└─┘└─┘┴┘└┘└─┘
	// process plugins
	$files_plugin = glob('./plugin/*.active.php');	
	foreach ($files_plugin as $filepath_plugin) {
		$result = null;
		include $filepath_plugin;
		//if (is_array($result)) 
		if ($result != null) $tokenPluginData .= "$result<BR>";
	}

		
	// ┌┬┐┬┌─┐┌─┐┬  ┌─┐┬ ┬  ┌┬┐┌─┐┌┬┐┌─┐
	//  │││└─┐├─┘│  ├─┤└┬┘   ││├─┤ │ ├─┤
	// ─┴┘┴└─┘┴  ┴─┘┴ ┴ ┴   ─┴┘┴ ┴ ┴ ┴ ┴	
	// display data

	if (isset($_GET['url_image'])) {
		exit($tokenAssetTwitterImageURL);
	}


	// ┌─┐┬┬  ┌─┐  ┌┬┐┬ ┬┌─┐┌─┐  ┌─┐┬─┐┌─┐┌─┐┌─┐┌─┐┌─┐┬┌┐┌┌─┐
	// ├┤ ││  ├┤    │ └┬┘├─┘├┤   ├─┘├┬┘│ ││  ├┤ └─┐└─┐│││││ ┬
	// └  ┴┴─┘└─┘   ┴  ┴ ┴  └─┘  ┴  ┴└─└─┘└─┘└─┘└─┘└─┘┴┘└┘└─┘
	// file type processing


	$tokenAsset = "<img id='dynamic_image' src='$tokenAssetTwitterImageURL' width='300'>";  //default

	$ext = strtolower(pathinfo($tokenAssetURL)['extension']);
	if ($layer2['json']['type'] == 'application/octet-stream') $ext = 'glb'; // best guess atm
	if ($layer2['json']['type'] == 'model/gltf-binary') $ext = 'glb'; 

	if ($ext == 'mp4') $tokenAsset = <<<EODMP4
<video width='60%' controls>  <source src='$tokenAssetURL' type='video/mp4'></video>
EODMP4;
	
	if ($ext == 'glb') $tokenAsset = <<<EODGLB
	<!-- Import the component -->
<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

<model-viewer src='$tokenAssetURL' alt='glb' ar ar-modes='webxr scene-viewer quick-look' environment-image='neutral'  auto-rotate camera-controls></model-viewer>
EODGLB;		



	// ┌─┐┌─┐┬   ┬┌─┐┌─┐┌┐┌
	// ├─┤├─┘│   │└─┐│ ││││
	// ┴ ┴┴  ┴  └┘└─┘└─┘┘└┘
	// api json

	if (isset($_GET['json'])) {
		//if (isset($_GET['cors'])) header("Access-Control-Allow-Origin: *");
		if (!isset($_GET['nocors'])) header("Access-Control-Allow-Origin: *");
		
		if (isset($_GET['pre'])) echo "<PRE>";
		// rename vars for export
		$token_id = $tokenId; 
		$token_instance_name = $tokenInstanceName; // bit of work to tidy
		$token_instance_description = $tokenDescription;
		$token_asset_URL = $tokenAssetURL;
		$token_asset_image_URL = $tokenAssetTwitterImageURL;
		$token_holder_account_id = $tokenHolderAccountId;
		$token_class = $tokenClass;
		$token_instance = $tokenInstance;
		$token_instances = $tokenInstances;
		$token_asset_html = $tokenAsset;
		$token_keys_html = $keys;
		
		
		
		// omitted: layer3 (may contain raw image); metadata is within tokenInstances; json_image hmmm
		$out_fields = 'network token_id serial token_instance_name token_instance_description token_asset_URL token_asset_image_URL token_holder_account_id token_class token_asset_URL market_info plugin_data';
		if ($isInstance) $out_fields .= ' token_instance layer1 layer2';
		else $out_fields .= ' token_instances';
		
		if (isset($_GET['html'])) $out_fields .= ' token_asset_html token_keys_html';
		$out_fields = explode(' ', $out_fields);
		//exit(print_r($out_fields, true));
		$out = compact($out_fields);
		
		//not yet.. check dependencies... if (!$isInstance) unset($out['serial'], $out['token_instance_name']);
		
		if (isset($_GET['dev'])) exit(print_r($out, true));
		$json = json_encode($out, JSON_PRETTY_PRINT);
		echo $json;
		//echo json_last_error();
		exit;
	}


//$html_card_tech = "<PRE>" . print_r($layer3['links'],true)	;
	include('form.php');
	
	
	$metadataUnwrapped = str_replace('\/','/', $metadataUnwrapped);
	
	include "template {$tokenType}.php";
	include('footer.php');
	
exit;	
	echo "<pre>";
	print_r($tokenClass);
	print_r($tokenInstance);

//exit;
	$layers = compact('layer1', 'layer2', 'layer3');
	print_r($layers);




function getGomintThumbnail() { // placeholder not in use

		$thumbnail = glob("{$dir_imagedata}{$hash}.thumbnail.{png,jpeg,jpg,gif}", GLOB_BRACE);
		if ($thumbnail != null) $thumbnail = basename($thumbnail[0]);
		
		if ($thumbnail == null) $thumbnail = glob("{$dir_imagedata}{$hash}.{png,jpeg,jpg,gif}", GLOB_BRACE);
		if ($thumbnail != null) $thumbnail = basename($thumbnail[0]);
}
		
?>
