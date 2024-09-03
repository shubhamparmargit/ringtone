<?php $page_title="Manage Reports";
    include("includes/header.php");
    require("includes/lb_helper.php");
    require("language/language.php");
    
    $tableName="tbl_users";   
    $targetpage = "manage_report.php"; 
    $limit = 15; 

    $query = "SELECT COUNT(*) as num FROM $tableName";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
    $total_pages = $total_pages['num'];
    
    $stages = 3;
    $page=0;
    if(isset($_GET['page'])){
        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
    }
    if($page){
        $start = ($page - 1) * $limit; 
    }else{
        $start = 0; 
    } 
    
    $sql_query="SELECT * FROM tbl_users ORDER BY tbl_users.`id` DESC LIMIT $start, $limit"; 
    $result=mysqli_query($mysqli,$sql_query) or die(mysqli_error($mysqli));
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
            
        <div class="card h-100">
            <div class="card-header d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                <a href="<?php echo (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "dashboard.php" ?>" class="d-inline-flex align-items-center text-decoration-none fw-semibold nsofts-link">
                    <i class="ri-arrow-left-line text-danger fw-bold"></i>
                    <span class="ps-2 lh-1"><?=$page_title ?></span>
                </a>
                <span class="ps-2 lh-1">Total Reports (<?=$total_pages ?>)</span>
                
            </div>
            
            <div class="card-body p-4">
                <?php if(mysqli_num_rows($result) > 0){ ?>
                    <div class="row g-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Registered</th>
                                    <th class="text-center">Currect Status</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $i=0; while($row=mysqli_fetch_array($result)) {

                                        $threshold_minutes = 10;
                                        $threshold_time = date('Y-m-d H:i:s', strtotime("-$threshold_minutes minutes"));
                                        $is_online = ($row['last_activity'] >= $threshold_time) ? true : false;

                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center">
                                                <?php if($row['user_type']=='Google'){?>
                                                    <img src="assets/images/google-logo.png" class="social_img" alt="">
                                                <?php }?>
                                                <?php if($row['profile_img']!="" AND file_exists("images/".$row['profile_img'])){?>
                                                    <img src="images/<?php echo $row['profile_img']?>"alt="">
                                                <?php }else{?>
                                                    <img src="assets/images/user_photo.png" alt="">
                                                <?php }?>
                                            </div>
                                        </td>
                                        <td><?php echo $row['user_name'];?></td>
                                        <td><?php echo $row['user_email'];?></td>
                                        <td><?php echo $row['user_phone'];?></td>
                                        <td><?php echo date('d-m-Y',$row['registered_on']);?></td>
                                        <td class="text-center">
                                            <?php if ($is_online) { ?>
                                                <span class="badge bg-success">Online</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Offline</span>
                                            <?php } ?>
                                        </td>
                                        
                                        <td class="text-center" >
                                            <?php if ($row['status'] == 1) { ?>
                                                <span class="badge bg-success">Enable</span>
                                            <?php } else { ?>
                                                <span class="badge bg-danger">Disable</span>
                                            <?php } ?>
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