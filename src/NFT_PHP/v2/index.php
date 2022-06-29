<?php 

//require_once 'config.php';

// set user
//require_once("{$_SERVER['DOCUMENT_ROOT']}/kpay/kpayid.php");


require_once "{$_SERVER['DOCUMENT_ROOT']}/headheader.php";	

$url_banner = 'https://shop.gomint.me/assets/images/oldmint.jpg';

// new base required as html base is updated

$folder = dirname($_SERVER["PHP_SELF"]);
$path_base_local = "https://{$_SERVER['SERVER_NAME']}{$folder}/";
//echo "$path_base_local {$_SERVER["REQUEST_URI"]}  {$_SERVER["PHP_SELF"]}";


?>

    <!-- Page Content -->
    <div class="page-heading header-text" style="background-image: url('<?php echo $url_banner; ?>');" >
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h1>Explore NFTs</h1>
            <span>On hedera</span>
          </div>
        </div>
      </div>
    </div>
    <div class="callback-form contact-us" style = "margin-top: 0px;">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <span>Enter <em>token ID</em></span>
            </div>
          </div>
          <div class="col-md-12">
            <div class="contact-form">
              <form id="contact" action="<?php echo $path_base_local; ?>view.php" method="post">
                <div class="row">
								

                  <div class="col-lg-3 col-md-12 col-sm-12">

                  </div>
    

                  <div class="col-lg-2 col-md-12 col-sm-12">
                    <fieldset>
                      <!--<input name="network" type="text" class="form-control" placeholder="network" required=""> -->
					  <select name="network" class="form-control" data-msg-required="This field is required.">
							 <option value="mainnet">mainnet</option>
							 <option value="testnet">testnet</option>
							 <option value="previewnet">previewnet</option>
					  </select>
					  
                    </fieldset>
                  </div>
                 												
                  <div class="col-lg-4 col-md-12 col-sm-12">
                    <fieldset>
                      <input name="tokenId" type="text" class="form-control" placeholder="token ID eg 0.0.654321-42 (serial optional)" required="">
                    </fieldset>
                  </div>

                  <div class="col-lg-3 col-md-12 col-sm-12">

                  </div>				  
                 
			 
                  <div class="col-lg-12">
                    <fieldset>
                      <button type="submit" id="form-submit" class="filled-button">Lookup</button>
                    </fieldset>
                  </div>
				 
				  
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>




<?php 
//include "{$_SERVER['DOCUMENT_ROOT']}/footer.php";	

?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/accordions.js"></script>

    <script language = "text/Javascript"> 
      cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
      function clearField(t){                   //declaring the array outside of the
      if(! cleared[t.id]){                      // function makes it static and global
          cleared[t.id] = 1;  // you could use true and false, but that's more typing
          t.value='';         // with more chance of typos
          t.style.color='#fff';
          }
      }
    </script>

  </body>
</html>