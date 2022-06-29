<?php

	// ┌─┐┌─┐┌┬┐  ╔╗╔╔═╗╔╦╗  ┬┌┐┌┌─┐┌┬┐┌─┐┌┐┌┌─┐┌─┐
	// │ ┬├┤  │   ║║║╠╣  ║   ││││└─┐ │ ├─┤││││  ├┤ 
	// └─┘└─┘ ┴   ╝╚╝╚   ╩   ┴┘└┘└─┘ ┴ ┴ ┴┘└┘└─┘└─┘
	// get NFT instance (serial)
	
		$string = $tokenClass['symbol'];
	
	$pattern = '/(?:(?:\d+)\.){2}\d+/';
	preg_match($pattern, $string, $matches);
	$fileId = $matches[0];
	//exit("X{$fileId}X");

	$url = "https://{$_SERVER['SERVER_NAME']}/api/HFS/?network=$network&fileId=$fileId&raw";
	
	//$file_content = getCacheData($url); // this is probably double-cached!
	$file_content = file_get_contents($url); // this is probably double-cached!
	//exit($file_content );
	$metadata = json_decode($file_content, true);
	
	$tokenInstanceName = $metadata['properties']['name'];
	$tokenDescription = $metadata['properties']['description'];

	$tokenAssetURL = $metadata['properties']['image'];

	//exit($tokenAssetURL);
	$tokenAsset = "<img src='$tokenAssetURL' width='300'>";
	
	$tokenAssetURL = $metadata['properties']['properties']['media'];
	
	$tokenAsset .= "<video width='300' controls autoplay><source src='$tokenAssetURL' type='video/mp4'></video>";
	
	//$metadata['properties']['description'];

	
	$tokenMetadata	= $file_content;
	
	$tokenCategory = $metadata['properties']['properties']['category'];
	$tokenCreator = $metadata['properties']['properties']['qualities']['Creator'];
	
	$metadataUnwrapped = json_encode($metadata, JSON_PRETTY_PRINT);
