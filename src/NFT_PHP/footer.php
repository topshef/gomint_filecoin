<?php
// from Corey.. add $embed if image is missing



?>


<B>Technical comments or feedback welcome</B>
<BR>
(comments are not shown publicly, they are only shared with the token owner or minter. Please include your contact details if you want a reply)
<BR><BR>
<form action="submitComment.php" method="post">
<input hidden name="productId" id="productId" type="text" value="<?php echo $productId;?>" />    
 <textarea type="text" name="comments" rows="5" cols="60"></textarea>
 <BR><BR>
 <input type="submit" value="submit" />
</form>

<script>
	(function(){
		//const img1 = document.querySelector("body > table > tbody > tr:nth-child(13) > td:nth-child(2) > img");
		const dynamic_image = document.getElementById("dynamic_image");
		
		checkImageExists(dynamic_image).then(imageExists=>{
			if(!imageExists && window.location.href.search("embed") === -1 ){
				window.location.href = window.location.href + "&embed";
			}
		});


		/**
		 * Returns promise with true if image exists.
		 *
		 * @param img - img element
		 * @returns promise (boolean)

		 */
		async function checkImageExists(img) {
			let response = await fetch(img.src);

			return await response.blob().then((blob)=>{
				let blobType = blob.type;
		//return blob.type;
				if (blobType == "text/plain") {
					return false;
				}
				return true;
			});
		}
		
		
	})();	
</script>


