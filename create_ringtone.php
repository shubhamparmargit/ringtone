<?php 
    $page_title=(isset($_GET['ringtone_id'])) ? 'Edit Ringtone' : 'Create Ringtone';
    include("includes/header.php");
    require("includes/lb_helper.php");
    require("language/language.php");
    require_once("thumbnail_images.class.php");
    
    $page_save=(isset($_GET['ringtone_id'])) ? 'Save' : 'Create';
    
    //Users
    $users_qry="SELECT * FROM tbl_users ORDER BY user_name";
    $users_result=mysqli_query($mysqli,$users_qry); 
    
    //Category
    $cat_qry="SELECT * FROM tbl_category ORDER BY category_name";
    $cat_result=mysqli_query($mysqli,$cat_qry); 
    
    
    if(isset($_POST['submit']) and isset($_GET['add'])){
        
        $audio_type = trim($_POST['audio_type']);
        $is_hyped = isset($_POST['is_hyped']) ? 1 : 0;
        if($audio_type=='server_url'){
            
            $audio_url = htmlentities(trim($_POST['audio_url']));
        } else {
            $path = "uploads/";
            $audio_local=rand(0,99999)."_".str_replace(" ", "-", $_FILES['audio_local']['name']);
            $tmp = $_FILES['audio_local']['tmp_name'];
            
            if (move_uploaded_file($tmp, $path.$audio_local)) {
                $audio_url = $audio_local;
            } else {
                echo "Error in uploading mp3 file !!";
                exit;
            }
        }
        
        $data = array( 
            'cat_id'  =>  trim($_POST['cat_id']),
            'user_id'  =>  trim($_POST['user_id']),
            'ringtone_title'  =>  cleanInput($_POST['ringtone_title']),
            'audio_type'  =>  $audio_type,
            'ringtone_url'  =>  $audio_url,
            'is_hyped' => $is_hyped,
            'play_times'  =>  cleanInput($_POST['play_times']),
        );  
        
        $qry = Insert('tbl_ringtone',$data);
        
        $_SESSION['msg']="10";
        $_SESSION['class']='success';
        header( "Location:manage_ringtone.php");
        exit;
    }
    
    if(isset($_GET['ringtone_id'])){
        $qry="SELECT * FROM tbl_ringtone where id='".$_GET['ringtone_id']."'";
        $result=mysqli_query($mysqli,$qry);
        $row=mysqli_fetch_assoc($result);
    }
    
    if(isset($_POST['submit']) and isset($_POST['ringtone_id'])){
        
        $audio_type = trim($_POST['audio_type']);
        if($audio_type=='server_url'){
            
            $audio_url = htmlentities(trim($_POST['audio_url']));
        } else {
            
            if($_FILES['audio_local']['name']!=""){
                $path = "uploads/";
                $audio_local=rand(0,99999)."_".str_replace(" ", "-", $_FILES['audio_local']['name']);
                $tmp = $_FILES['audio_local']['tmp_name'];
                
                if (move_uploaded_file($tmp, $path.$audio_local)) {
                    $audio_url = $audio_local;
                } else {
                    echo "Error in uploading mp3 file !!";
                    exit;
                }
            } else {
                $audio_url=$row['ringtone_url'];
            }
        }
        
        $data = array( 
            'cat_id'  =>  trim($_POST['cat_id']),
            'user_id'  =>  trim($_POST['user_id']),
            'ringtone_title'  =>  cleanInput($_POST['ringtone_title']),
            'audio_type'  =>  $audio_type,
            'ringtone_url'  =>  $audio_url
        ); 
        
        $category_edit=Update('tbl_ringtone', $data, "WHERE id = '".$_POST['ringtone_id']."'");
        
        $_SESSION['msg']="11";
        $_SESSION['class']='success';
        header( "Location:create_ringtone.php?ringtone_id=".$_POST['ringtone_id']);
        exit;
    }

?>

