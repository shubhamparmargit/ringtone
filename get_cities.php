<?php
 include("includes/header.php");
 require("includes/lb_helper.php");
 require("language/language.php");

if(isset($_POST['state_id'])) {
    $state_id = $_POST['state_id'];
    $cities_qry = "SELECT * FROM cities WHERE state_id = '$state_id' ORDER BY name";
    $cities_result = mysqli_query($mysqli, $cities_qry);
    
    echo '<option value="">--Select City--</option>';
    while($cities_row = mysqli_fetch_array($cities_result)) {
        echo '<option value="'.$cities_row['id'].'">'.$cities_row['name'].'</option>';
    }
}
?>
