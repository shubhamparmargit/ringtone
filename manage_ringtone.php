<?php $page_title = "Manage Ringtone";
    include("includes/header.php");
    require("includes/lb_helper.php");
    require("language/language.php");
    
    if(!isset($_SESSION['admin_type'])){
        if($_SESSION['admin_type'] == 0){
            session_destroy();
            header( "Location:index.php");
            exit;
        }
    }
    
    $tableName="tbl_ringtone";   
    $targetpage = "manage_ringtone.php"; 
    $limit = 10; 
    $keyword='';
    
    if(!isset($_GET['keyword'])){
        $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_ringtone.active='1'";
        
    } else{
        
        $keyword=addslashes(trim($_GET['keyword']));
        $query = "SELECT COUNT(*) as num FROM $tableName WHERE (`ringtone_title` LIKE '%$keyword%' AND tbl_ringtone.active='1')";
        $targetpage = "manage_ringtone.php?keyword=".$_GET['keyword'];
    }
    
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
    $total_pages = $total_pages['num'];
    
    $stages = 3;
    $page=0;
    if(isset($_GET['page'])){
        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
    }
    if($page){
        $start = ($page - 1) * $limit; 
    } else {
        $start = 0; 
    }
    if(!isset($_GET['keyword'])){
        $sql_query="SELECT * FROM tbl_ringtone WHERE tbl_ringtone.active='1' ORDER BY tbl_ringtone.`id` DESC LIMIT $start, $limit"; 
    } else {
        $sql_query="SELECT * FROM tbl_ringtone WHERE tbl_ringtone.active='1' WHERE (`ringtone_title` LIKE '%$keyword%') ORDER BY tbl_ringtone.`id` DESC LIMIT $start, $limit"; 
    }
    $result=mysqli_query($mysqli,$sql_query) or die(mysqli_error($mysqli)); 
?>
<link rel="stylesheet" type="text/css" href="assets/player/progres-bar.css">

<!-- Start: main -->
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?php echo (isset($page_title)) ? $page_title : "" ?></li>
            </ol>
        </nav>
            
        <div class="card h-100">
            <div class="card-header d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                <a href="<?php echo (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "dashboard.php" ?>" class="d-inline-flex align-items-center text-decoration-none fw-semibold nsofts-link">
                    <i class="ri-arrow-left-line text-danger fw-bold"></i>
                    <span class="ps-2 lh-1"><?=$page_title ?></span>
                </a>
                <span class="ps-2 lh-1">Total Ringtone (<?=$total_pages ?>)</span>
                <div class="d-flex mt-2 mt-md-0">
                    <form method="get" id="searchForm" action="" class="me-2">
                        <div class="input-group">
                            <input type="text" id="search_input" class="form-control" placeholder="Search here..." name="keyword" value="<?php if(isset($_GET['keyword'])){ echo $_GET['keyword'];} ?>" required="required">
                            <button class="btn btn-outline-default d-inline-flex align-items-center" type="search">
                                <i class="ri-search-2-line"></i>
                            </button>
                        </div>
                    </form>
                    <a href="create_ringtone.php?add=yes" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ri-add-line"></i>
                        <span class="ps-1 text-nowrap d-none d-sm-block">Create Ringtone</span>
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <?php if(mysqli_num_rows($result) > 0){ ?>
                    <div class="row g-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Ringtone</th>
                                    <th>Name</th>
                                    <th class="text-center">Status</th>
                                    <th style="width: 150px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=0; while($row=mysqli_fetch_array($result)) { ?>
                                    <tr>
                                        <?php
                                         $file_path = null;
                                          $audio_file=$row['ringtone_url'];
                                          if($row['audio_type']=='local'){
                                            $audio_file=$file_path.'uploads/'.basename($row['ringtone_url']);
                                          } 
                                          ?>
                                        <td><div class="mediPlayer"><audio class="listen" preload="none" data-size="50" src="<?=$audio_file?>"></audio></div></td>
                                
                                        <td><?php echo $row['ringtone_title'];?></td>

                                        <td class="text-center" >
                                            <div class="nsofts-switch enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                                <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-id="<?= $row['id'] ?>" data-table="<?=$tableName ?>" data-column="status" class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                                <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="create_ringtone.php?ringtone_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-icon" style="padding: 10px 10px !important;  margin-right: 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete" data-id="<?php echo $row['id'];?>" data-table="<?=$tableName ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                 <?php $i++; } ?>
                            </tbody>
                        </table>
                    </div>
                     <?php include("pagination.php"); ?>
                <?php } else { ?>
                
                    <ul class="p-5">
                        <h1 class="text-center">No data found</h1>
                    </ul>
                    
                <?php } ?>
                </nav>
            </div>
        </div>

    </div>
</main>
<!-- End: main -->
    
<?php include("includes/footer.php");?> 
<script src="https://code.jquery.com/jquery-1.11.2.min.js"
    integrity="sha256-Ls0pXSlb7AYs7evhd+VLnWsZ/AqEHcXBeMZUycz/CcA="
    crossorigin="anonymous">
</script>
<script type="text/javascript" src="assets/player/player.js"></script> 
<script>
    $(document).ready(function () {
        $('.mediPlayer').mediaPlayer();
    });
</script>