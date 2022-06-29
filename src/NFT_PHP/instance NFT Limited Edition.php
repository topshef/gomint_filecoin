<?php

	// ┌─┐┌─┐┌┬┐ ╔═╗╔╦╗  ┬┌┐┌┌─┐┌┬┐┌─┐┌┐┌┌─┐┌─┐
	// │ ┬├┤  │  ╠╣  ║   ││││└─┐ │ ├─┤││││  ├┤ 
	// └─┘└─┘ ┴  ╚   ╩   ┴┘└┘└─┘ ┴ ┴ ┴┘└┘└─┘└─┘
	// get FT instance (edition)

/*	
	$limit = 1; // lookup 1 NFT only
	if ($network == 'mainnet') $url = "https://mainnet-public.mirrornode.hedera.com";
	else $url = "https://testnet.mirrornode.hedera.com";

	$url .= "/api/v1/tokens/$tokenId/nfts/{$serial}?limit=$limit";

	$json = getCacheData($url, 60);
	
	if ($serial == null) 
		$tokenInstance = json_decode($json, true)['nfts'];
		else $tokenInstance = array(json_decode($json, true));
	*/


	$string = $tokenClass['symbol'];
	
	$pattern = '/(?:(?:\d+)\.){2}\d+/';
	preg_match($pattern, $string, $matches);
	$fileId = $matches[0];
//exit("X{$fileId}X");
	

	$url = "https://{$_SERVER['SERVER_NAME']}/api/HFS/?network=$network&fileId=$fileId";
	
	$file_content = getCacheData($url); // this is probably double-cached!
	//exit($file_content );
	$metadata = json_decode($file_content, true);
	
	
	//https://dev.gomint.me/api/HFS/?network=mainnet&fileId=0.0.639887	
	
	
	// get owner info
	$url = "https://gomint.me/api/edition/?tokenId=$tokenId&network=$network";
	if (isset($_GET['nocache'])) $json = getCacheData($url, 0); 
	else $json = getCacheData($url); 
	$owners = json_decode($json, true)['editions'];
	$tokenHolderAccountId = $owners[$serial]['accountId'];
	$lastTransferTxid =  $owners[$serial]['txid'];
	$lastTransferTime =  $owners[$serial]['transferTime'];
	$lastTransferTime = str_replace('T',' ', substr($lastTransferTime,0,16));
	
	//$tokenHolder = "<a href='https://app.dragonglass.me/hedera/search?q=$lastTransferTxid' target = 'lasttx'>$tokenHolderAccountId</a>";
	$tokenHolder = "<a href='https://hashscan.io/#/{$network}/account/$tokenHolderAccountId' target = '$tokenHolderAccountId'>$tokenHolderAccountId</a>";	$tokenHolder .= " since $lastTransferTime UTC";
	



	//https://app.dragonglass.me/hedera/search?q=00554921639213696500440285
	
	//https://gomint.me/api/edition/?tokenId=0.0.673534&pre
/*
    "token_instance": [
        {
            "account_id": "0.0.657983",
            "created_timestamp": "1647715751.322297701",
            "deleted": false,
            "metadata": "YmFma3JlaWFnY2plbWo2cmU3cWg0aGk2NG51bGd4NTRsdWdvYWx1dG5hNmtuaWE3NXhucWtwMmJvamU=",
            "modified_timestamp": "1647734539.124848467",
            "serial_number": 1,
            "token_id": "0.0.789484"
        }
*/		
	function consensusTimeToEpochNano($consensusTime) {
		//eg 2020-08-16T00:56:02.232+0000
		//eg 2022-03-20T18:10:33.588518908Z
		//return $consensusTime;
		return strtotime(substr($consensusTime, 0, 19)) . '.' . substr($consensusTime, 20, 9) ;
	}
	

	
	$tokenInstance = array(
		'account_id' => $tokenHolderAccountId, 
		'deleted' => false, 
		//'modified_timestamp' => consensusTimeToEpochNano($owners[$serial]['transferTime']),
		'consensus_time_last_transfer' => $owners[$serial]['transferTime'],
		'serial_number' => $serial,
		'token_id' => $tokenId,
		'instance_type' => 'GoMint Limited Edition'
		);
	
	// ┌─┐┌─┐┬─┐┌─┐┌─┐  ┌┬┐┌─┐┬┌─┌─┐┌┐┌  ┬┌┐┌┌─┐┌┬┐┌─┐┌┐┌┌─┐┌─┐
	// ├─┘├─┤├┬┘└─┐├┤    │ │ │├┴┐├┤ │││  ││││└─┐ │ ├─┤││││  ├┤ 
	// ┴  ┴ ┴┴└─└─┘└─┘   ┴ └─┘┴ ┴└─┘┘└┘  ┴┘└┘└─┘ ┴ ┴ ┴┘└┘└─┘└─┘
	// parse token instance (serials)
	
	//output vars for template
	$tokenAssetURL = $metadata['data'][0]['tokenAssetURL'];
	$tokenAssetHash = $metadata['data'][0]['tokenAssetHash'];
//exit($tokenAssetURL);
	$tokenAsset = "<img src='$tokenAssetURL' width='300'>";  // check - this gets overwritten later
	
	$tokenDescription = $metadata['data'][0]['tokenDescription'];
	$tokenCreator = $metadata['data'][0]['tokenAssetCreator'];
	$tokenCreator = str_replace('KPAYID ', '', $tokenCreator);
	
	$tokenMetadata	= $file_content;
	
	$tokenCategory = null;
	
	$metadataUnwrapped = json_encode($metadata, JSON_PRETTY_PRINT);
	



	
	

