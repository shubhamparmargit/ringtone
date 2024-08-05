<?php
    include("includes/db_helper.php");
    include("includes/lb_helper.php");
    include("language/language.php");
    require_once("thumbnail_images.class.php");
    
    if(isset($_POST['submit'])) {
        
        $ext = pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION);
        $profile_img=rand(0,99999)."_banner.".$ext;
        $tpath1='images/'.$profile_img;
        
        if($ext!='png')  {
            $pic1=compress_image($_FILES["banner_image"]["tmp_name"], $tpath1, 80);
        } else {
            $tmp = $_FILES['banner_image']['tmp_name'];
            move_uploaded_file($tmp, $tpath1);
        }
        
        if($_POST['register_password'] == "") {
            
            $_SESSION['msg']="enter_password_admin";
            $_SESSION['class']='error'; 
            
        } else if($_POST['register_confirm_password'] == "") {
            
            $_SESSION['msg']="enter_confirm_password_admin";
            $_SESSION['class']='error'; 
            
        } else if($_POST['register_confirm_password'] != $_POST['register_password']) {
            
            $_SESSION['msg']="error_pass_not_match_admin";
            $_SESSION['class']='error'; 
            
       	} else if($_POST['register_confirm_password'] == $_POST['register_password']) {
            
            $data = array(
              'username'  => $_POST['register_name'],
              'email'  =>  $_POST['register_email'],
              'password'  =>  md5(trim($_POST['register_password'])),
              'image'  => $profile_img,
              'status'  => '0',
            );
            
            $qry = Insert('tbl_admin',$data);
            
            $_SESSION['msg']="register_success_admin";
            $_SESSION['class']='success'; 
            
        } else {
            
            $_SESSION['msg']="register_fail_admin";
            $_SESSION['class']='error'; 
            
        }
        
        header( "Location:auth_register.php");
        exit;
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

	<!-- Seo Meta -->
    <meta name="description" content="Admin panel | Dashboard">
    <meta name="keywords" content="css3, html5">
    
    <!-- Website Title -->
    <title>Login | <?php echo APP_NAME;?></title>
    
    <!-- Favicon --> 
    <link href="images/<?php echo APP_LOGO;?>" rel="icon" sizes="32x32">
    <link href="images/<?php echo APP_LOGO;?>" rel="icon" sizes="192x192">

    <!-- IOS Touch Icons -->
    <link rel="apple-touch-icon" href="images/<?php echo APP_LOGO;?>">
    <link rel="apple-touch-icon" sizes="152x152" href="images/<?php echo APP_LOGO;?>">
    <link rel="apple-touch-icon" sizes="180x180" href="images/<?php echo APP_LOGO;?>">
    <link rel="apple-touch-icon" sizes="167x167" href="images/<?php echo APP_LOGO;?>">

    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vendor styles -->
    <link rel="stylesheet" href="assets/vendors/bootstrap/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.css" type="text/css">
    <link rel="stylesheet" href="assets/vendors/remixicon/remixicon.min.css" type="text/css">
    <link rel="stylesheet" href="assets/vendors/quill/quill.min.css" type="text/css">
    <link rel="stylesheet" href="assets/vendors/select2/select2.min.css" type="text/css">

    <!-- Main style -->
    <link rel="stylesheet" href="assets/css/styles.css" type="text/css">
    
    <!--[if lt IE 9]>
	    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>
<body>
    
    <!-- Loader -->
    <div id="nsofts_loader">
        <div class="text-center">
            <i class="ri-3x ri-donut-chart-line nsofts-loader-icon"></i>
            <span class="d-block">Loading</span>
        </div>
    </div>


    <!-- Start: 404 -->
    <main class="d-flex justify-content-center align-items-center py-5 min-vh-100">
        <div class="container">
            <div class="col-xl-4 col-lg-5 col-md-7 col-sm-9 mx-auto">
                <div class="nsofts-auth position-relative">
                    <img src="assets/images/pattern-1.svg" class="nsofts-auth__pattern-1 position-absolute" alt="">
                    <img src="assets/images/pattern-2.svg" class="nsofts-auth__pattern-2 position-absolute" alt="">
                    <div class="card position-relative">
                        <div class="card-body px-4 py-4">
                            
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <h5>Welcome to <?php echo APP_NAME;?>!</h5>
                                    <p>Please Sign-up to your account</p>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="email" class="nsofts-input-icon__left">
                                            <i class="ri-user-line"></i>
                                        </label>
                                        <input type="text" name="register_name" id="register_name"  class="form-control" autocomplete="off" placeholder="Enter your username" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="email" class="nsofts-input-icon__left">
                                            <i class="ri-at-line"></i>
                                        </label>
                                        <input type="text" name="register_email" id="register_email"  class="form-control" autocomplete="off" placeholder="Enter your email " required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="email" class="nsofts-input-icon__left">
                                             <i class="ri-lock-2-line"></i>
                                        </label>
                                        <input type="text" name="register_password" id="register_password"  class="form-control" autocomplete="off" placeholder="Enter your password " required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="email" class="nsofts-input-icon__left">
                                             <i class="ri-lock-2-line"></i>
                                        </label>
                                        <input type="text" name="register_confirm_password" id="register_confirm_password"  class="form-control" autocomplete="off" placeholder="Enter your confirm password " required>
                                    </div>
                                </div>
                                
                                <div class="mb-3 mt-2">
                                    <input type="file" class="form-control-file" name="banner_image"  accept=".png, .jpg, .JPG .PNG" onchange="fileValidation()" id="fileupload">
                                </div>
                                
                                <div class="mb-3 col-sm-9">
                                    <div class="fileupload_img" id="imagePreview">
                                    <img class="col-sm-3 img-thumbnail" type="image" src="assets/images/300x300.jpg" alt="image" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button  type="submit" name="submit" class="btn btn-primary btn-lg w-100">Register</button>
                                </div>
                                <p class="text-center">Already have an account? <a href="index.php" class="text-decoration-none">Login</a></p>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- End: 404 -->
    

    <!-- Vendor scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="assets/vendors/notify/notify.min.js"></script>
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/vendors/quill/quill.min.js"></script>
    <script src="assets/vendors/select2/select2.min.js"></script>

    <!-- Main script -->
    <script src="assets/js/main.js"></script>
    
    <script type="text/javascript">
    
        function fileValidation(){
            var fileInput = document.getElementById('fileupload');
            var filePath = fileInput.value;
            var allowedExtensions = /(\.png|.jpg|.jpeg|.PNG|.JPG|.JPEG)$/i;
            if(!allowedExtensions.exec(filePath)){
                if(filePath!='')
                fileInput.value = '';
                $.notify('Please upload file having extension .png, .jpg, .jpeg .PNG, .JPG, .JPEG only!', { position:"top right",className: 'error'} ); 
                return false;
            }else{
                if (fileInput.files && fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $("#imagePreview").find("img").attr("src", e.target.result);
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
        }
  
    </script> 
    
    <?php if (isset($_SESSION['msg'])) { ?>
        <script type="text/javascript">
            $('.notifyjs-corner').empty();
            $.notify(
            '<?php echo $client_lang[$_SESSION["msg"]]; ?>', {
                position: "top right",
                className: '<?= $_SESSION["class"] ?>'
            }
            );
        </script>
        <?php
        unset($_SESSION['msg']);
        unset($_SESSION['class']);
    }?>

</body>
</html>