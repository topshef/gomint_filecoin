<?php
//return;
// look for buy option and present link to user

	if ($network == null) return;
	if ($tokenId == null) return;

	$pattern = __DIR__ . "/../../../data/market/order/$network.$tokenId*ask-add.*";
	//exit($pattern);
	
	$files = glob($pattern);
	// print_r($files);
	// exit;

	//if ($files != null) {
	foreach ($files as $filepath) {

		//$filepath = $files[0];
		$filename = basename($filepath);
		$v = explode('.', $filename, 8);  //limit 9 in case decimal in price
		//etc split out info
		$thisSeller = $v[4];
		
		if (isset($_GET['seller']) && $thisSeller != $_GET['seller']) continue; // skip if seller is specified and not matching
		if ($seller != null && $thisSeller != $seller) continue; // skip if seller is specified from productId
		
		$qty_price_unit = explode('x', $v[7]);
		$available = $qty_price_unit[0];
		$price = $qty_price_unit[1];
		$unit = $qty_price_unit[2];

		$domain = $_SERVER['SERVER_NAME'];
		$result = "<a href='https://$domain/gallery?creator=$seller&network=$network&tokenId=$tokenId'>Buy</a> this token on GoMint 💚 $available available @ $price $unit";
		
		$market_info = compact('domain', 'domain', 'available', 'price', 'unit');
		break; // break after first successful match


	}

//if (isset($_GET['dev'])) exit($seller);
?>

