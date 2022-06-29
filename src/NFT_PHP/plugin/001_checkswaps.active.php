<?php
//return;
// look for swap option and present link to user

	if ($network == null) return;
	if ($tokenId == null) return;


	$pattern = __DIR__ . "/../../../swap/data/$network*$tokenId*approved.json";
	//exit($pattern);
	
	$files = glob($pattern);

	if ($files != null) {
	//foreach ($files as $filepath) {
		//0.0.789474;0.0.789484
		$filepath = $files[0];
		$filename = basename($filepath);
		$v = explode(';', $filename);
		//etc split out info
		$swap_nFT = $v[2];
		$swap_NFT = $v[3];
		
		$link_swap_nFT = "<a href='./?network=$network&tokenId=$swap_nFT'>$swap_nFT</a>";
		$link_swap_NFT = "<a href='./?network=$network&tokenId=$swap_NFT'>$swap_NFT</a>";
		$link_swap = "<a href='https://gomint.me/swap?network=$network&tokenId=$swap_NFT' target='swap'>GoMint swap</a>";
		
		if ($tokenId == $swap_nFT) 
			$result = "This token is eligible for $link_swap to $link_swap_NFT";
		else 
			$result = "This token is eligible for $link_swap from $link_swap_nFT";
		
		$plugin_data['swaps'][] = compact('swap_nFT', 'swap_NFT'); 
	//}
	

	
	}


?>

