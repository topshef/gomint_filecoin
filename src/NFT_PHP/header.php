<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="GoMint your content!">
    <meta property="og:url" content="https://gomint.me">
    <meta property="og:description" content="Tokenize your media so fans can invest in your content and be rewarded with a share of the media's future revenue">
    <meta property="og:image" content="https://gomint.me/img/GoMintThumbnail.PNG">
	<link rel="shortcut icon" href="img/K.ico">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@gomintme">
	<meta name="twitter:creator" content="@gomintme">
<?php if ($cleanTokenName != null) {  ?>
	<meta name="twitter:title" content="<?php echo $cleanTokenName; ?>">
	<meta name="twitter:description" content="<?php echo $cleanTokenDescription; ?>">
	<meta name="twitter:image" content="<?php echo $tokenAssetTwitterImageURL; ?>">
<?php } else {  ?>
	<meta name="twitter:title" content="Lookup any HTS token on GoMint">
	<meta name="twitter:description" content="View, Mint, Buy, Sell any HTS token on GoMint">
	<meta name="twitter:image" content="https://gomint.me/img/query_card.png">
<?php  } ?>	
		
    <title>GoMint</title>
	<link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300&display=swap" rel="stylesheet">
	<link rel="prefetch" href="<?php echo $url_prev; ?>" />
	<link rel="prefetch" href="<?php echo $url_next; ?>" />

<style>
/* Mobile phones */
@media (max-width: 767px) {
    table {
        table-layout: fixed;
    }
    table tr td {
        display: block;
        border: none;
        padding: 5px 10px;
    }
    table tr td:first-child {
        font-weight: bold;
        padding-top: 10px;
    }
    table tr td:last-child {
        padding-bottom: 10px;
    }
    table tr:not(tr:first-child) td {
        text-align: left !important;
    }
    table tr td pre {
        display: block;
        border: none;
        padding: 5px 10px;
        word-break: break-word;
        white-space: pre-wrap;
    }
    table tr td span.smaller_grey {
        word-break: break-word;
    }

}

.smaller_grey {
	color: #4d4d4d;
	font-size: 75%;
}

a { text-decoration: none; }
			
a:hover {font-weight: 800;}

/*
table tr td:first-child {
	font-weight: bold;
}
*/
	
table, th, td {
  border: 2px solid white;
  padding: 4px;
  /* max-width: 450px; */

}

table {
  margin-left: auto;
  margin-right: auto;
}

td {
//  vertical-align: baseline;
  vertical-align:top;

}

tr:nth-child(even) {background: #ebf6ff}

/*
	tr:nth-child(6n) {background: #ebf6ff}
	tr:nth-child(6n+1) {background: #ebf6ff}
	tr:nth-child(6n+2) {background: #ebf6ff}
*/


button, input, textarea, select  {
	font-family: 'Work Sans', sans-serif !important;
	font-size: 14.8px;
    font-weight: 600;
	//border: 2px solid #333333;
	//border: 1px solid grey;
	//border-radius: 3px;
	/* resize: none;  */
}


plaincode {
text-align: left;
}

img {
	border-radius: 3px;
}

body {
  
  background-color: white;
    font-family: 'Work Sans', sans-serif  !important;
	text-align: center;
	color: dark-grey;
}

p {  
    font-family: 'Work Sans', sans-serif  !important;
	text-align: center;
	color: dark-grey;
}


select{
	font-family: 'Work Sans', sans-serif  !important;
	font-size: 14.8px;
	font-weight: 600;
    width: 168px;
    height: 22px;
}


model-viewer {
    
	width: 300;
}

model-viewer#reveal {
    --poster-color: transparent;
	width: 300px;
}
 

  
</style>

<BR>
<a href='https://gomint.co'><img src="https://gomint.me/img/GoMintLogo.png" height="42"></a>&nbsp&nbsp
<a href='https://filecoin.io/'><img src="https://gomint.me/img/filecoin.jpg" height="45"></a>&nbsp&nbsp
<a href='https://ipfs.io/'><img src="https://gomint.me/img/ipfs.jpg" height="40"></a>&nbsp&nbsp
<!--<a href='https://nft.storage/'><img src="https://gomint.me/img/nft.storage.PNG" height="40"></a>&nbsp&nbsp-->
<a href='https://hedera.com/'><img src="https://gomint.me/img/HEDERA.PNG" height="45"></a>


<BR><BR>
</head>