<!-- Start: main -->
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?php echo (isset($page_title)) ? $page_title : "" ?></li>
            </ol>
        </nav>
        
        <form action="" name="addeditaudio" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h5 class="mb-4"><?=$page_title ?></h5>
                            
                            <div class="mb-3">
                                <select name="user_id" id="user_id" class="nsofts-select " required>
                                    <option value="">--Select User --</option>
                                    <?php while($users_row=mysqli_fetch_array($users_result)){ ?>
                                        <?php if(isset($_GET['ringtone_id'])){ ?>
                                            <option value="<?php echo $users_row['id'];?>" <?php if($users_row['id']==$row['user_id']){?>selected<?php }?>><?php echo $users_row['user_name'];?></option>	          							 
                                        <?php }else{ ?>
                                            <option value="<?php echo $users_row['id'];?>"><?php echo $users_row['user_name'];?></option>   							 
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <select name="cat_id" id="cat_id" class="nsofts-select " required>
                                    <option value="">--Select Category --</option>
                                    <?php while($cat_row=mysqli_fetch_array($cat_result)){ ?>      
                                        <?php if(isset($_GET['ringtone_id'])){ ?>
                                            <option value="<?php echo $cat_row['cid'];?>" <?php if($cat_row['cid']==$row['cat_id']){?>selected<?php }?>><?php echo $cat_row['category_name'];?></option>	          							 
                                        <?php }else{ ?>
                                            <option value="<?php echo $cat_row['cid'];?>"><?php echo $cat_row['category_name'];?></option>   							 
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <input type="text" name="ringtone_title" id="ringtone_title" class="form-control" value="<?php if(isset($_GET['ringtone_id'])){echo $row['ringtone_title'];}?>" placeholder="Enter Ringtone title" required>
                            </div>
                            
                            </br>
                            <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;"><?=$page_save?></button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h5 class="mb-4">Upload Ringtone</h5>
                            
                            <div class="mb-3">
                                <select name="audio_type" id="audio_type" class="nsofts-select" required>
                                    <?php if(isset($_GET['ringtone_id'])){ ?>
                                        <option value="server_url" <?php if($row['audio_type']=='server_url'){?>selected<?php }?>>From Server(URL)</option>
                                        <option value="local" <?php if($row['audio_type']=='local'){?>selected<?php }?>>Browse From Device</option>
                                    <?php } else { ?>
                                        <option value="server_url">From Server(URL)</option>
                                        <option value="local">Browse From Device</option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="audio_url_display" <?php if(isset($_GET['ringtone_id'])){ ?> <?php if($row['audio_type']=='local'){?>style="display:none;"<?php }?> <?php } ?>>
                                <input type="text" name="audio_url" id="audio_url" class="form-control" value="<?php if(isset($_GET['ringtone_id'])){echo $row['audio_url'];}?>" placeholder="Enter Ringtone URL" >
                            </div>
                            
                            <div class="mb-3" id="audio_local_display" <?php if(isset($_GET['ringtone_id'])){ ?> <?php if($row['audio_type']!='local'){?>style="display:none;"<?php }?> <?php } else { ?>  style="display:none;" <?php } ?>>
                                <input type="file" class="form-control-file" name="audio_local" accept=".mp3" onchange="fileValidationAudio()" id="audio_local" >
                            </div>
                            
                            <?php if(isset($_GET['ringtone_id'])){ ?>
                                <?php
                                $file_path = null;
                                  $audio_file=$row['ringtone_url'];
                                  if($row['audio_type']=='local'){
                                    $audio_file=$file_path.'uploads/'.basename($row['ringtone_url']);
                                  } 
                                  ?>
                            <?php } else { ?>
                                <?php $audio_file=""; ?>
                            <?php } ?>
                            
                            <div id="audio_player" class="nemosofts-player" <?php if(isset($_GET['ringtone_id'])){ ?> <?php if($row['audio_type']!='local'){?>style="display:none;"<?php }?> <?php } else { ?>  style="display:none;" <?php } ?>>
                                <p><strong>Current URL:</strong> <?=$audio_file?></p>
                                <audio id="audio" controls src="<?=$audio_file?>"></audio>  
                            </div>

                            <div class="mb-3">
                                <input type="checkbox" name="is_hyped" id="is_hyped" class="form-check-input"> <?php if(isset($row['is_hyped']) && $row['is_hyped'] == 1) { echo 'checked'; } ?>
                                <label for="is_hyped">Mark as Hyped</label>
                            </div>

                            <div class="mb-3" id="play_timesDiv" style="display:none;">
                                <label for="play_times" class="form-label">Play Times</label>
                                <input type="number" name="play_times" id="play_times" class="form-control" placeholder="Enter play times, e.g. 5"><?php if(isset($_GET['ringtone_id'])){ echo $row['play_times']; } ?></textarea>
                            </div>
                            
                        </div>
                    </div>
                </div>


            </div>

        </form>
    </div>
</main>
<!-- End: main -->
    
<?php include("includes/footer.php");?>
<script type="text/javascript">
    $(document).ready(function(e) {
        $("#audio_type").change(function(){
            var type=$("#audio_type").val();
            if(type=="server_url"){
                $("#audio_url_display").show();
                $("#audio_local_display").hide();
                $("#audio_player").hide();
                $("#audio_local").val('');
                $("#audio").attr('src','');
            } else {
                $("#audio_url_display").hide(); 
                $("#audio_local_display").show();
                $("#audio_player").hide();
            }
        });

        $("#is_hyped").change(function(){
           if ($(this).is(":checked")) {
                $('#play_timesDiv').show();
            }else{
                 $('#play_timesDiv').hide();
            }
        });
    });

    var objectUrl;
    $("#audio_local").change(function(e){
        var file = e.currentTarget.files[0];
        $("#filesize").text(file.size);
        objectUrl = URL.createObjectURL(file);
        $("#audio").prop("src", objectUrl);
        $("#audio_player").show();
    });
  
    function fileValidationAudio(){
        var fileInput = document.getElementById('audio_local');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.MP3|.mp3)$/i;
        if(!allowedExtensions.exec(filePath)){
            if(filePath!='')
            fileInput.value = '';
            $.notify('Please upload file having extension .MP3 only.', { position:"top right",className: 'error'} ); 
            return false;
        }
    }
</script>