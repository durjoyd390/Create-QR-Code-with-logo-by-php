<?php 
if (isset($_POST['create_qr'])) {
$create_qr = $_POST['create_qr'];
if (empty($create_qr)) {
$response = json_encode(array( 'error' => 'Please Input URL or Text!', 'qr' => '0' ));
exit($response);
}else{

include('phpqrcode/qrlib.php');
$filepath = 'img/qr/'.rand(1000000000,9999999999).'.png';
$logopath = 'img/logo.png';
$codeContents = $create_qr;
QRcode::png($codeContents,$filepath , QR_ECLEVEL_H, 20);

$QR = imagecreatefrompng($filepath);
$logo = @imagecreatefromstring(file_get_contents($logopath));
imagecolortransparent($logo , imagecolorallocatealpha($logo , 0, 0, 0, 127));
imagealphablending($logo , false);
imagesavealpha($logo , true);

$QR_width    = imagesx($QR);
$QR_height   = imagesy($QR);
$logo_width  = imagesx($logo);
$logo_height = imagesy($logo);

$logo_qr_width  = $QR_width/5;
$scale          = $logo_width/$logo_qr_width;
$logo_qr_height = $logo_height/$scale;
imagecopyresampled($QR, $logo, $QR_width/2.5, $QR_height/2.5, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
imagepng($QR,$filepath);

// Delete QR codes that are 5 minutes or older.
$qr_path = "img/qr/";
foreach (glob($qr_path."*") as $file) {
if(time() - filectime($file) > 300){ unlink($file); }
}

$response = json_encode(array( 'error' => '1', 'qr' => $filepath ));
exit($response);
}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Create QR Code With Logo</title>
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
 
<style type="text/css">
.divider:after,
.divider:before {
content: "";
flex: 1;
height: 1px;
background: #eee;
}
.h-custom {
height: calc(100% - 73px);
}
@media (max-width: 450px) {
.h-custom {
height: 100%;
}
}
</style>
</head>
<body>
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="img/demoqr.png" id="new_qr" class="img-fluid" alt="QR Code">
      </div>
<div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
 <h3>Create QR Code With Logo</h3><hr>
   <div class="form-outline mb-4">
    <textarea class="form-control form-control-lg" placeholder="Enter URL or Text Here..." rows="2" id="qr_data"></textarea>
   </div>
  <input type="hidden" id="unlink_status" value="0">
   <div class="text-center text-lg-start mt-4 pt-2">
    <button type="button" class="btn btn-primary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem;" name="submit" id="submit">Create</button>
   </div>
      </div>
    </div>
  </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
  $(document).ready(function () {
    $('#submit').click(function (e) {
      e.preventDefault();
       $('#submit[name="submit"]').html('<i style="font-size:20px;" class="fas fa-cog fa-spin">');
        setTimeout(function(){
            $('#submit[name="submit"]').html('Create');
        }, 1100);
      var qr_data = $('#qr_data').val();
      $.ajax
        ({
          type: "POST",
          url: "index.php",
          data: {"create_qr": qr_data},
success: function (data) {
if (data && data.length > 0) {   
data=$.parseJSON( data );
error=data.error;
qr=data.qr;
if (error != 1) {
swal({ text: error, icon: "error", button: "Close", });	
}else{
$("#new_qr").attr("src",qr);
$("#unlink_status").val(error);
}
}
}
 });
   });
     });
</script>
</body>
</html>