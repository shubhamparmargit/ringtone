<?php 
include("includes/db_helper.php");
include("includes/lb_helper.php"); 
include("language/api_language.php"); 
include("smtp_email.php");

// error_reporting(0);

$file_path = getBaseUrl();

$mysqli->set_charset('utf8mb4');

date_default_timezone_set("Asia/Colombo");

define("DEFAULT_PASSWORD",'123');
define("PACKAGE_NAME",$settings_details['envato_package_name']);

// For Api header
$API_NAME = 'NEMOSOFTS_APP';

// Purchase code verification
if($settings_details['envato_buyer_name']=='' OR $settings_details['envato_purchase_code']=='' OR $settings_details['envato_api_key']=='') {
    $set[$API_NAME][]=array('MSG'=> 'Purchase code verification failed!','success'=>'0');
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}

// Generate random password
function generateRandomPassword($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function update_activity_log($user_id){
	global $mysqli;
    
    $sql="SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
    $result=mysqli_query($mysqli, $sql);
    
    if(mysqli_num_rows($result) == 0){
        
        $data_log = array('user_id'  =>  $user_id, 'date_time'  =>  strtotime(date('d-m-Y h:i:s A')));
        $qry = Insert('tbl_active_log',$data_log);
    } else {
        
        $data_log = array('date_time'  =>  strtotime(date('d-m-Y h:i:s A')));
        $update=Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");  
    }
    
    mysqli_free_result($result);
}
function send_register_email($to, $recipient_name, $subject, $message){	
	global $file_path;
    global $app_lang;

	$message_body='<div style="background-color: #eee;" align="center"><br />
	<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
	<tbody>
	<tr>
	<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
	</tr>
	<br>
	<br>
	<tr>
	<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
	<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
	</td>
	</tr>
	<tr>
	<td width="600" valign="top" bgcolor="#FFFFFF">
	<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	<tbody>
	<tr>
	<td valign="top">
	<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
	<tbody>
	<tr>
	<td>
	<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.$recipient_name.'</strong></p>
	<br>
	<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$message.'<br /></p>
	<br/>
	<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
	</td>
	</tr>
	</tbody>
	</table>
	</td>
	</tr>
	</tbody>
	</table>
	</td>
	</tr>
	<tr>
	<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
	</tr>
	</tbody>
	</table>
	</div>';

	send_email($to,$recipient_name,$subject,$message_body);
}

// For favorite
function is_favorite($id,$user_id=''){	
 	global $mysqli;
 	$sql="SELECT * FROM tbl_favourite WHERE `post_id`='$id' AND `user_id`='$user_id'";
 	$result=mysqli_query($mysqli, $sql);
 	if(mysqli_num_rows($result) > 0){
 		return true;
 	} else {
 		return false;
 	}
}

$get_helper = get_api_data($_POST['data']);

if($get_helper['helper_name']=="get_home"){
    
    $home_limit = HOME_LIMIT;
    
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;
    $ids=isset($get_helper['ringtone_ids']) ? trim($get_helper['ringtone_ids']) : 0;
    
    $jsonObj= array();
	$data_arr= array();
    
    $sql="SELECT * FROM tbl_banner WHERE tbl_banner.status='1' ORDER BY tbl_banner.bid DESC";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            $data_arr['bid'] = $data['bid'];
            $data_arr['banner_title'] = $data['banner_title'];
            $data_arr['banner_info'] = $data['banner_info'];
            $data_arr['banner_image'] = $file_path.'images/'.$data['banner_image'];
            array_push($jsonObj,$data_arr);
        }
    }
    $row['slider'] = $jsonObj;

	mysqli_free_result($result);
	$jsonObj = array();
	$data_arr = array();
	
	$sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $home_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while ($data2 = mysqli_fetch_assoc($result)){
            
            $data_arr['id'] = $data2['id'];
            $data_arr['user_id'] = $data2['user_id'];
            $data_arr['ringtone_title'] = $data2['ringtone_title'];
            if($data2['audio_type']=="local"){
                $data_arr['ringtone_url'] = $file_path.'uploads/'.$data2['ringtone_url'];
            } else {
                $data_arr['ringtone_url'] = $data2['ringtone_url'];
            }
            $data_arr['cat_id'] = $data2['cat_id'];
            $data_arr['category_name'] = $data2['category_name'];
            $data_arr['rate_avg'] = $data2['rate_avg'];
            $data_arr['total_rate'] = $data2['total_rate'];
            $data_arr['total_views'] = $data2['total_views'];
            $data_arr['total_download'] = $data2['total_download'];
            $data_arr['is_favorite'] = is_favorite($data2['id'],$user_id);
            
    		array_push($jsonObj, $data_arr);
    	}
    }
	$row['latest_ringtone'] = $jsonObj;
	
	mysqli_free_result($result);
	$jsonObj = array();
	$data_arr = array();
	
	$sql_views="SELECT *,DATE_FORMAT(`date`, '%m/%d/%Y') FROM `tbl_ringtone_views` WHERE `date` BETWEEN NOW() - INTERVAL 30 DAY AND NOW() GROUP BY `ringtone_id` ORDER BY views DESC LIMIT 25";
	$res_views = mysqli_query($mysqli, $sql_views) or die(mysqli_error($mysqli));
	if(mysqli_num_rows($res_views) > 0){
	    while ($row_views=mysqli_fetch_assoc($res_views)){
            
            $id = $row_views['ringtone_id'];
            
            $sql="SELECT * FROM tbl_ringtone
            LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
            WHERE tbl_ringtone.`id`='$id' AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC";
            $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
            if(mysqli_num_rows($result) > 0){
                while ($data3 = mysqli_fetch_assoc($result)){
                
                    $data_arr['id'] = $data3['id'];
                    $data_arr['user_id'] = $data3['user_id'];
                    $data_arr['ringtone_title'] = $data3['ringtone_title'];
                    if($data3['audio_type']=="local"){
                        $data_arr['ringtone_url'] = $file_path.'uploads/'.$data3['ringtone_url'];
                    } else {
                        $data_arr['ringtone_url'] = $data3['ringtone_url'];
                    }
                    $data_arr['cat_id'] = $data3['cat_id'];
                    $data_arr['category_name'] = $data3['category_name'];
                    $data_arr['rate_avg'] = $data3['rate_avg'];
                    $data_arr['total_rate'] = $data3['total_rate'];
                    $data_arr['total_views'] = $data3['total_views'];
                    $data_arr['total_download'] = $data3['total_download'];
                    $data_arr['is_favorite'] = is_favorite($data3['id'],$user_id);
                    array_push($jsonObj, $data_arr);
            	}
            }
        }
	}
    $row['trending_ringtone'] = $jsonObj;

	mysqli_free_result($result);
	$jsonObj = array();
	$data_arr = array();
	
	$sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.`id` IN ($ids) AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $home_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while ($data4 = mysqli_fetch_assoc($result)){
            
            $data_arr['id'] = $data4['id'];
            $data_arr['user_id'] = $data4['user_id'];
            $data_arr['ringtone_title'] = $data4['ringtone_title'];
            if($data4['audio_type']=="local"){
                $data_arr['ringtone_url'] = $file_path.'uploads/'.$data4['ringtone_url'];
            } else {
                $data_arr['ringtone_url'] = $data4['ringtone_url'];
            }
            $data_arr['cat_id'] = $data4['cat_id'];
            $data_arr['category_name'] = $data4['category_name'];
            $data_arr['rate_avg'] = $data4['rate_avg'];
            $data_arr['total_rate'] = $data4['total_rate'];
            $data_arr['total_views'] = $data4['total_views'];
            $data_arr['total_download'] = $data4['total_download'];
            $data_arr['is_favorite'] = is_favorite($data4['id'],$user_id);
            
    		array_push($jsonObj, $data_arr);
    	}
    }
	$row['recently_ringtone'] = $jsonObj;

    $set[$API_NAME] = $row;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="get_latest"){
    
    $user_id=isset($get_helper['user_id']) ? $get_helper['user_id'] : 0;
    
    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    $sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
    
}
else if($get_helper['helper_name']=="get_most_viewed"){
    
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;

    $jsonObj = array();
	$data_arr = array();

	$sql_views="SELECT *,DATE_FORMAT(`date`, '%m/%d/%Y') FROM `tbl_ringtone_views` WHERE `date` BETWEEN NOW() - INTERVAL 30 DAY AND NOW() GROUP BY `ringtone_id` ORDER BY views DESC LIMIT 25";
	$res_views = mysqli_query($mysqli, $sql_views) or die(mysqli_error($mysqli));
	if(mysqli_num_rows($res_views) > 0){
	    while ($row_views=mysqli_fetch_assoc($res_views)){
            
            $id = $row_views['ringtone_id'];
            
            $sql="SELECT * FROM tbl_ringtone
            LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
            WHERE tbl_ringtone.`id`='$id' AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC";
            $result = mysqli_query($mysqli, $sql);
            if(mysqli_num_rows($result) > 0){
                while ($data3 = mysqli_fetch_assoc($result)){
                    $data_arr['id'] = $data3['id'];
                    $data_arr['user_id'] = $data3['user_id'];
                    $data_arr['ringtone_title'] = $data3['ringtone_title'];
                    if($data3['audio_type']=="local"){
                        $data_arr['ringtone_url'] = $file_path.'uploads/'.$data3['ringtone_url'];
                    } else {
                        $data_arr['ringtone_url'] = $data3['ringtone_url'];
                    }
                    $data_arr['cat_id'] = $data3['cat_id'];
                    $data_arr['category_name'] = $data3['category_name'];
                    $data_arr['rate_avg'] = $data3['rate_avg'];
                    $data_arr['total_rate'] = $data3['total_rate'];
                    $data_arr['total_views'] = $data3['total_views'];
                    $data_arr['total_download'] = $data3['total_download'];
                    $data_arr['is_favorite'] = is_favorite($data3['id'],$user_id);
                    array_push($jsonObj, $data_arr);
            	}
            }
        }
	}
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="get_recent"){
    
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;
    $ids=trim($get_helper['post_ids']);
    
    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    $sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.`id` IN ($ids) AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            array_push($jsonObj,$row);
        }  
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="cat_list"){
    
    $search_text=addslashes(trim($get_helper['search_text']));
    $search_type=trim($get_helper['search_type']);
    
    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    if($search_type == 'search'){
        $sql="SELECT * FROM tbl_category WHERE tbl_category.status='1' AND tbl_category.`category_name` like '%$search_text%'
        ORDER BY tbl_category.category_name DESC LIMIT $limit, $page_limit";
    } else {
        $sql="SELECT * FROM tbl_category WHERE tbl_category.status='1' ORDER BY tbl_category.cid DESC LIMIT $limit, $page_limit";
    }
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            
            $row['cid'] = $data['cid'];
            $row['category_name'] = $data['category_name'];
            $row['category_image'] = $file_path.'images/'.$data['category_image'];
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="get_cat_list_all"){

    $jsonObj= array();
    
    $sql="SELECT * FROM tbl_category WHERE tbl_category.status='1' ORDER BY tbl_category.cid DESC";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            $row['cid'] = $data['cid'];
            $row['category_name'] = $data['category_name'];
            $row['category_image'] = $file_path.'images/'.$data['category_image'];
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="cat_ringtone"){
    
    $user_id=isset($get_helper['user_id']) ? $get_helper['user_id'] : 0;
    $cat_id=isset($get_helper['cat_id']) ? $get_helper['cat_id'] : 0;
    
    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    $sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.`cat_id`='$cat_id' AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="banner_ringtone"){
    
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;
    $banner_id = $get_helper['banner_id'];
    
    $sql_banner="SELECT * FROM tbl_banner WHERE status='1' AND `bid`='$banner_id' ORDER BY tbl_banner.`bid` DESC";
	$res_banner = mysqli_query($mysqli,$sql_banner);
	$row_banner=mysqli_fetch_assoc($res_banner);

	$ringtone_ids = trim($row_banner['banner_post_id']);

    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    $sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.`id` IN ($ringtone_ids) AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="favourite_post"){
    
    $response=array();
    $jsonObj= array();	
	
	$user_id = cleanInput($get_helper['user_id']);
	$post_id = cleanInput($get_helper['post_id']);

    $sql="SELECT * FROM tbl_favourite WHERE `post_id`='$post_id' AND `user_id`='$user_id'";
    $res = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
	if(mysqli_num_rows($res) == 0){
		
		$data = array(
            'post_id'  =>  $post_id,
            'user_id'  =>  $user_id,
            'type'  =>  'ringtone',
            'created_at'  =>  strtotime(date('d-m-Y h:i:s A')), 
        );
        $qry = Insert('tbl_favourite',$data);
        
        $response=array('MSG' => $app_lang['favourite_success'],'success'=>'1');
	} else {
        
        $deleteSql="DELETE FROM tbl_favourite WHERE `post_id`='$post_id' AND `user_id`='$user_id'";
        if(mysqli_query($mysqli, $deleteSql)){
            $response=array('MSG' => $app_lang['favourite_remove_success'],'success'=>'0');
        } else{
            $response=array('MSG' => $app_lang['favourite_remove_error'],'success'=>'1');
        }
	}
	$set[$API_NAME][]=$response;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="get_favourite"){
    $jsonObj= array();	
    
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;
    
    $page_limit=10;
    $limit=($get_helper['page']-1) * $page_limit;

    $query="SELECT tbl_ringtone.*, tbl_category.* FROM tbl_ringtone
		LEFT JOIN tbl_favourite ON tbl_ringtone.`id`= tbl_favourite.`post_id`
		LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`=tbl_category.`cid` 
		WHERE tbl_ringtone.`status`='1' AND tbl_ringtone.`active`='1' AND tbl_category.`status`='1' AND tbl_favourite.`user_id`='$user_id' ORDER BY tbl_favourite.`id` DESC LIMIT $limit, $page_limit";
	$sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
	if(mysqli_num_rows($sql) > 0){
	    while($data = mysqli_fetch_assoc($sql)){
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            array_push($jsonObj,$row);
    	}
	}
	$set[$API_NAME] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="get_search_ringtone"){
    
    $user_id=isset($get_helper['user_id']) ? $get_helper['user_id'] : 0;
    $search_text = addslashes(trim($get_helper['search_text']));
    
    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    $sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.`ringtone_title` like '%$search_text%' AND tbl_ringtone.status='1' AND tbl_ringtone.`active`=1 AND tbl_category.`status`=1 ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="add_ringtone"){	
    
    $response= array();	
    
	$user_id = cleanInput($get_helper['user_id']);
	$cat_id = cleanInput($get_helper['cat_id']);
	$post_title = cleanInput($get_helper['post_title']);
	
	$status = 0;
	if(AUTO_POST=='true'){
		$status=1;
	}
	
	$is_update = false;
    	
	if($_FILES['data_ringtone']['name']!=""){
	    
	    $path = "uploads/";
        $audio_local=rand(0,99999)."_".str_replace(" ", "-", $_FILES['data_ringtone']['name']);
        $tmp = $_FILES['data_ringtone']['tmp_name'];
        
        if (move_uploaded_file($tmp, $path.$audio_local)) {
            $audio_url = $audio_local;
            $is_update = true;
        }
	}
	
	if($is_update==true){
	    $data = array( 
            'cat_id'  =>  $cat_id,
            'user_id'  =>  $user_id,
            'ringtone_title'  =>  $post_title,
            'audio_type'  =>  'local',
            'ringtone_url'  =>  $audio_url,
            'active' => $status
        );  
        $qry = Insert('tbl_ringtone',$data);
    
        $data_not = array(
            'user_id' => $user_id,
            'notification_title' => 'Update successfully',
            'notification_msg' => $post_title,
            'notification_on' =>  strtotime(date('d-m-Y h:i:s A')) 
        );
        $qry2 = Insert('tbl_notification',$data_not);
        
        $response=array('MSG' => "Upload Success",'success'=>'1');
	} else {
	    $response=array('MSG' => "Error in uploading mp3 file !!",'success'=>'0');
	}
	
    $set[$API_NAME][]=$response;
  	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="get_post_download"){
    
	$post_id=cleanInput($get_helper['post_id']);

	$view_qry=mysqli_query($mysqli,"UPDATE tbl_ringtone SET total_download = total_download + 1 WHERE id = '".$post_id."' AND tbl_ringtone.`status`='1' AND tbl_ringtone.`active`=1");
	
    $set[$API_NAME][]=array('MSG'=> 'done','success'=>'1');
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="get_post_views"){
    
	$view_qry=mysqli_query($mysqli,"UPDATE tbl_ringtone SET total_views = total_views + 1 WHERE id = '".$get_helper['post_id']."' AND tbl_ringtone.`status`='1' AND tbl_ringtone.`active`=1");
	
	$ringtone_id = $get_helper['post_id'];
	$date = date('Y-m-d');
	
	$start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
	$finish = (date('D') != 'Sat') ? date('Y-m-d', strtotime('next Saturday')) : date('Y-m-d');
	
    $query = "SELECT * FROM tbl_ringtone_views WHERE ringtone_id='$ringtone_id' AND date BETWEEN '$start' AND '$finish'";
	$sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
	if (mysqli_num_rows($sql) > 0) {
		
		$query1 = "UPDATE tbl_ringtone_views SET views=views+1 WHERE ringtone_id='$ringtone_id' AND date BETWEEN '$start' AND '$finish'";
		$sql1 = mysqli_query($mysqli, $query1);
		
		$response=array('MSG' => 'update','success'=>'1');
		
	} else {
	    
        $query2 = "SELECT * FROM tbl_ringtone_views WHERE ringtone_id='$ringtone_id'";
        $sql2 = mysqli_query($mysqli, $query2);
        if (mysqli_num_rows($sql2) > 0) {
            $deleteSql = "DELETE FROM tbl_ringtone_views WHERE `ringtone_id` IN ($ringtone_id)";
            mysqli_query($mysqli, $deleteSql);
        }
        $data = array('ringtone_id'  =>  $ringtone_id, 'views'  =>  1, 'date'  =>  $date);
        $qry = Insert('tbl_ringtone_views', $data);
        $response=array('MSG' => 'added','success'=>'1');
	}
	$set[$API_NAME][]=$response;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="get_user_by"){
    
    $user_id=isset($get_helper['user_id']) ? $get_helper['user_id'] : 0;
    
    $jsonObj= array();
    
    $page_limit=15;
    $limit=($get_helper['page']-1) * $page_limit;
    
    $sql="SELECT * FROM tbl_ringtone
        LEFT JOIN tbl_category ON tbl_ringtone.`cat_id`= tbl_category.`cid` 
        WHERE tbl_ringtone.`user_id`='".$user_id."' ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($result) > 0){
        while($data = mysqli_fetch_assoc($result)){
            
            $row['id'] = $data['id'];
            $row['user_id'] = $data['user_id'];
            $row['ringtone_title'] = $data['ringtone_title'];
            if($data['audio_type']=="local"){
                $row['ringtone_url'] = $file_path.'uploads/'.$data['ringtone_url'];
            } else {
                $row['ringtone_url'] = $data['ringtone_url'];
            }
            $row['cat_id'] = $data['cat_id'];
            $row['category_name'] = $data['category_name'];
            $row['rate_avg'] = $data['rate_avg'];
            $row['total_rate'] = $data['total_rate'];
            $row['total_views'] = $data['total_views'];
            $row['total_download'] = $data['total_download'];
            $row['is_favorite'] = is_favorite($data['id'],$user_id);
            
            $row['active'] = $data['active'];
            $row['status'] = $data['status'];
            
            array_push($jsonObj,$row);
        }
    }
    $set[$API_NAME] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
    
}
else if($get_helper['helper_name']=="post_rating"){
    
    $jsonObj= array();	
    
    $post_id = cleanInput($get_helper['post_id']);
    $device_id = cleanInput($get_helper['device_id']);
    $therate = cleanInput($get_helper['rate']);
    $message = cleanInput($get_helper['message']);
    
    $result = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE `post_id` = '$post_id' AND `device_id` = '$device_id'");
    
    if(mysqli_num_rows($result) == 0){
        
        $data = array(   
            'post_id' => $post_id,
            'device_id' => $device_id,
            'rate' => $therate,
            'message' => addslashes($message)
        );  
        $qry = Insert('tbl_rating',$data); 
        
        $query = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE `post_id` = '$post_id'");
        
        while($data = mysqli_fetch_assoc($query)){
            $rate_db[] = $data;
            $sum_rates[] = $data['rate'];
        }
        
        if(@count($rate_db)){
            $rate_times = count($rate_db);
            $sum_rates = array_sum($sum_rates);
            $rate_value = $sum_rates/$rate_times;
            $rate_bg = (($rate_value)/5)*100;
        } else {
            $rate_times = 0;
            $rate_value = 0;
            $rate_bg = 0;
        }
        
        $rate_avg=round($rate_value); 
        
        $sql="UPDATE tbl_ringtone SET `total_rate` = `total_rate` + 1, `rate_avg` = '$rate_avg' where id='$post_id'";
        mysqli_query($mysqli,$sql);
        
        $total_rat_sql="SELECT * FROM tbl_ringtone WHERE id='".$post_id."'";
        $total_rat_res=mysqli_query($mysqli,$total_rat_sql);
        $total_rat_row=mysqli_fetch_assoc($total_rat_res);
        
        $jsonObj = array('total_rate' => $total_rat_row['total_rate'],'rate_avg' => $total_rat_row['rate_avg'],'MSG' => $app_lang['rate_success'],'success'=> '1');
    } else {
        $jsonObj = array('MSG' => $app_lang['rate_already'],'success'=> '0');
    }
    $set[$API_NAME][] = $jsonObj;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="get_rating"){
    
    $jsonObj= array();	
    
    $post_id = cleanInput($get_helper['post_id']);
	$device_id = cleanInput($get_helper['device_id']);
	
	$result = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE `post_id` = '$post_id' AND `device_id` = '$device_id'"); 
    if(mysqli_num_rows($result) > 0){
		$data = mysqli_fetch_assoc($result);
		$jsonObj = array( 'total_rate' => $data['rate'] , 'message' => $data['message']);	
	} else {
		$jsonObj = array( 'total_rate' => 0, 'message' => '');
	}
	
	$set[$API_NAME][] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="post_report"){
    
    $jsonObj= array();
    $post_id=cleanInput($get_helper['post_id']);
	$user_id=cleanInput($get_helper['user_id']);
	$report_title=cleanInput($get_helper['report_title']);
	$report_msg=cleanInput($get_helper['report_msg']);
    
	$data = array(
        'post_id'  =>  $post_id,
        'user_id'  =>  $user_id,
        'report_title'  =>  $report_title,
        'report_msg'  =>  $report_msg,
        'report_on'  =>  strtotime(date('d-m-Y h:i:s A')), 
    );
    $qry = Insert('tbl_reports',$data);
    
    $data_not = array(
        'user_id' => $user_id,
        'notification_title' => 'Report successful',
        'notification_msg' => $report_msg,
        'notification_on' =>  strtotime(date('d-m-Y h:i:s A')) 
    );
    
    $qry2 = Insert('tbl_notification',$data_not);
    
	$set[$API_NAME][]=array('MSG'=> $app_lang['report_success'],'success'=> '1');
  	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
// User
else if($get_helper['helper_name']=="user_register"){
    
    $user_type=isset($get_helper['type']) ? trim($get_helper['type']) : '';

    $email=isset($get_helper['user_email']) ? addslashes(trim($get_helper['user_email'])) : '';
    $auth_id=isset($get_helper['auth_id']) ? addslashes(trim($get_helper['auth_id'])) : '';

    $to=isset($get_helper['user_email']) ? $get_helper['user_email'] : '';
    $recipient_name=isset($get_helper['user_name']) ? $get_helper['user_name'] : '';

	$subject = str_replace('###', APP_NAME, $app_lang['register_mail_lbl']);

	$response=array();

	$user_id='';
	
    switch ($user_type) {
        case 'Google':
        {
            $sql="SELECT * FROM tbl_users WHERE (`user_email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Google'";
            $res = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
            if(mysqli_num_rows($res) == 0){
                
                $data = [
                    'user_type'=>'Google',
                    'user_name' => addslashes(trim($get_helper['user_name'])),
                    'user_email' => addslashes(trim($get_helper['user_email'])),
                    'user_phone' => '',
                    'user_password' => md5(DEFAULT_PASSWORD),
                    'user_gender'  => '',
                    'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
                    'auth_id' => $auth_id,
                    'profile_img' => '',
                    'status'  =>  '1'
                ];
                
                $qry = Insert('tbl_users',$data);
                
                $user_id=mysqli_insert_id($mysqli);
                send_register_email($to, $recipient_name, $subject, $app_lang['google_register_msg']);
                
                // login success
                $response = array('user_id' =>  strval($user_id), 'user_name'=> $get_helper['user_name'], 'user_email'=> $get_helper['user_email'], 'user_phone'=> '', 'user_gender'=> '', 'profile_img'=> '', 'auth_id' => $auth_id, 'MSG' => $app_lang['login_success'], 'success'=>'1');
            }
            else {
                
                $row = mysqli_fetch_assoc($res);
                
                $data = array('auth_id'  =>  $auth_id); 
                $update=Update('tbl_users', $data, "WHERE id = '".$row['id']."'");
                
                $user_id=$row['id'];
                
                if($row['status']==0){
                    $response=array('msg' =>$app_lang['account_deactive'],'success'=>'0');
                } else {
                    $response = array('user_id' =>  $row['id'],'user_name'=> $row['user_name'],'user_email'=> $row['user_email'],'user_phone'=> $row['user_phone'],'user_gender'=> $row['user_gender'],'profile_img'=> $row['profile_img'],'auth_id' => $auth_id,'MSG' => $app_lang['login_success'],'success'=> '1');
                }
            }
            update_activity_log($user_id);
        }
        break;
        case 'Normal':
        {
            $sql = "SELECT * FROM tbl_users WHERE user_email = '$email'"; 
            $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
            $row = mysqli_fetch_assoc($result);
            
            if (!filter_var($get_helper['user_email'], FILTER_VALIDATE_EMAIL)) {
                $response=array('MSG' => $app_lang['invalid_email_format'],'success'=>'0');
            }
            else if($row['user_email']!="") {
                $response=array('MSG' => $app_lang['email_exist'],'success'=>'0');
            }
            else {
                
                if($_FILES['image_data']['name']!="") {
                    
                    $imgName=rand(0,99999)."_".$_FILES['image_data']['name'];
                    //Main Image
                    $tpath1='images/'.$imgName;        
                    $pic1=compress_image($_FILES["image_data"]["tmp_name"], $tpath1, 80);
                    
                } else {
                    $imgName = '';
                }
                
                $data = [
                    'user_name' => addslashes(trim($get_helper['user_name'])),
                    'user_email' => addslashes(trim($get_helper['user_email'])),
                    'user_phone' => addslashes(trim($get_helper['user_phone'])),
                    'user_password' => md5(trim($get_helper['user_password'])),
                    'user_gender'  => addslashes(trim($get_helper['user_gender'])),
                    'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
                    'profile_img' => $imgName,
                    'status'  =>  '1'
                ];
                
                $qry = Insert('tbl_users',$data);
                
                $user_id=mysqli_insert_id($mysqli);
                
                send_register_email($to, $recipient_name, $subject, $app_lang['normal_register_msg']);
                
                $response=array('MSG' => $app_lang['register_success'],'success'=>'1');
                
                update_activity_log($user_id);
            }
        }
        break;
        default:
        {
            $response=array('success'=>'0', 'MSG' =>$app_lang['register_fail']);
        }
        break;
    }
	$set[$API_NAME][]=$response;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="user_login"){
    
    $response=array();

    $email=isset($get_helper['user_email']) ? trim($get_helper['user_email']) : '';
    $password=isset($get_helper['user_password']) ? trim($get_helper['user_password']) : '';
    $auth_id=isset($get_helper['auth_id']) ? trim($get_helper['auth_id']) : '';
    $user_type=isset($get_helper['type']) ? trim($get_helper['type']) : '';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) AND $email!='') {
        $response=array('MSG' => $app_lang['invalid_email_format'],'success'=>'0');
        $set[$API_NAME][]=$response;
        header( 'Content-Type: application/json; charset=utf-8' );
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }
    
    switch ($user_type) {
        case 'Google':
        {
            $sql = "SELECT * FROM tbl_users WHERE (`user_email` = '$email' OR `auth_id`='$auth_id') AND (`user_type`='Google' OR `user_type`='google')";
            $res = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
            if(mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                if($row['status']==0) {
                    $response=array('MSG' => $app_lang['account_deactive'],'success'=>'0');
                } else {
                    $user_id=$row['id'];
                    
                    update_activity_log($user_id);
                    
                    $data = array('auth_id'  =>  $auth_id);  
                    
                    Update('tbl_users', $data, "WHERE `id` = ".$row['id']);
                    
                    $response = array('user_id' =>  $row['id'],'user_name'=> $row['user_name'],'user_phone'=> $row['user_phone'],'user_gender'=> $row['user_gender'],'profile_img'=> $file_path.'images/'.$row['profile_img'],'MSG' => $app_lang['login_success'],'success'=>'1');
                }
            }
            else {
                $response=array('MSG' => $app_lang['email_not_found'],'success'=>'0');
            }
        }
        break;
        case 'Normal':
        {
            $qry = "SELECT * FROM tbl_users WHERE user_email = '$email' AND (`user_type`='Normal' OR `user_type`='normal') AND `id` <> 0"; 
            $result = mysqli_query($mysqli, $qry) or die(mysqli_error($mysqli));
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $row = mysqli_fetch_assoc($result);
                if($row['status']==1) {
                    if($row['user_password']==md5($password)) {
                        
                        $user_id=$row['id'];
                        
                        update_activity_log($user_id);
                        
                        $response = array('user_id' =>  $row['id'],'user_name'=> $row['user_name'],'user_phone'=> $row['user_phone'],'user_gender'=> $row['user_gender'],'profile_img'=> $file_path.'images/'.$row['profile_img'],'MSG' => $app_lang['login_success'],'success'=>'1'); 
                    }
                    else{
                        $response=array('MSG' =>$app_lang['invalid_password'],'success'=>'0');
                    }
                }
                else {
                    $response=array('MSG' =>$app_lang['account_deactive'],'success'=>'0');
                }
            }
            else {
                $response=array('MSG' =>$app_lang['email_not_found'],'success'=>'0');	
            }
        }
        break;
        default:
        {
            $response=array('success'=>'0', 'MSG' =>$app_lang['register_fail']);
        }
        break;
    }
    $set[$API_NAME][]=$response;
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name'] == "user_profile") {
	$jsonObj= array();	
	
	$user_id=cleanInput($get_helper['user_id']);

	$qry = "SELECT * FROM tbl_users WHERE id = '$user_id'"; 
	$result = mysqli_query($mysqli, $qry) or die(mysqli_error($mysqli));
	$row = mysqli_fetch_assoc($result);	
	
	$data['success']="1";
	$data['user_id'] = $row['id'];
	$data['user_name'] = $row['user_name'];
	$data['user_email'] = ($row['user_email']!='') ? $row['user_email'] : '';
	$data['user_phone'] = ($row['user_phone']!='') ? $row['user_phone'] : '';
	$data['user_gender'] = $row['user_gender'];
	$data['profile_img'] = $file_path.'images/'.$row['profile_img'];

	array_push($jsonObj,$data);

    $set[$API_NAME] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();
}
else if($get_helper['helper_name']=="edit_profile"){
    
    $jsonObj= array();	
	
	$qry = "SELECT * FROM tbl_users WHERE id = '".$get_helper['user_id']."'"; 
	$result = mysqli_query($mysqli, $qry) or die(mysqli_error($mysqli));
	$row = mysqli_fetch_assoc($result);
  
  	if (!filter_var($get_helper['user_email'], FILTER_VALIDATE_EMAIL)) {
  	    $set[$API_NAME][]=array('MSG' => $app_lang['invalid_user_type'],'success'=>'0');
	}
	else if($row['user_email']==$get_helper['user_email'] AND $row['id']!=$get_helper['user_id']) {
        $set[$API_NAME][]=array('MSG' => $app_lang['email_not_found'],'success'=>'0');
	} 
	else {
        $data = array(
            'user_name'  =>  cleanInput($get_helper['user_name']),
            'user_email'  =>  trim($get_helper['user_email']),
            'user_phone'  =>  cleanInput($get_helper['user_phone']),
		);
		
		if($get_helper['user_password']!=""){
			$data = array_merge($data, array("user_password" => md5(trim($get_helper['user_password']))));
		}
		
		$user_edit=Update('tbl_users', $data, "WHERE id = '".$get_helper['user_id']."'");
		
		$set[$API_NAME][] = array('MSG' => $app_lang['update_success'], 'success' => '1');
	}
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="user_images_update"){	
    
	if($_FILES['image_data']['name']!="") {
	    
		$image_data=rand(0,99999)."_".$_FILES['image_data']['name'];
        //Main Image
        $tpath1='images/'.$image_data;        
        $pic1=compress_image_app($_FILES["image_data"]["tmp_name"], $tpath1, 80);
        
        $data = array('profile_img'  =>  $image_data);
        $user_update =Update('tbl_users', $data, "WHERE id = '".$get_helper['user_id']."'");
        
        $set[$API_NAME][]=array('MSG'=> $app_lang['update_success'],'success' => '1');
	} else {
        $set[$API_NAME][]=array('MSG' => "Update error",'success' => '0');
	}
  	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="account_delete"){
    
    $ids = cleanInput($get_helper['user_id']);
    
    $sql="SELECT * FROM tbl_users WHERE `id`='$ids'";
    $res=mysqli_query($mysqli, $sql);
    if(mysqli_num_rows($res) > 0) {
        
        $sql_img="SELECT * FROM tbl_users WHERE `id` IN ($ids)";
        $res_img=mysqli_query($mysqli, $sql_img);
        while ($row=mysqli_fetch_assoc($res_img)) {
            if($row['profile_img']!="") {
                unlink('images/'.$row['profile_img']);
            }
        }
        
        $deleteSql = "DELETE FROM tbl_active_log WHERE `user_id` IN ($ids)";
        mysqli_query($mysqli, $deleteSql);
        
        $deleteSql="DELETE FROM tbl_notification WHERE `user_id` IN ($ids)";
        mysqli_query($mysqli, $deleteSql);
        
        $deleteSql = "DELETE FROM tbl_users WHERE `id` IN ($ids)";
        mysqli_query($mysqli, $deleteSql);
        
        $set[$API_NAME][]=array('MSG'=> "Remove success",'success'=> '1');
    } else {
        $set[$API_NAME][]=array('MSG'=> 'Remove error','success'=> '0');
    }
    header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if($get_helper['helper_name']=="forgot_pass"){
    
    $email=addslashes(trim($get_helper['user_email']));

	$qry = "SELECT * FROM tbl_users WHERE user_email = '$email' AND `user_type`='Normal' AND `id` <> 0"; 
	$result = mysqli_query($mysqli, $qry) or die(mysqli_error($mysqli));
	$row = mysqli_fetch_assoc($result);
	
	if($row['user_email']!="") {
		$password=generateRandomPassword(7);
		
		$new_password=md5($password);
		$to = $row['user_email'];
		$recipient_name=$row['user_name'];
		
		// subject
		$subject = str_replace('###', APP_NAME, $app_lang['forgot_password_sub_lbl']);
		$message='<div style="background-color: #f9f9f9;" align="center"><br />
				  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
				    <tbody>
				      <tr>
				        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" style="width:100px;height:auto"/></td>
				      </tr>
				      <tr>
				        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
				          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
				            <tbody>
				              <tr>
				                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
				                    <tbody>
				                      <tr>
				                        <td>
				                          <p style="color: #262626; font-size: 24px; margin-top:0px;"><strong>'.$app_lang['dear_lbl'].' '.$row['user_name'].'</strong></p>
				                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-top:5px;"><br>'.$app_lang['your_password_lbl'].': <span style="font-weight:400;">'.$password.'</span></p>
				                          <p style="color:#262626; font-size:17px; line-height:32px;font-weight:500;margin-bottom:30px;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
				                          
				                        </td>
				                      </tr>
				                    </tbody>
				                  </table></td>
				              </tr>
				               
				            </tbody>
				          </table></td>
				      </tr>
				      <tr>
				        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
				      </tr>
				    </tbody>
				  </table>
				</div>';
				
		send_email($to,$recipient_name,$subject,$message);
		
		$sql="UPDATE tbl_users SET `user_password`='$new_password' WHERE `id`='".$row['id']."'";
      	mysqli_query($mysqli,$sql);
		 	  
		$set[$API_NAME][]=array('MSG' => $app_lang['password_sent_mail'],'success'=>'1');
	}
	else {  	 
		$set[$API_NAME][]=array('MSG' => $app_lang['email_not_found'],'success'=>'0');
	}
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();
}
else if($get_helper['helper_name']=="get_notification") {
    
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;

    $jsonObj= array();
    
	$page_limit=50;
	$limit=($get_helper['page']-1) * $page_limit;

    $query="SELECT * FROM tbl_notification WHERE `user_id`='$user_id' ORDER BY tbl_notification.`id` DESC LIMIT $limit, $page_limit"; 
	$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));
	if(mysqli_num_rows($sql) > 0){
	    while($data = mysqli_fetch_assoc($sql)){
    		$row['id'] = $data['id'];
          	$row['notification_title'] = $data['notification_title'];
          	$row['notification_msg'] = $data['notification_msg']; 
    		$row['notification_on'] = calculate_time_span($data['notification_on'],true);		 
    		array_push($jsonObj,$row);
    	}
	}
	$set[$API_NAME] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
