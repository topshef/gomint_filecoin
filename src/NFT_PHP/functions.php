<?php

	// ┌─┐┬ ┬┌┐┌┌─┐┌┬┐┬┌─┐┌┐┌┌─┐
	// ├┤ │ │││││   │ ││ ││││└─┐
	// └  └─┘┘└┘└─┘ ┴ ┴└─┘┘└┘└─┘
	// functions
	function getCIDfromURL($url) {
		//superceded by dissectPathIPFS
		//move this to helpers later
		
		//$parts = explode('://', $url)[1];
		//https://stackoverflow.com/questions/3679033/multiple-explode-characters-with-comma-and-hyphen
		//$keywords = preg_split("/[\s,-]+/", "This-sign, is why we can't have nice things");
		//var_dump($keywords);
		//$parts = preg_split("/[\s,-/]+/", $url);
		//$parts = preg_split("/[/]+/", $url);
		$parts = $url;
		$parts = str_replace('.', '/', $parts);
		$parts = explode('/', $parts);
		foreach ($parts as $part) {
			$isCID = false;
			
			if (substr($part,0,3) == 'baf' && strlen($part) == 59) $isCID = true;
			if (substr($part,0,2) == 'Qm'  && strlen($part) == 46) $isCID = true;
			if ($isCID) return $part;
		}
		
	}
	
	
	function dissectPathIPFS($url) {
		// same as getCIDfromURL but also returns filename if present		
		//move this to helpers later
		
		$parts = $url;
		$parts = str_replace('.', '/', $parts);
		$parts = explode('/', $parts);
		foreach ($parts as $part) {
			$isCID = false;
			
			if (substr($part,0,3) == 'baf' && strlen($part) == 59) $isCID = true;
			if (substr($part,0,2) == 'Qm'  && strlen($part) == 46) $isCID = true;
			if ($isCID) $out['CID'] = $part;
			
		}
		
		$hasFilename = false;
		
		$ext = pathinfo($url)['extension'];
		
		if (in_array($ext,['png', 'jpg'])) $hasFilename = true;
		
		if ($hasFilename) {
			$filename = basename($url);
			$out['filename'] = $filename;
			$out['ext'] = $ext;
		}
		
		return $out;
		
	}


	function getLinksFromHTML($in) {
		$url = $in['url'];
		$keyword_url = $in['keyword_url'];
		$keyword_display = $in['keyword_display'];

		//https://www.the-art-of-web.com/php/parse-links/
		//$url = "http://www.example.net/somepage.html";
		$input = getCacheData($url);
		if ($input == null)	return "error: could not access $url";
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
			  if ($keyword_url != null && strpos($match[2], $keyword_url) === false) continue;
			  if ($keyword_display != null && strpos($match[3], $keyword_display) === false) continue;
			  $i += 1;
			  $out[$i]['url'] = $match[2];
			  $out[$i]['display'] = $match[3];
			}
		}
		return $out;
	}	
  


	// for Xact wallet and others - get the full asset path given only the CID to the folder
	function getFilepathFromCID($cid) {
		$in['keyword_url'] = "$cid/";
		$in['url'] = "https://cloudflare-ipfs.com/ipfs/$cid";
		$out = getLinksFromHTML($in);
		$asset_url = "https://cloudflare-ipfs.com/{$out[1]['url']}";
		return $asset_url;
	}
	


	function getFileCID($url) {

		$input = getCacheData($url);
		if ($input == null)	return "error: could not access $url";
		if (strpos($input,'<!DOCTYPE') === false) return false;
		
		$parts = explode('href="/ipfs/',$input);
		unset($parts[0]);
		foreach ($parts as $part) {
			$out = substr($part,0,59);
			//bafybeid4nu36bcuxmicw4qra25wlktx4xkmdprw4rpofrlwsaq7g47o57y
		}
		
		return $out;
		
	}	


	function getCacheData($url, $timeoutseconds = null) {  // default never timeout
		/* get a file from cache or download it
		$timeoutseconds = 0      don't use cache
		$timeoutseconds = null   always use cache if available
		*/
		
		$hash = hash('SHA1', $url);
		$cachepath = "./cache/autocache.$hash.txt";
		if (file_exists($cachepath)) {
			if ($timeoutseconds == null) return file_get_contents($cachepath); // never timeout
			
			$ageseconds = time() - filemtime($cachepath);
			if ($ageseconds < $timeoutseconds) return file_get_contents($cachepath);
		}
		
		// otherwise read from url
		$data = file_get_contents($url);
		if ($data) {
			file_put_contents("./cache/dir.txt", time() . "\t$hash\t$url\n", FILE_APPEND);
			file_put_contents($cachepath, $data);
		}
		return $data;

	}

  
	function getPathIFPS($cid, $filename = null, $provider ='infura') {

		if ($provider == null) $provider ='infura';
		
		$url['infura'] = "https://$cid.ipfs.infura-ipfs.io/$filename";
		$url['cloudfare'] = "https://https://cloudflare-ipfs.com/ipfs/$cid/$filename";
		$url['ipfsio'] = "https://ipfs.io/ipfs/$cid/$filename";
				
		$url = $url[$provider];
		
		return $url;
	
	}
	
	if (isset($_GET['cid'])) echo getPathIFPS($_GET['cid'], $_GET['filename'], $_GET['provider']);  // testing
	
	if (isset($_GET['url'])) echo "<PRE>" . print_r(dissectPathIPFS($_GET['url']),true);  // testing

	// not sure if we should use this
	// display performance might be faster from our server, but to be reviewed re stress
	function getCacheURL_IPFS($cid, $download_if_missing = true) {
		$cachepattern = "./cache/ipfs.$cid.*";
		$files = glob($cachepattern);
		if ($files == null) {
			if (!$download_if_missing) return false; // allows lookup without download
			//download
			$url_ipfs = "https://$cid.ipfs.infura-ipfs.io";
			$cachepath = "./cache/ipfs.$cid.download";
			$data = file_get_contents($url_ipfs);
			$result = file_put_contents($cachepath,$data);
			if ($result) {
				//return $cachepath;
				$mimetype = mime_content_type($cachepath);
				$ext = explode('/', $mimetype)[1];
				$newpath = "./cache/ipfs.$cid.$ext";
				rename($cachepath, $newpath);
				return $newpath;
			}
			else return $result;
		} else {
			$filename = basename($files[0]);
			$localdir = __DIR__;
			$docroot = $_SERVER['DOCUMENT_ROOT'];
			$dir = str_replace($docroot, "https://{$_SERVER['SERVER_NAME']}",$localdir);
			return "$dir/cache/$filename";
		}
	}