else if($get_helper['helper_name']=="remove_notification"){
    
    $post_id=isset($get_helper['post_id']) ? cleanInput($get_helper['post_id']) : 0;
    $user_id=isset($get_helper['user_id']) ? cleanInput($get_helper['user_id']) : 0;

	$jsonObj= array();
	
	$sql="SELECT * FROM tbl_notification WHERE `id`='$post_id' AND `user_id`='$user_id'";
	$res = mysqli_query($mysqli,$sql)or die(mysqli_error($mysqli));
	if(mysqli_num_rows($res) > 0) {
		$deleteSql="DELETE FROM tbl_notification WHERE `id`='$post_id' AND `user_id`='$user_id'";
		mysqli_query($mysqli, $deleteSql);
		
        $set[$API_NAME][]=array('MSG'=> $app_lang['remove_success'],'success'=> '1');
	} else {
	    $set[$API_NAME][]=array('MSG'=> $app_lang['like_remove_error'],'success'=> '0');
	}
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}
// App details
else if($get_helper['helper_name']=="app_details"){

    $jsonObj= array();	
	$query="SELECT * FROM tbl_settings WHERE id='1'";
	$sql = mysqli_query($mysqli,$query);
	while($data = mysqli_fetch_assoc($sql)){
	    
        // App Details ---------------------------------------------------------
        $row['app_email'] = $data['app_email'];
        $row['app_author'] = $data['app_author'];
        $row['app_contact'] = $data['app_contact'];
        $row['app_website'] = $data['app_website'];
        $row['app_description'] = $data['app_description'];
        $row['app_developed_by'] = $data['app_developed_by'];
        
        // Envato --------------------------------------------------------------
        $row['envato_api_key'] = $data['envato_api_key'];
        
        // API Latest Limit ----------------------------------------------------
        $row['api_latest_limit'] = $data['api_latest_limit'];
        
        // is ------------------------------------------------------------------
        $row['isRTL'] = $data['isRTL'];
        $row['isMaintenance'] = $data['isMaintenance'];
        $row['isScreenshot'] = $data['isScreenshot'];
        $row['isGoogleLogin'] = $data['isGoogleLogin'];
        $row['isLogin'] = $data['isLogin'];
        $row['isAPK'] = $data['isAPK'];
        $row['isVPN'] = $data['isVPN'];
        
        $row['isDummy_1'] = $data['isDummy_1'];
        $row['isDummy_2'] = $data['isDummy_2'];
        $row['dummy_test_1'] = $data['dummy_test_1'];
        $row['dummy_test_2'] = $data['dummy_test_2'];
       
        // AppUpdate -----------------------------------------------------------
        $row['app_update_status'] = $data['app_update_status'];
        $row['app_new_version'] = $data['app_new_version'];
        $row['app_update_desc'] = $data['app_update_desc'];
        $row['app_redirect_url'] = $data['app_redirect_url'];
        
        // More Apps -----------------------------------------------------------
        $row['more_apps_url'] = $data['more_apps_url'];
        
        // Ads Network ---------------------------------------------------------
        $row['ad_status'] = $data['ad_status'];
        // PRIMARY ADS
        $row['ad_network'] = $data['ad_network'];
        // admob
        $row['admob_publisher_id'] = $data['admob_publisher_id'];
        $row['admob_banner_unit_id'] = $data['admob_banner_unit_id'];
        $row['admob_interstitial_unit_id'] = $data['admob_interstitial_unit_id'];
        $row['admob_native_unit_id'] = $data['admob_native_unit_id'];
        $row['admob_app_open_ad_unit_id'] = $data['admob_app_open_ad_unit_id'];
        // startapp
        $row['startapp_app_id'] = $data['startapp_app_id'];
        // unity
        $row['unity_game_id'] = $data['unity_game_id'];
        $row['unity_banner_placement_id'] = $data['unity_banner_placement_id'];
        $row['unity_interstitial_placement_id'] = $data['unity_interstitial_placement_id'];
        // applovin max
        $row['applovin_banner_ad_unit_id'] = $data['applovin_banner_ad_unit_id'];
        $row['applovin_interstitial_ad_unit_id'] = $data['applovin_interstitial_ad_unit_id'];
        $row['applovin_native_ad_manual_unit_id'] = $data['applovin_native_ad_manual_unit_id'];
        $row['applovin_app_open_ad_unit_id'] = $data['applovin_app_open_ad_unit_id'];
        // ironsource
        $row['ironsource_app_key'] = $data['ironsource_app_key'];
        // Meta Audience Network
        $row['mata_banner_ad_unit_id'] = $data['mata_banner_ad_unit_id'];
        $row['mata_interstitial_ad_unit_id'] = $data['mata_interstitial_ad_unit_id'];
        $row['mata_native_ad_manual_unit_id'] = $data['mata_native_ad_manual_unit_id'];
        // yandex
        $row['yandex_banner_ad_unit_id'] = $data['yandex_banner_ad_unit_id'];
        $row['yandex_interstitial_ad_unit_id'] = $data['yandex_interstitial_ad_unit_id'];
        $row['yandex_native_ad_manual_unit_id'] = $data['yandex_native_ad_manual_unit_id'];
        $row['yandex_app_open_ad_unit_id'] = $data['yandex_app_open_ad_unit_id'];
        // wortise
        $row['wortise_app_id'] = $data['wortise_app_id'];
        $row['wortise_banner_unit_id'] = $data['wortise_banner_unit_id'];
        $row['wortise_interstitial_unit_id'] = $data['wortise_interstitial_unit_id'];
        $row['wortise_native_unit_id'] = $data['wortise_native_unit_id'];
        $row['wortise_app_open_unit_id'] = $data['wortise_app_open_unit_id'];
        
        // ADS PLACEMENT
        $row['app_open_ad_on_start'] = $data['app_open_ad_on_start'];
        $row['app_open_ad_on_Resume'] = $data['app_open_ad_on_Resume'];
        $row['banner_home'] = $data['banner_home'];
        $row['banner_post_details'] = $data['banner_post_details'];
        $row['banner_category_details'] = $data['banner_category_details'];
        $row['banner_search'] = $data['banner_search'];
        $row['interstitial_post_list'] = $data['interstitial_post_list'];
        $row['native_ad_post_list'] = $data['native_ad_post_list'];
        $row['native_ad_category_list'] = $data['native_ad_category_list'];
        
        // GLOBAL CONFIGURATION
        $row['interstital_ad_click'] = $data['interstital_ad_click'];
        $row['native_position'] = $data['native_position'];
        
        array_push($jsonObj,$row);
    }
	$set[$API_NAME] = $jsonObj;
	header( 'Content-Type: application/json; charset=utf-8' );
    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();
}else if ($get_helper['helper_name'] == "generate_otp") {
    $response = array();

    $phone_number = isset($get_helper['phone_number']) ? trim($get_helper['phone_number']) : '';

    if (!preg_match('/^\\d{1,15}$/', $phone_number)) { // Basic validation for international phone number format
        $response = array('MSG' => 'Invalid phone number format', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $otp = generateOtp();
    // sendOtpSms($phone_number, $otp);

    // Save OTP to session or database
    $_SESSION['otp'] = $otp; // For simplicity, using session here
    $_SESSION['phone_number'] = $phone_number;


    $response = array('MSG' => 'OTP sent successfully', 'success' => '1');
    $set[$API_NAME][] = $response;
    header('Content-Type: application/json; charset=utf-8');
    echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}else if ($get_helper['helper_name'] == "login") {
    $response = array();

    $phone_number = isset($get_helper['phone_number']) ? trim($get_helper['phone_number']) : '';
    $otp = isset($get_helper['otp']) ? trim($get_helper['otp']) : '';

    if (!preg_match('/^\\d{1,15}$/', $phone_number)) {
        $response = array('MSG' => 'Invalid phone number format', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }
    if (!isset($_SESSION['otp']) || $_SESSION['otp'] != $otp || $_SESSION['phone_number'] != $phone_number) {        
        $response = array('MSG' => 'Invalid OTP', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $sql = "SELECT * FROM tbl_users WHERE user_phone = '$phone_number'";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['status'] == 1) {
            $response = array(
                'user_id' => $row['id'],
                'user_name' => $row['user_name'],
                'user_phone' => $row['user_phone'],
                'user_gender' => $row['user_gender'],
                'profile_img' => $file_path . 'images/' . $row['profile_img'],
                'MSG' => 'Login successful',
                'success' => '1'
            );
            update_user_last_login($row['id'], $mysqli);
        } else {
            $response = array('MSG' => 'Account deactivated', 'success' => '0');
        }
    } else {
        $response = array('MSG' => 'Phone number not found', 'success' => '0');
    }

    $set[$API_NAME][] = $response;
    header('Content-Type: application/json; charset=utf-8');
    echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}else if ($get_helper['helper_name'] == "verify_otp") {
    $response = array();

    $phone_number = isset($get_helper['phone_number']) ? trim($get_helper['phone_number']) : '';
    $otp = isset($get_helper['otp']) ? trim($get_helper['otp']) : '';

    if (!preg_match('/^\\d{1,15}$/', $phone_number)) {
        $response = array('MSG' => 'Invalid phone number format', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }
    if (!isset($_SESSION['otp']) || $_SESSION['otp'] != $otp || $_SESSION['phone_number'] != $phone_number) {        
        $response = array('MSG' => 'Invalid OTP', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $response = array('MSG' => 'OTP verified successfully', 'success' => '1');

    $set[$API_NAME][] = $response;
    header('Content-Type: application/json; charset=utf-8');
    echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}else if($get_helper['helper_name']=="get_ringtones"){
    
    $response = array();

    $user_id = isset($get_helper['user_id']) ? intval($get_helper['user_id']) : 0;
    $page = isset($get_helper['page']) ? intval($get_helper['page']) : 1;
    $page_limit = isset($get_helper['page_limit']) ? intval($get_helper['page_limit']) : 15;
    $is_hyped = isset($get_helper['is_hyped']) ? boolval($get_helper['is_hyped']) : false;

    
    // Validate page number and page limit
    if ($page < 1 || $page_limit < 1) {
        $response = array('MSG' => 'Invalid page number or page limit', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $city_sql = "SELECT city_id FROM tbl_users WHERE id = $user_id";
    $city_result = mysqli_query($mysqli, $city_sql);
    $city_data = mysqli_fetch_assoc($city_result);
    $city_id = isset($city_data['city_id']) ? intval($city_data['city_id']) : 0;


    $limit = ($page - 1) * $page_limit;

    $sql = "SELECT tbl_ringtone.*, tbl_category.category_name
            FROM tbl_ringtone
            LEFT JOIN tbl_category ON tbl_ringtone.cat_id = tbl_category.cid
            WHERE tbl_ringtone.status = '1'
            AND tbl_ringtone.active = 1
            AND tbl_category.status = 1";


    if ($city_id > 0) {
        $sql .= " AND (tbl_ringtone.city_id = $city_id OR tbl_ringtone.is_all = 1)";
    } else {
        $sql .= " AND tbl_ringtone.is_all = 1";
    }

    if ($is_hyped) {
        $sql .= " AND tbl_ringtone.is_hyped = 1";
    }

    $sql .= " ORDER BY tbl_ringtone.id DESC LIMIT $limit, $page_limit";
    
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        $response = array('MSG' => 'Database query failed', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $jsonObj = array();

    while ($data = mysqli_fetch_assoc($result)) {
        $row = array();
        $row['id'] = $data['id'];
        $row['user_id'] = $data['user_id'];
        $row['ringtone_title'] = $data['ringtone_title'];
        $row['ringtone_url'] = ($data['audio_type'] == "local") 
            ? $file_path . 'uploads/' . $data['ringtone_url']
            : $data['ringtone_url'];
        $row['cat_id'] = $data['cat_id'];
        $row['category_name'] = $data['category_name'];
        $row['rate_avg'] = $data['rate_avg'];
        $row['total_rate'] = $data['total_rate'];
        $row['total_views'] = $data['total_views'];
        $row['total_download'] = $data['total_download'];
        $row['is_favorite'] = is_favorite($data['id'], $user_id);
        $row['is_hyped'] = $data['is_hyped'];
        $row['is_all'] = $data['is_all'];
        $row['state_id'] = $data['state_id'];
        $row['city_id'] = $data['city_id'];
        $row['play_times'] = $data['play_times'];
        $row['created_at'] = $data['created_at'];
        
        $jsonObj[] = $row;
    }

    $set[$API_NAME] = $jsonObj;
    header('Content-Type: application/json; charset=utf-8');
    echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();  
}else if ($get_helper['helper_name'] == "signup") {

    $email = isset($get_helper['user_email']) ? addslashes(trim($get_helper['user_email'])) : '';
    $phone = isset($get_helper['user_phone']) ? addslashes(trim($get_helper['user_phone'])) : '';
    $otp = isset($get_helper['otp']) ? trim($get_helper['otp']) : '';
    $to = $email;
    $recipient_name = isset($get_helper['user_name']) ? $get_helper['user_name'] : '';
    $subject = str_replace('###', APP_NAME, $app_lang['register_mail_lbl']);
    $state_id = isset($get_helper['state_id']) ? trim($get_helper['state_id']) : '';
    $city_id = isset($get_helper['city_id']) ? trim($get_helper['city_id']) : '';
    $response = array();

    // $sql = "SELECT * FROM tbl_users WHERE user_email = '$email'";
    // $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    // $row = mysqli_fetch_assoc($result);

    // Check if the phone number already exists
    $sqlPhone = "SELECT * FROM tbl_users WHERE user_phone = '$phone'";
    $resultPhone = mysqli_query($mysqli, $sqlPhone) or die(mysqli_error($mysqli));
    $rowPhone = mysqli_fetch_assoc($resultPhone);

    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //     $response = array('MSG' => $app_lang['invalid_email_format'], 'success' => '0');
    // } 
    // else if ($row['user_email'] != "") {
    //     $response = array('MSG' => $app_lang['email_exist'], 'success' => '0');
    // } 
    if (isset($rowPhone['user_phone']) && $rowPhone['user_phone'] != "") {
        $response = array('MSG' => 'Phone number already registered', 'success' => '0');
    }
    else if (!isset($_SESSION['otp']) || $_SESSION['otp'] != $otp) {
        $response = array('MSG' => 'Invalid OTP', 'success' => '0');
    }
    else {
        $imgName = '';
        if (isset($_FILES['image_data']) && $_FILES['image_data']['name'] != "") {
            $imgName = rand(0, 99999) . "_" . $_FILES['image_data']['name'];
            $tpath1 = 'images/' . $imgName;
            $pic1 = compress_image($_FILES["image_data"]["tmp_name"], $tpath1, 80);
        }

        // Insert user data into the database
        $data = [
            'user_type' => 'Normal',
            'user_name' => addslashes(trim($get_helper['user_name'])),
            // 'user_email' => $email,
            'user_phone' => $phone,
            // 'user_password' => md5(trim($get_helper['user_password'])),
            'user_gender' => addslashes(trim($get_helper['user_gender'])),
            'profile_img' => $imgName,
            'registered_on' => strtotime(date('d-m-Y h:i:s A')),
            'last_activity' => date('Y-m-d H:i:s'),
            'status' => '1',
            'state_id' => $state_id,
            'city_id' => $city_id,
        ];

        $qry = Insert('tbl_users', $data);
        $user_id = mysqli_insert_id($mysqli);

        send_register_email($to, $recipient_name, $subject, $app_lang['normal_register_msg']);

         // Fetch the inserted user's details
        $sql = "SELECT * FROM tbl_users WHERE id = '$user_id'";
        $result = mysqli_query($mysqli, $sql);
        $row = mysqli_fetch_assoc($result);

        // Prepare the response with the user's data
        $response = array(
            'user_id' => $row['id'],
            'user_name' => $row['user_name'],
            'user_phone' => $row['user_phone'],
            'user_gender' => $row['user_gender'],
            'profile_img' => $file_path . 'images/' . $row['profile_img'],
            'MSG' => $app_lang['register_success'],
            'success' => '1'
        );

        $response = array('MSG' => $app_lang['register_success'], 'success' => '1');
        update_activity_log($user_id);
    }

    $set[$API_NAME][] = $response;
    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}else if ($get_helper['helper_name'] == "set_random_ringtone") {

    $user_id = isset($get_helper['user_id']) ? intval($get_helper['user_id']) : 0;
    $response = array();

    if ($user_id <= 0) {
        $response = array('MSG' => 'Invalid user ID', 'success' => '0');
    } else {
        $user_check_sql = "SELECT * FROM tbl_users WHERE id = '$user_id' AND status = '1'";
        $user_check_result = mysqli_query($mysqli, $user_check_sql) or die(mysqli_error($mysqli));
        
        if (mysqli_num_rows($user_check_result) == 0) {
            $response = array('MSG' => 'User does not exist or is inactive', 'success' => '0');
        } else {
            $sql = "SELECT * FROM tbl_ringtone WHERE status = '1' AND active = '1' ORDER BY RAND() LIMIT 1";
            $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
            $ringtone = mysqli_fetch_assoc($result);

            if ($ringtone) {
                $data = ['ringtone_id' => $ringtone['id']];
                $update = Update('tbl_users', $data, "WHERE id = '$user_id'");

                $response = array(
                    'ringtone_id' => $ringtone['id'],
                    'ringtone_title' => $ringtone['ringtone_title'],
                    'ringtone_url' => ($ringtone['audio_type'] == "local") 
                        ? $file_path . 'uploads/' . $ringtone['ringtone_url']
                        : $ringtone['ringtone_url'],
                    'is_hyped' => $ringtone['is_hyped'],
                    'play_times' => $ringtone['play_times'],
                    'MSG' => 'Ringtone set successfully',
                    'success' => '1'
                );

                update_activity_log($user_id);
            } else {
                $response = array('MSG' => 'No ringtones available', 'success' => '0');
            }
        }
    }

    $set[$API_NAME][] = $response;
    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}else if ($get_helper['helper_name'] == "get_states") {

    $response = array();

    $sql = "SELECT id, name FROM states ORDER BY name ASC";
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        $response = array('MSG' => 'Database query failed', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $set[$API_NAME] = $data;
    header('Content-Type: application/json; charset=utf-8');
    echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}else if ($get_helper['helper_name'] == "get_cities") {

    $response = array();

    $state_id = isset($get_helper['state_id']) ? intval($get_helper['state_id']) : 0;

    if ($state_id < 1) {
        $response = array('MSG' => 'Invalid state ID', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    $sql = "SELECT id, name FROM cities WHERE state_id = $state_id ORDER BY name ASC";
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        $response = array('MSG' => 'Database query failed', 'success' => '0');
        $set[$API_NAME][] = $response;
        header('Content-Type: application/json; charset=utf-8');
        echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
    }

    // Fetch all rows at once
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $set[$API_NAME] = $data;
    header('Content-Type: application/json; charset=utf-8');
    echo str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}


else {
    $get_helper = get_api_data($_POST['data']);
}