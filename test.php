<?
include('apiconfig.php'); 
$base_url = $_SERVER['DOCUMENT_ROOT'] . '/gps/gps/gps/login';
$result = array(); 
$store_id = isset($_REQUEST['store_id'])?$_REQUEST['store_id']:'';

if($store_id == '' || !is_numeric($store_id)))
{
	$data['msg'] = 'Store Id is invalid';
	$response = array(400,$data);
	echo json_encode($response);
	return;
}
$prefix = "SELECT store_prefix, store_code, hub_id  FROM stores WHERE id='$store_id'";
$res           = $con->query($prefix);
$list          = $res->fetch_array();
$hub_id = $list['hub_id'];
$store_prefix = $list['store_prefix'];
$source_prefix = $list['store_code'];

$customer_details = isset($_REQUEST['customer_data'])?$_REQUEST['customer_data']:'';
if($customer_details== '')
{
	$data['msg'] = 'Customer details can not be blank';
	$response = array(400,$data);
	echo json_encode($response);
	return;
}


$existing_cust     = $customer_details['newexisting'];
$cust_id            == $customer_details['cust_id'];
$cust_name          === $customer_details['cust_name'];
$cust_mobile        = $customer_details['cust_mobile'];
$cust_email         = $customer_details['cust_email'];
$cust_address       = $customer_details['cust_address'];
$c_weight          = isset($customer_details['c_weight'])?$customer_details['c_weight']:'';
$body_postures = isset($_REQUEST['body_postures'])?$_REQUEST['body_postures']:'';
if($body_postures== '')
{
	$data['msg'] = 'Body postures can not be blank';
	$response = array(400,$data);
	echo json_encode($response);
	return;
}
if(count($body_postures)<4))
{
	$data['msg'] = 'Please send details of all body postures';
	$response = array(400,$data);
	echo json_encode($response);
	return;
}
$backshape          = $body_postures['backshape'];
$stomachshape       = $body_postures['stomachshape'];
$shouldershape      = $body_postures['shouldershape'];
$legshape           = $body_postures['legshape'];

$measurements = isset($_REQUEST['measurements'])?$_REQUEST['measurements']:'';
if($measurements== '')
{
	$data['msg'] = 'Measurements can not be blank';
	$response = array(400,$data);
	echo json_encode($response);
	return;
}

$type_id           = isset($_REQUEST['type_id'])?$_REQUEST['type_id']:'';

$shirt_data    = isset($measurements['shirt_data'])? $measurements['shirt_data'] :'';
$trouser_data  = isset($measurements['trouser_data'])? $measurements['trouser_data'] :'';
$jacket_data   = isset($measurements['jacket_data'])? $measurements['jacket_data']:'';
$s_sample_garment = isset($measurements['s_sample_garment'])?$measurements['s_sample_garment']:'';
$t_sample_garment  = isset($measurements['t_sample_garment'])?$measurements['t_sample_garment']:'';
$j_sample_garment  = isset($measurements['j_sample_garment'])?$measurements['j_sample_garment']:'';

$shirt_date    = isset($measurements['shirt_date'])?$measurements['shirt_date']:'';
$trouser_date  = isset($measurements['trouser_date'])?$measurements['trouser_date']:'';
$jacket_date   = isset($measurements['jacket_date'])?$measurements['jacket_date']:'';

$measurementstatus = isset($measurements['measurementstatus'])? $measurements['measurementstatus']:'';
$measurement_id = isset($measurements['measurement_id'])?$measurements['measurement_id']:'';
$added_on = date('Y-m-d H:i:s');

$customer_images =isset($_REQUEST['cust_images'])?$_REQUEST['cust_images']:'';

$chargeCount = $charge_styles = array();
$sqlCharge   = "SELECT id ,garment ,part FROM styles WHERE hub_id ='$hub_id' AND status = '1' AND chargeable = '1' order by sort_order asc";
$sqlCharge1  = $con->query($sqlCharge);
if ($sqlCharge1->num_rows > 0) 
{
	while ($list = $sqlCharge1->fetch_array()) {
	    $charge_styles['id'][]   = trim($list['id']);
	    $charge_styles['name'][] = trim($list['garment'] . '_' . $list['part']);
	}
}
if(isset($_REQUEST['shirtstyle']) && detail)
{
	$style = json_decode($_REQUEST['shirtstyle']);
 foreach($style as $skey => $svalue) {
        // $sjsonVal = json_decode($svalue);
        foreach ($svalue as $sk => $sval) {
            $sk = str_replace('"', '', $sk);
            if (in_array(trim($sval), $charge_styles['id']) && in_array(trim($sk), $charge_styles['name'])) {
                $chargeCount[] = array(
                    'style_id' => $sval,
                    'style_name' => $sk
                );
            }
        }
    }
}

if(isset($_REQUEST['trouserstyle']))
{  
    foreach ($_REQUEST['trouserstyle'] as $tkey => $tvalue) {
        $tjsonVal = json_decode($tvalue);
        foreach ($tjsonVal as $tk => $tval) {
            $tk = str_replace('"', '', $tk);
            if (in_array(trim($tval), $charge_styles['id']) && in_array(trim($tk), $charge_styles['name'])) {
                $chargeCount[] = array(
                    'style_id' => $tval,
                    'style_name' => $tk
                );
            }
        }
    }
}
  
if(isset($_REQUEST['jacketstyle']))
{  
    foreach ($_REQUEST['jacketstyle'] as $jkey => $jvalue) {
        $jjsonVal = json_decode($jvalue);
        foreach ($jjsonVal as $jk => $jval) {
            $jk = str_replace('"', '', $jk);
            if (in_array(trim($jval), $charge_styles['id']) && in_array(trim($jk), $charge_styles['name'])) {
                $chargeCount[] = array(
                    'style_id' => $jval,
                    'style_name' => $jk
                );
            }
        }
    }
}
 	$date1 = date('Y-m-d',strtotime("-7 days"));
  
    $date2 = date('Y-m-d',strtotime("+4 days"));
   
    $predict_sql = "SELECT c.id,c.hub_id,c.category, c.id,c.predict_date,c.slot1,c.slot2,c.slot3,c.slot4, c.speed_available,c.urgent_available,c.remaining ,(c.slot1+c.slot2+c.slot3+c.slot4) as total_slot FROM predict_calendar AS c WHERE  c.is_holiday != '1' AND c.is_weekend != '1' AND  c.remaining != c.slot1+c.slot2+c.slot3+c.slot4 AND 
        c.hub_id = '$hub_id' and  c.predict_date BETWEEN $date1 AND $date2  ORDER BY c.id ASC ";
    $predict_query  = $con->query($predict_sql);    
    if ($predict_query->num_rows > 0) {
        while ($predict_list = $predict_query->fetch_array()) {
            if($predict_list['total_slot'] != $predict_list['remaining']){
                $update_pre= "UPDATE predict_calendar SET remaining='".$predict_list['total_slot']."' WHERE  id ='".$predict_list['id']."'";
               $con->query($update_pre);
            }
        }
    }
if ($existing_cust == 1) 
{
           
            $update_customer_data = "UPDATE  customers SET name='$cust_name',email='$cust_email',address='$cust_address',back='$backshape',
            stomach='$stomachshape',
            shoulder='$shouldershape',
            legs='$legshape' 
            WHERE id = '$cust_id' ";
            
            
            $update_cust_data = $con->query($update_customer_data);
          
            
            $sql  = "SELECT cust_img1,cust_img2,cust_img3,cust_img4,cust_img5,phone FROM customers WHERE id='$cust_id'";
            $res           = $con->query($sql);
            $list          = $res->fetch_array();
            $customerimg_1 = $list['cust_img1'];
            $customerimg_2 = $list['cust_img2'];
            $customerimg_3 = $list['cust_img3'];
            $customerimg_4 = $list['cust_img4'];
            $customerimg_5 = $list['cust_img5'];
            
            
            $j = 1;
            for ($i = 1; $i < 6; $i++) {
            	$img = isset($customer_images['customerimage_' . $i])?$customer_images['customerimage_' . $i]:'';
                if (!empty($img)) {
                	$base_name =  basename($img);
                	$url = $base_url . "/customerimg/" . $base_name;
                	file_put_contents($url, file_get_contents($img));
                  
                    $customerimg         = "customerimg/" . $base_name;
                    $update_customer_img = "UPDATE  customers SET  cust_img{$i}= '$customerimg' where id = '$cust_id' ";
                    $custim  = $con->query($update_customer_img);
                } else {
                    if (isset($_REQUEST['removeimage']) && !empty($_REQUEST['removeimage'][$i])) {
                        $customerimg  = '';
                        $update_customer_img = "UPDATE  customers SET  cust_img{$i}= '$customerimg' where id = '$cust_id' ";
                        $custim  = $con->query($update_customer_img);
                    }
                }
            }
            if ($measurementstatus == 'new') {
                $cust_measurements = "INSERT INTO customer_measurements(cid,c_weight,shirt_measurement,trouser_measurement,jacket_measurement,added_on,shirt_date,trouser_date,jacket_date)
            	VALUES ('$cust_id','$c_weight','$shirt_data','$trouser_data','$jacket_data','$added_on','$shirt_date','$trouser_date','$jacket_date')";
                $cust_meas_data    = $con->query($cust_measurements);
                $measurement_id    = $con->insert_id;
            }
            
            
            /********Insert into orders table********/
            $order_id ='';
            $order_data        = "INSERT INTO orders(oid,cid,shirt_sample,trouser_sample,jacket_sample,status,store_id,added_on) VALUES('$order_id','$cust_id','$s_sample_garment','$t_sample_garment','$j_sample_garment','1','$type_id', '$added_on')";
            $order_data_insert = $con->query($order_data);
            
            $year          = date('y');
         
            $order_id_data = $con->query("SELECT o.oid,o.id,s.store_prefix 
                                        from orders o 
                                        INNER JOIN stores s on
                                        o.store_id = s.id 
                                        where o.store_id = '" . $type_id . "' 
                                        AND o.oid LIKE '" . $store_prefix . $year . '%' . "' ORDER BY o.id DESC LIMIT 1");
            if ($order_id_data->num_rows > 0) {
                $order_list        = $order_id_data->fetch_array();
              
                $latest_primary_id = $order_list['oid'];
                $seq  = $order_list['store_prefix'];
              	$sql  = "SELECT o.id,s.store_prefix 
                          from orders o 
                          INNER JOIN stores s on
                          o.store_id = s.id 
                          where o.store_id = '" . $type_id . "' ORDER BY o.id DESC LIMIT 1";
                $sqlNumber         = $con->query($sql);
                if ($sqlNumber->num_rows > 0) {
                    $sqlRes           = $sqlNumber->fetch_array();
                    $order_primary_id = $sqlRes['id'];
                    $existOrder       = $order_list['store_prefix'] . $year;
                   
                    $incOrder1        = explode($existOrder, $latest_primary_id);
                    $incOrder         = (int) $incOrder1[1];
                    $newId            = $incOrder + 1;
                    if ($newId < 10) {
                        $newId = '0' . $newId;
                    }
                    $firstid = $existOrder . $newId;
                    
                }
                if ($order_primary_id < 10) {
                    $order_primary_id = '0' . $order_primary_id;
                }
                
                $store_prefix = $order_list['store_prefix'];
                $new_order_id = $firstid;
                
            } else {
                $sql  = "SELECT o.id,s.store_prefix 
                          from orders o 
                          INNER JOIN stores s on
                          o.store_id = s.id 
                          where o.store_id = '" . $type_id . "' ORDER BY o.id DESC LIMIT 1";
                $sqlNumber = $con->query($sql);
                if ($sqlNumber->num_rows > 0) {
                    $sqlRes           = $sqlNumber->fetch_array();
                    $order_primary_id = $sqlRes['id'];
                    $firstid          = '01';
                    $new_order_id     = $store_prefix . $year . $firstid;
                }
            }
            
            $order_id = $con->query("UPDATE orders SET oid = '" . $new_order_id . "' WHERE id = '" . $order_primary_id . "'");
            if ($order_primary_id < 10) {
                $order_primary_id = ltrim($order_primary_id, '0');
                ;
            }
            
            $new_order_id = $order_primary_id;
            /****Done with insertion*****************/
            /*******Insert into sub oreders table****/
            if (isset($_REQUEST['pid'])) {
                
                $product_id           = $_REQUEST['pid'];
                $stitch_type          = $_REQUEST['stitch_type'];
                $service_type         = $_REQUEST['service_type'];
                $shirtstyle           = isset($_REQUEST['shirtstyle'])?$_REQUEST['shirtstyle']:'';
                $trouserstyle         = isset($_REQUEST['trouserstyle'])?$_REQUEST['trouserstyle']:'';
                $jacketstyle          = isset($_REQUEST['jacketstyle'])?$_REQUEST['jacketstyle']:'';
                $waiststyle           = isset($_REQUEST['waiststyle'])?$_REQUEST['waiststyle']:'';

                $shirt_comment           = isset($_REQUEST['shirt_comment'])?$_REQUEST['shirt_comment']:'';
                $trouser_comment         = isset($_REQUEST['trouser_comment'])?$_REQUEST['trouser_comment']:'';
                $jacket_comment          = isset($_REQUEST['jacket_comment'])?$_REQUEST['jacket_comment']:'';
                $waist_comment           = isset($_REQUEST['waist_comment'])?$_REQUEST['waist_comment']:'';


                $fabric_id            =  isset($_REQUEST['fabric_id'])?$_REQUEST['fabric_id']:'';
                $fabric_length        = isset($_REQUEST['min_fabric_length'])?$_REQUEST['min_fabric_length']:'';
                $pucodeSelect         = isset($_REQUEST['pucodeSelect'])?$_REQUEST['pucodeSelect']:'';
                $shirtreadyMeasure    = isset($_REQUEST['shirtreadyMeasure'])?$_REQUEST['shirtreadyMeasure']:'';
                $trouserreadyMeasure  = isset($_REQUEST['trouserreadyMeasure'])?$_REQUEST['trouserreadyMeasure']:'';
                $jacketreadyMeasure   = isset($_REQUEST['jacketreadyMeasure'])?$_REQUEST['jacketreadyMeasure']:'';
                $pc_id                = isset($_REQUEST['pc_id'])?$_REQUEST['pc_id']:'';
                $slot_available_date  = isset($_REQUEST['slot_available_date'])?$_REQUEST['slot_available_date']:'';
                $alter_available_date = isset($_REQUEST['alter_available_date'])?$_REQUEST['alter_available_date']:'';
                $alt_category         = isset($_REQUEST['alt_category'])?$_REQUEST['alt_category']:'';
                $extra_comment        = isset($_REQUEST['extra_comment'])?$_REQUEST['extra_comment']:'';
                $fit_type             = isset($_REQUEST['fit_type'])?$_REQUEST['fit_type']:'';
                $sub_order_id         = '';
                
               
                for ($product = 0; $product < count($product_id); $product++) {                    
                    $trial_date_added = '';
                    if (!empty($_REQUEST['trial_date'][$product])) {
                        $trial_date       = $_REQUEST['trial_date'][$product];
                        $trial_date_added = date('Y-m-d H:i:s', strtotime($trial_date));
                    }
                    
                    $del_date                   = isset($_REQUEST['del_date'][$product])?$_REQUEST['del_date'][$product]:'';
                    $delivery_date              = date('Y-m-d H:i:s', strtotime($del_date));
                    $product_data_added         = $product_id[$product];
                    $stitch_type_added          = $stitch_type[$product];
                    $service_type_added         = $service_type[$product];
                    $fabric_length_added        = $fabric_length[$product];
                    $shirtreadyMeasure_added    = $shirtreadyMeasure[$product];
                    $trouserreadyMeasure_added  = $trouserreadyMeasure[$product];
                    $jacketreadyMeasure_added   = $jacketreadyMeasure[$product];
                    $shirtstyle_added           = strip_tags(mysqli_escape_string($con, $shirtstyle[$product]));
                    $trouserstyle_added         = strip_tags(mysqli_escape_string($con, $trouserstyle[$product]));
                    $extra_comment_added         = strip_tags(mysqli_escape_string($con, $extra_comment[$product]));
                    $pucodeSelect_added         = strip_tags(mysqli_escape_string($con, $pucodeSelect[$product]));
                    $jacketstyle_added          = strip_tags(mysqli_escape_string($con, $jacketstyle[$product]));
                    $waiststyle_added           = strip_tags(mysqli_escape_string($con, $waiststyle[$product]));
                    $fabric_id_added            = strip_tags(mysqli_escape_string($con, $fabric_id[$product]));
                    $pc_id_added                = $pc_id[$product];
                    $slot_available_date_added  = $slot_available_date[$product];
                    $alter_available_date_added = $alter_available_date[$product];
                    $alt_category_added         = $alt_category[$product];
                    $fit_type_added             = $fit_type[$product];

                    $shirt_comment_added          = strip_tags(mysqli_escape_string($con, $shirt_comment[$product]));
                   $trouser_comment_added           = strip_tags(mysqli_escape_string($con, $trouser_comment[$product]));
                    $jacket_comment_added          = strip_tags(mysqli_escape_string($con, $jacket_comment[$product]));
                    $waist_comment_added           = strip_tags(mysqli_escape_string($con, $waist_comment[$product]));
                                     
                    $atf_status   = 0;
                    $trial_status = 0;
                    if ($stitch_type_added == 1) {
                        $atf_status = 1;
                    }
                    if ($stitch_type_added == 2) {
                        $trial_status = 1;
                    }
                    
                    $pu_query1 = ("SELECT pu from products WHERE id = " . $product_data_added);
                    $pu_query  = $con->query($pu_query1);
                    $pu_result = $pu_query->fetch_row();
                    //print_r($pu_result);die;
                    if (substr($pu_result[0], 0, 1) == 'L') {
                        $pu = $pu_result[0];
                    } else {
                        if ($service_type_added == '1') { //regular(S)
                            $pu = 'S' . $pu_result[0];
                        } else if ($service_type_added == '2') { //speed(U)
                            $pu = 'U' . $pu_result[0];
                        } else if ($service_type_added == '3') { //urgent(S)
                            $pu = 'S' . $pu_result[0];
                        } else if ($service_type_added == '4') { //premium(P)
                            $pu = 'P' . $pu_result[0];
                        }
                    }
                    
                    $rate_query  = $con->query("SELECT retail_price from store_products WHERE store_code ='$source_prefix' AND pu = '" . $pu . "'");
                    $rate_result = $rate_query->fetch_row();
                    if ($rate_result[0] == '') {
                        $rate_result[0] = '0';
                    }
                    
                     $sub_data_sql   = "INSERT INTO sub_orders(order_id,sid,pid,fabric_code,stitch,service,shirt_style,trouser_style,jacket_style,waist_style,shirt_ready,trouser_ready,jacket_ready,rate,status,alter_status,store_challan,hub_challan,added_on,delivery_date,trial_date,add_pucode,fabric_length,extra_comment,fit_type,shirt_comment, trouser_comment, jacket_comment, waist_comment)
                      VALUES ('$order_primary_id','$sub_order_id','$product_data_added','$fabric_id_added','$stitch_type_added','$service_type_added','$shirtstyle_added','$trouserstyle_added','$jacketstyle_added','$waiststyle_added',
                      '$shirtreadyMeasure_added','$trouserreadyMeasure_added','$jacketreadyMeasure_added','" . $rate_result[0] . "','1','0','0','0','$added_on','$delivery_date','$trial_date_added','$pucodeSelect_added','$fabric_length_added','$extra_comment_added','$fit_type_added',
                      '$shirt_comment_added','$trouser_comment_added','$jacket_comment_added','$waist_comment_added')";

                    $sub_order_data = $con->query($sub_data_sql);

                   // print_r( $extra_comment_added);
               
                     if ($service_type_added == '3') { 
                        $month=date('m');
                        $year=date('Y');
                        $store_sql= "SELECT id,urgent_remaining  FROM store_urgent_quota WHERE store_id = '$type_id' and month='$month' and year ='$year'  order by id desc limit 1";
                        $store_result     = $con->query($store_sql);
                        $storeData   = $store_result->fetch_assoc();
                      
                        $date= date("Y-m-d H:i:s");
                        $update_storesql="UPDATE store_urgent_quota SET urgent_remaining = urgent_remaining-1,last_updated ='$date' where id ='".$storeData['id']."'";
                        $con->query($update_storesql);
                    }

                    $sub_data       = $con->query("SELECT s.id,o.oid as oid from sub_orders s Left join orders o on 
                      s.order_id = o.id  where s.order_id = '$order_primary_id'");
                    if ($sub_data->num_rows > 0) {
                        while ($sub_list = $sub_data->fetch_array()) {
                            $sub_primary_id = $sub_list['id'];
                            if ($sub_primary_id < 10) {
                                $sub_primary_id = '0' . $sub_primary_id;
                            }
                            $order_sub_id     = $sub_list['oid'];
                            $new_sub_order_id = $order_sub_id . $sub_primary_id;
                        }
                    }
                    $subOrders_id = $con->query("UPDATE sub_orders SET sid = '" . $new_sub_order_id . "' ,trial_status = '" . $trial_status . "',atf_status= '" . $atf_status . "',measurement_id='$measurement_id' WHERE id = '" . $sub_primary_id . "'");
                    
                    $pc_id_added = rtrim($pc_id_added, ',');
                    $pc_id_added = explode(',', $pc_id_added);
                   
                 
                    /************************Multiple Garment types**********************************/
                    foreach ($pc_id_added as $key => $value) {
                        $pc_id_added = $value;
                        $pre_cl = "SELECT predict_date FROM predict_calendar WHERE id = '$pc_id_added'";
                       
                        $pre_Query = $con->query($pre_cl);
                        if ($pre_Query->num_rows > 0) {
                            $pre_Result   = $pre_Query->fetch_array();
                            $pre_predict_date   = $pre_Result['predict_date'];

                            $suborder_slot = "INSERT INTO suborder_slot(hub_id,sid,stitch_type,pc_id,slot_available_date,added_on) 
                             VALUES ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$added_on')";

                            $con->query($suborder_slot);
                            $suborderSlotID = $con->insert_id;
                            $calendar = "SELECT remaining,slot1,slot2,slot3,slot4,speed_available,urgent_available
                                                  FROM predict_calendar WHERE id = '$pc_id_added'";
                            $calendarQuery = $con->query($calendar);
                            if ($calendarQuery->num_rows > 0) {
                                $calendarResult   = $calendarQuery->fetch_array();
                                $remaining        = $calendarResult['remaining'];
                                $slot1            = $calendarResult['slot1'];
                                $slot2            = $calendarResult['slot2'];
                                $slot3            = $calendarResult['slot3'];
                                $slot4            = $calendarResult['slot4'];
                                $speed_available  = $calendarResult['speed_available'];
                                $urgent_available = $calendarResult['urgent_available'];
                                if ($slot1 > 0) {
                                    $pc_filedname      = 'slot1';
                                    $dynamic_new_slot1 = $slot1 - 1;
                                } else if ($slot2 > 0) {
                                    $pc_filedname      = 'slot2';
                                    $dynamic_new_slot1 = $slot2 - 1;
                                } else if ($slot3 > 0) {
                                    $pc_filedname      = 'slot3';
                                    $dynamic_new_slot1 = $slot3 - 1;
                                } else if ($slot4 > 0) {
                                    $pc_filedname      = 'slot4';
                                    $dynamic_new_slot1 = $slot4 - 1;
                                }
                                
                                if ($service_type_added == 3) { //urgent case
                                    
                                    $blocked_slot = $speed_available;
                                    $urgent_fir   = $slot1 - $blocked_slot;
                                    if ($urgent_fir > 0) {
                                        $pc_filedname      = 'slot1';
                                        $dynamic_new_slot1 = $slot1 - 1;
                                    } else if ($slot2 > 0) {
                                        $pc_filedname      = 'slot2';
                                        $dynamic_new_slot1 = $slot2 - 1;
                                    } else if ($slot3 > 0) {
                                        $pc_filedname      = 'slot3';
                                        $dynamic_new_slot1 = $slot3 - 1;
                                    } else if ($slot4 > 0) {
                                        $pc_filedname      = 'slot4';
                                        $dynamic_new_slot1 = $slot4 - 1;
                                    }
                                    
                                    
                                    $new_urgent_available = $urgent_available - 1;
                                    if ($new_urgent_available < 0) {
                                        $new_urgent_available = 0;
                                    }
                                    $new_remaining = $remaining - 1;
                                    //$new_slot1            = $slot1 - 1;
                                    $pc_filedname1 = ucfirst($pc_filedname);
                                    
                                    $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',urgent_available = '$new_urgent_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";

                                    $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id,urgent_unit) VALUES ('$new_remaining','$pc_filedname generate_receipt_exit_user','$dynamic_new_slot1','$pc_id_added','$hub_id','$new_urgent_available' ) ";
                                     $con->query($pre_ins);
                                     
                                    $suborderSlotStart = "UPDATE suborder_slot SET slot_available = '$pc_filedname1'  WHERE id = '" . $suborderSlotID . "'";

                                    $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                    ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$pc_filedname1','$added_on','generate_receipt_exit_user')";
                                    $con->query($sub_log);
                                    
                                    
                                } else if ($service_type_added == 4) {
                                    $new_speed_available = $speed_available - 1;
                                    $new_remaining       = $remaining - 1;
                                    if ($new_speed_available < 0) {
                                        $new_speed_available = 0;
                                    }
                                    //$new_slot1            = $slot1 - 1;
                                    $pc_filedname1     = ucfirst($pc_filedname);
                                    $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',speed_available = '$new_speed_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";


                                    $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id,speed_unit) VALUES ('$new_remaining','$pc_filedname generate_receipt_exit_user','$dynamic_new_slot1','$pc_id_added','$hub_id','$new_speed_available') ";
                                    $con->query($pre_ins);

                                   
                                    $suborderSlotStart = "UPDATE suborder_slot SET slot_available = '$pc_filedname1'  WHERE id = '" . $suborderSlotID . "'";

                                    $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                    ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$pc_filedname1','$added_on','generate_receipt_exit_user')";
                                    $con->query($sub_log);



                                }


                                
                                else if ($service_type_added == 2) {
                                    
                                    $blocked_slot = $urgent_available;
                                    $speed_fir    = $slot1 - $blocked_slot;
                                    
                                    if ($speed_fir > 0) {
                                        $pc_filedname      = 'slot1';
                                        $dynamic_new_slot1 = $slot1 - 1;
                                    } else if ($slot2 > 0) {
                                        $pc_filedname      = 'slot2';
                                        $dynamic_new_slot1 = $slot2 - 1;
                                    } else if ($slot3 > 0) {
                                        $pc_filedname      = 'slot3';
                                        $dynamic_new_slot1 = $slot3 - 1;
                                    } else if ($slot4 > 0) {
                                        $pc_filedname      = 'slot4';
                                        $dynamic_new_slot1 = $slot4 - 1;
                                    }
                                    
                                    
                                    
                                    $new_speed_available = $speed_available - 1;
                                    $new_remaining       = $remaining - 1;
                                    
                                    if ($new_speed_available < 0) {
                                        $new_speed_available = 0;
                                    }
                                    //$new_slot1            = $slot1 - 1;
                                    $pc_filedname1     = ucfirst($pc_filedname);
                                    $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',speed_available = '$new_speed_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";

                                    $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id,speed_unit) VALUES ('$new_remaining','$pc_filedname generate_receipt_exit_user','$dynamic_new_slot1','$pc_id_added','$hub_id','$new_speed_available') ";
                                    $con->query($pre_ins);


                                  
                                    $suborderSlotStart = "UPDATE suborder_slot SET slot_available = '$pc_filedname1'  WHERE id = '" . $suborderSlotID . "'";


                                    $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                    ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$pc_filedname1','$added_on','generate_receipt_exit_user')";
                                    $con->query($sub_log);



                                } else if ($service_type_added == 1) {
                                    $blocked_slot  = $speed_available + $urgent_available;
                                    $new_remaining = $remaining - 1;
                                    $stf_fir       = $slot1 - $blocked_slot;
                                    if ($stf_fir > 0) {
                                        
                                        $slot1 - $blocked_slot;
                                        $new_slot1         = $slot1 - 1;
                                        if($new_slot1 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',slot1 = '$new_slot1' 
                                                             WHERE id = '$pc_id_added'";

                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot1_generate_receipt_exit_user','$new_slot1','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);


                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot1' 
                                                             WHERE id = '" . $suborderSlotID . "'";

                                            $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot1','$added_on','generate_receipt_exit_user')";
                                            $con->query($sub_log);

                                        }
                                        
                                    } else if ($slot2 > 0) {
                                        $new_slot2         = $slot2 - 1;
                                        if($new_slot2 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining', slot2 = '$new_slot2' 
                                                                 WHERE id = '$pc_id_added'";
                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot2_generate_receipt_exit_user','$new_slot2','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);

                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot2' 
                                                                 WHERE id = '" . $suborderSlotID . "'";

                                             $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot2','$added_on','generate_receipt_exit_user')";
                                            $con->query($sub_log);
                                        }
                                    } else if ($slot3 > 0) {
                                        $new_slot3         = $slot3 - 1;
                                        if($new_slot3 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',slot3 = '$new_slot3' 
                                                                 WHERE id = '$pc_id_added'";

                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot3_generate_receipt_exit_user','$new_slot3','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);


                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot3' 
                                                                 WHERE id = '" . $suborderSlotID . "'";

                                            $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot3','$added_on','generate_receipt_exit_user')";
                                            $con->query($sub_log);

                                        }
                                    } else if ($slot4 > 0) {
                                        $new_slot4         = $slot4 - 1;
                                        if($new_slot4 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',slot4 = '$new_slot4' 
                                                                 WHERE id = '$pc_id_added'";

                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot4_generate_receipt_exit_user','$new_slot4','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);

                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot4' 
                                                                 WHERE id = '" . $suborderSlotID . "'";

                                            $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot4','$added_on','generate_receipt_exit_user')";
                                            $con->query($sub_log);
                                        }
                                    }   
                                }
                                
                                
                                $con->query($updateCalendar);
                                $con->query($suborderSlotStart);
                             
                            }
                        }
                      
                    }
                    
                   
                    foreach ($chargeCount as $ckey => $cvalue) {
                        $charge_style_query = "INSERT INTO suborders_chargeable_styles( order_id, suborder_id, style_id, hub_id, store_id, added_on) VALUES ('" . $order_primary_id . "','" . $sub_primary_id . "','" . $cvalue['style_id'] . "','" . $hub_id . "','" . $type_id . "','" . $added_on . "')";
                        $con->query($charge_style_query);
                    }
                }
             
            }
       
} 
else
{
    $customerimg_1 = '';
    $customerimg_2 = '';
    $customerimg_3 = '';
    $customerimg_4 = '';
    $customerimg_5 = '';
    
    $customer_data = "INSERT INTO customers(name,email,phone,address,back,stomach,shoulder,legs,added_on) Values ('$cust_name','$cust_email','$cust_mobile','$cust_address','$backshape','$stomachshape','$shouldershape','$legshape','$added_on')";
    $customernew   = $con->query($customer_data);
    $customer_id   = $con->insert_id;
    
            
    for ($i = 1; $i < 6; $i++) {
    	$img = isset($customer_images['customerimage_' . $i])?$customer_images['customerimage_' . $i]:'';
        if (!empty($img)) {
           	$base_name =  basename($img);
            $url = $base_url . "/customerimg/" . $base_name;
            file_put_contents($url, file_get_contents($img));
                  
            $customerimg         = "customerimg/" . $base_name;
          	$update_customer_img = "UPDATE  customers SET  cust_img{$i}= '$customerimg' where id = '$cust_id' ";
            $custim  = $con->query($update_customer_img);
            
        }
    }
            
    $cust_measurements = "INSERT INTO customer_measurements(cid,c_weight,shirt_measurement,trouser_measurement,jacket_measurement,added_on,shirt_date,trouser_date,jacket_date)
   VALUES ('$customer_id','$c_weight','$shirt_data','$trouser_data','$jacket_data','$added_on','$shirt_date','$trouser_date','$jacket_date')";
    $cust_meas_data    = $con->query($cust_measurements);
    $measurement_id    = $con->insert_id;
            
           
            $otp_customers     = "INSERT INTO customer_verified_stores(cid,store_id,added_on) VALUES ('$customer_id','$type_id','$added_on')";
            $otp_data          = $con->query($otp_customers);
            //include 'new_customer_sms.php';
            /********Insert into orders table********/
            $order_data        = "INSERT INTO orders(oid,cid,shirt_sample,trouser_sample,jacket_sample,status,store_id,added_on) VALUES('$order_id','$customer_id','$s_sample_garment','$t_sample_garment','$j_sample_garment','1','$type_id', '$added_on')";
            $order_data_insert = $con->query($order_data);
            $year              = date('y');
            $order_id_data     = $con->query("SELECT o.oid,o.id,s.store_prefix 
                                          from orders o 
                                          INNER JOIN stores s on
                                          o.store_id = s.id 
                                          where o.store_id = '" . $type_id . "' 
                                          AND o.oid LIKE '" . $store_prefix . $year . '%' . "' ORDER BY o.id DESC LIMIT 1");
            if ($order_id_data->num_rows > 0) {
                $order_list        = $order_id_data->fetch_array();
                $latest_primary_id = $order_list['oid'];
                $sql               = "SELECT o.id,s.store_prefix 
                          from orders o 
                          INNER JOIN stores s on
                          o.store_id = s.id 
                          where o.store_id = '" . $type_id . "' ORDER BY o.id DESC LIMIT 1";
                $sqlNumber         = $con->query($sql);
                if ($sqlNumber->num_rows > 0) {
                    $sqlRes           = $sqlNumber->fetch_array();
                    $order_primary_id = $sqlRes['id'];
                    $existOrder       = $order_list['store_prefix'] . $year;
                    $incOrder1        = explode($existOrder, $latest_primary_id);
                    $incOrder         = (int) $incOrder1[1];
                    $newId            = $incOrder + 1;
                    if ($newId < 10) {
                        $newId = '0' . $newId;
                    }
                    $firstid = $existOrder . $newId;
                    
                }
                if ($order_primary_id < 10) {
                    $order_primary_id = '0' . $order_primary_id;
                }
                
                $store_prefix = $order_list['store_prefix'];
                $new_order_id = $firstid;
            } else {
                $sql       = "SELECT o.id,s.store_prefix 
                          from orders o 
                          INNER JOIN stores s on
                          o.store_id = s.id 
                          where o.store_id = '" . $type_id . "' ORDER BY o.id DESC LIMIT 1";
                $sqlNumber = $con->query($sql);
                if ($sqlNumber->num_rows > 0) {
                    $sqlRes           = $sqlNumber->fetch_array();
                    $order_primary_id = $sqlRes['id'];
                    $firstid          = '01';
                    $new_order_id     = $store_prefix . $year . $firstid;
                }
                
            }
            
            $order_id = $con->query("UPDATE orders SET oid = '" . $new_order_id . "' WHERE id = '" . $order_primary_id . "'");
            if ($order_primary_id < 10) {
                $order_primary_id = ltrim($order_primary_id, '0');
                ;
            }
            $new_order_id = $order_primary_id;
            /****Done with insertion*****************/
            /*******Insert into sub orders table*****/
            if (!empty($_REQUEST['pid'])) {
                $product_id           = $_REQUEST['pid'];
                $stitch_type          = $_REQUEST['stitch_type'];
                $service_type         = $_REQUEST['service_type'];
                $shirtstyle           = $_REQUEST['shirtstyle'];
                $trouserstyle         = $_REQUEST['trouserstyle'];
                $jacketstyle          = $_REQUEST['jacketstyle'];
                $waiststyle           = $_REQUEST['waiststyle'];
                $fabric_id            = $_REQUEST['fabric_id'];
                $pucodeSelect         = $_REQUEST['pucodeSelect'];
                $fabric_length        = $_REQUEST['min_fabric_length'];
                $shirtreadyMeasure    = $_REQUEST['shirtreadyMeasure'];
                $trouserreadyMeasure  = $_REQUEST['trouserreadyMeasure'];
                $jacketreadyMeasure   = $_REQUEST['jacketreadyMeasure'];
                $extra_comment        = $_REQUEST['extra_comment'];
                $pc_id                = $_REQUEST['pc_id'];
                $slot_available_date  = $_REQUEST['slot_available_date'];
                $alter_available_date = $_REQUEST['alter_available_date'];
                $alt_category         = $_REQUEST['alt_category'];
                $fit_type             = $_REQUEST['fit_type'];

                $shirt_comment           = $_REQUEST['shirt_comment'];
                $trouser_comment         = $_REQUEST['trouser_comment'];
                $jacket_comment          = $_REQUEST['jacket_comment'];
                $waist_comment           = $_REQUEST['waist_comment'];

                $sub_order_id         = '';
                for ($product = 0; $product < count($product_id); $product++) {
                                       
                    $trial_date_added = '';
                    if (!empty($_REQUEST['trial_date'][$product])) {
                        $trial_date       = $_REQUEST['trial_date'][$product];
                        $trial_date_added = date('Y-m-d H:i:s', strtotime($trial_date));
                    }
                    
                    $del_date                   = $_REQUEST['del_date'][$product];
                    $delivery_date              = date('Y-m-d H:i:s', strtotime($del_date));
                    $product_data_added         = $product_id[$product];
                    $stitch_type_added          = $stitch_type[$product];
                    $service_type_added         = $service_type[$product];
                    $fabric_length_added        = $fabric_length[$product];
                    $shirtreadyMeasure_added    = $shirtreadyMeasure[$product];
                    $trouserreadyMeasure_added  = $trouserreadyMeasure[$product];
                    $jacketreadyMeasure_added   = $jacketreadyMeasure[$product];
                    $shirtstyle_added           = strip_tags(mysqli_escape_string($con, $shirtstyle[$product]));
                    $trouserstyle_added         = strip_tags(mysqli_escape_string($con, $trouserstyle[$product]));
                    $pucodeSelect_added         = strip_tags(mysqli_escape_string($con, $pucodeSelect[$product]));
                    $jacketstyle_added          = strip_tags(mysqli_escape_string($con, $jacketstyle[$product]));
                    $waiststyle_added           = strip_tags(mysqli_escape_string($con, $waiststyle[$product]));
                    $fabric_id_added            = strip_tags(mysqli_escape_string($con, $fabric_id[$product]));
                    $extra_comment_added         = strip_tags(mysqli_escape_string($con, $extra_comment[$product]));
                    $pc_id_added                = $pc_id[$product];
                    $slot_available_date_added  = $slot_available_date[$product];
                    $alter_available_date_added = $alter_available_date[$product];
                    $alt_category_added         = $alt_category[$product];
                    $fit_type_added             = $fit_type[$product];

                    $shirt_comment_added          = strip_tags(mysqli_escape_string($con, $shirt_comment[$product]));
                   $trouser_comment_added           = strip_tags(mysqli_escape_string($con, $trouser_comment[$product]));
                    $jacket_comment_added          = strip_tags(mysqli_escape_string($con, $jacket_comment[$product]));
                    $waist_comment_added           = strip_tags(mysqli_escape_string($con, $waist_comment[$product]));
                    
                    $atf_status   = 0;
                    $trial_status = 0;
                    if ($stitch_type_added == 1) {
                        $atf_status = 1;
                    }
                    if ($stitch_type_added == 2) {
                        $trial_status = 1;
                    }
                    
                    $pu_query1 = ("SELECT pu from products WHERE id = " . $product_data_added);
                    $pu_query  = $con->query($pu_query1);
                    $pu_result = $pu_query->fetch_row();
                    //print_r($pu_result);die;
                    if (substr($pu_result[0], 0, 1) == 'L') {
                        $pu = $pu_result[0];
                    } else {
                        if ($service_type_added == '1') { //regular(S)
                            $pu = 'S' . $pu_result[0];
                        } else if ($service_type_added == '2') { //speed(U)
                            $pu = 'U' . $pu_result[0];
                        } else if ($service_type_added == '3') { //urgent(S)
                            $pu = 'S' . $pu_result[0];
                        } else if ($service_type_added == '4') { //premium(P)
                            $pu = 'P' . $pu_result[0];
                        }
                    }
                    
                    $rate_query  = $con->query("SELECT retail_price from store_products WHERE store_code = " . $source_prefix . " AND pu = '" . $pu . "'");
                    $rate_result = $rate_query->fetch_row();
                    if ($rate_result[0] == '') {
                        $rate_result[0] = '0';
                    }
                    
                    $sub_data_sql   = "INSERT INTO sub_orders(order_id,sid,pid,fabric_code,stitch,service,shirt_style,trouser_style,jacket_style,waist_style,shirt_ready,trouser_ready,jacket_ready,rate,status,alter_status,store_challan,hub_challan,added_on,delivery_date,trial_date,add_pucode,fabric_length,extra_comment,fit_type,shirt_comment, trouser_comment, jacket_comment, waist_comment)
                      VALUES ('$order_primary_id','$sub_order_id','$product_data_added','$fabric_id_added','$stitch_type_added','$service_type_added','$shirtstyle_added','$trouserstyle_added','$jacketstyle_added','$waiststyle_added',
                      '$shirtreadyMeasure_added','$trouserreadyMeasure_added','$jacketreadyMeasure_added','" . $rate_result[0] . "','1','0','0','0','$added_on','$delivery_date','$trial_date_added','$pucodeSelect_added','$fabric_length_added','$extra_comment_added','$fit_type_added','$shirt_comment_added','$trouser_comment_added','$jacket_comment_added','$waist_comment_added')";
                    $sub_order_data = $con->query($sub_data_sql);

                    if ($service_type_added == '3') { 
                        $month=date('m');
                        $year=date('Y');
                        $store_sql= "SELECT id,urgent_remaining  FROM store_urgent_quota WHERE store_id = '".$type_id."' and month='$month' and year ='$year'  order by id desc limit 1";
                        $store_result     = $con->query($store_sql);
                        $storeData   = $store_result->fetch_assoc();
                        $storeData['id'];
                        $date= date("Y-m-d H:i:s");
                        $update_storesql="UPDATE store_urgent_quota SET urgent_remaining = urgent_remaining-1,last_updated ='$date' where id ='".$storeData['id']."'";
                        $con->query($update_storesql);
                    }


                    $sub_data       = $con->query("SELECT s.id,o.oid as oid from sub_orders s Left join orders o on 
                      s.order_id = o.id  where s.order_id = '" . $order_primary_id . "'");
                    if ($sub_data->num_rows > 0) {
                        while ($sub_list = $sub_data->fetch_array()) {
                            $sub_primary_id = $sub_list['id'];
                            if ($sub_primary_id < 10) {
                                $sub_primary_id = '0' . $sub_primary_id;
                            }
                            $order_sub_id     = $sub_list['oid'];
                            $new_sub_order_id = $order_sub_id . $sub_primary_id;
                        }
                    }
                    $subOrders_id = $con->query("UPDATE sub_orders SET sid = '" . $new_sub_order_id . "' ,trial_status = '" . $trial_status . "',atf_status= '" . $atf_status . "',measurement_id='$measurement_id' WHERE id = '" . $sub_primary_id . "'");
                    
                    $pc_id_added = rtrim($pc_id_added, ',');
                    $pc_id_added = explode(',', $pc_id_added);
                  
                    foreach ($pc_id_added as $key => $value) {
                        $pc_id_added = $value;
                        $pre_cl = "SELECT predict_date FROM predict_calendar WHERE id = '$pc_id_added'";
                       
                        $pre_Query = $con->query($pre_cl);
                        if ($pre_Query->num_rows > 0) {
                            $pre_Result   = $pre_Query->fetch_array();
                            $pre_predict_date   = $pre_Result['predict_date'];

                            $suborder_slot = "INSERT INTO suborder_slot(hub_id,sid,stitch_type,pc_id,slot_available_date,added_on) 
                             VALUES ('" . $hub_id. "','" . $sub_primary_id . "','$stitch_type_added','$pc_id_added','$pre_predict_date','$added_on')";
                            $con->query($suborder_slot);
                            $suborderSlotID = $con->insert_id;
                            $calendar = "SELECT remaining,slot1,slot2,slot3,slot4,speed_available,urgent_available
                                                  FROM predict_calendar WHERE id = '$pc_id_added'";
                            $calendarQuery = $con->query($calendar);
                            if ($calendarQuery->num_rows > 0) {
                                $calendarResult   = $calendarQuery->fetch_array();
                                $remaining        = $calendarResult['remaining'];
                                $slot1            = $calendarResult['slot1'];
                                $slot2            = $calendarResult['slot2'];
                                $slot3            = $calendarResult['slot3'];
                                $slot4            = $calendarResult['slot4'];
                                $speed_available  = $calendarResult['speed_available'];
                                $urgent_available = $calendarResult['urgent_available'];
                                if ($slot1 > 0) {
                                    $pc_filedname      = 'slot1';
                                    $dynamic_new_slot1 = $slot1 - 1;
                                } else if ($slot2 > 0) {
                                    $pc_filedname      = 'slot2';
                                    $dynamic_new_slot1 = $slot2 - 1;
                                } else if ($slot3 > 0) {
                                    $pc_filedname      = 'slot3';
                                    $dynamic_new_slot1 = $slot3 - 1;
                                } else if ($slot4 > 0) {
                                    $pc_filedname      = 'slot4';
                                    $dynamic_new_slot1 = $slot4 - 1;
                                }
                                
                                if ($service_type_added == 3) { //urgent case
                                    
                                    $blocked_slot = $speed_available;
                                    $urgent_fir   = $slot1 - $blocked_slot;
                                    if ($urgent_fir > 0) {
                                        $pc_filedname      = 'slot1';
                                        $dynamic_new_slot1 = $slot1 - 1;
                                    } else if ($slot2 > 0) {
                                        $pc_filedname      = 'slot2';
                                        $dynamic_new_slot1 = $slot2 - 1;
                                    } else if ($slot3 > 0) {
                                        $pc_filedname      = 'slot3';
                                        $dynamic_new_slot1 = $slot3 - 1;
                                    } else if ($slot4 > 0) {
                                        $pc_filedname      = 'slot4';
                                        $dynamic_new_slot1 = $slot4 - 1;
                                    }
                                    
                                    
                                    $new_urgent_available = $urgent_available - 1;
                                    if ($new_urgent_available < 0) {
                                        $new_urgent_available = 0;
                                    }
                                    $new_remaining = $remaining - 1;
                                    //$new_slot1            = $slot1 - 1;
                                    $pc_filedname1 = ucfirst($pc_filedname);
                                    
                                    $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',urgent_available = '$new_urgent_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";
                                    // $updateCalendar       = "UPDATE predict_calendar SET urgent_available = '$new_urgent_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";
                                    $suborderSlotStart = "UPDATE suborder_slot SET slot_available = '$pc_filedname1'  WHERE id = '" . $suborderSlotID . "'";

                                    $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id,urgent_unit) VALUES ('$new_remaining','$pc_filedname generate_receipt_new_user','$dynamic_new_slot1','$pc_id_added','$hub_id','$new_urgent_available' ) ";
                                     $con->query($pre_ins);

                                    $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                    ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$pc_filedname1','$added_on','generate_receipt_new_user')";
                                    $con->query($sub_log);
                                    
                                    
                                } else if ($service_type_added == 4) {
                                    $new_speed_available = $speed_available - 1;
                                    $new_remaining       = $remaining - 1;
                                    if ($new_speed_available < 0) {
                                        $new_speed_available = 0;
                                    }
                                    //$new_slot1            = $slot1 - 1;
                                    $pc_filedname1     = ucfirst($pc_filedname);
                                    $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',speed_available = '$new_speed_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";
                                    
                                    $suborderSlotStart = "UPDATE suborder_slot SET slot_available = '$pc_filedname1'  WHERE id = '" . $suborderSlotID . "'";

                                    $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id,speed_unit) VALUES ('$new_remaining','$pc_filedname generate_receipt_new_user','$dynamic_new_slot1','$pc_id_added','$hub_id','$new_speed_available') ";
                                    $con->query($pre_ins);
                                    
                                     $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                    ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$pc_filedname1','$added_on','generate_receipt_new_user')";
                                    $con->query($sub_log);
                                }
                                
                                else if ($service_type_added == 2) {
                                    
                                    $blocked_slot = $urgent_available;
                                    $speed_fir    = $slot1 - $blocked_slot;
                                    
                                    if ($speed_fir > 0) {
                                        $pc_filedname      = 'slot1';
                                        $dynamic_new_slot1 = $slot1 - 1;
                                    } else if ($slot2 > 0) {
                                        $pc_filedname      = 'slot2';
                                        $dynamic_new_slot1 = $slot2 - 1;
                                    } else if ($slot3 > 0) {
                                        $pc_filedname      = 'slot3';
                                        $dynamic_new_slot1 = $slot3 - 1;
                                    } else if ($slot4 > 0) {
                                        $pc_filedname      = 'slot4';
                                        $dynamic_new_slot1 = $slot4 - 1;
                                    }
                                    
                                    
                                    
                                    $new_speed_available = $speed_available - 1;
                                    $new_remaining       = $remaining - 1;
                                    
                                    if ($new_speed_available < 0) {
                                        $new_speed_available = 0;
                                    }
                                    //$new_slot1            = $slot1 - 1;
                                    $pc_filedname1     = ucfirst($pc_filedname);
                                    $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',speed_available = '$new_speed_available',$pc_filedname = '$dynamic_new_slot1' WHERE id = '$pc_id_added'";
                                    
                                    $suborderSlotStart = "UPDATE suborder_slot SET slot_available = '$pc_filedname1'  WHERE id = '" . $suborderSlotID . "'";

                                    $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id,speed_unit) VALUES ('$new_remaining','$pc_filedname generate_receipt_new_user','$dynamic_new_slot1','$pc_id_added','$hub_id','$new_speed_available') ";
                                    $con->query($pre_ins);
                                    
                                    
                                     $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                    ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','$pc_filedname1','$added_on','generate_receipt_new_user')";
                                    $con->query($sub_log);
                                } else if ($service_type_added == 1) {
                                   $blocked_slot  = $speed_available + $urgent_available;
                                    $new_remaining = $remaining - 1;
                                    $stf_fir       = $slot1 - $blocked_slot;
                                    if ($stf_fir > 0) {
                                        
                                        $slot1 - $blocked_slot;
                                        $new_slot1         = $slot1 - 1;
                                        if($new_slot1 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',slot1 = '$new_slot1' 
                                                             WHERE id = '$pc_id_added'";

                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot1_generate_receipt_new_user','$new_slot1','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);


                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot1' 
                                                             WHERE id = '" . $suborderSlotID . "'";

                                            $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot1','$added_on','generate_receipt_new_user')";
                                            $con->query($sub_log);

                                        }
                                        
                                    }else if ($slot2 > 0) {
                                        $new_slot2         = $slot2 - 1;
                                        if($new_slot2 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining', slot2 = '$new_slot2' 
                                                                 WHERE id = '$pc_id_added'";
                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot2_generate_receipt_new_user','$new_slot2','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);

                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot2' 
                                                                 WHERE id = '" . $suborderSlotID . "'";

                                             $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot2','$added_on','generate_receipt_new_user')";
                                            $con->query($sub_log);
                                        }
                                    } else if ($slot3 > 0) {
                                        $new_slot3         = $slot3 - 1;
                                        if($new_slot3 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',slot3 = '$new_slot3' 
                                                                 WHERE id = '$pc_id_added'";

                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot3_generate_receipt_new_user','$new_slot3','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);


                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot3' 
                                                                 WHERE id = '" . $suborderSlotID . "'";

                                            $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot3','$added_on','generate_receipt_new_user')";
                                            $con->query($sub_log);

                                        }
                                    } else if ($slot4 > 0) {
                                        $new_slot4         = $slot4 - 1;
                                        if($new_slot4 >= 0){
                                            $updateCalendar    = "UPDATE predict_calendar SET remaining = '$new_remaining',slot4 = '$new_slot4' 
                                                                 WHERE id = '$pc_id_added'";

                                            $pre_ins = "INSERT INTO predict_calendar_log(remaining,messege,slot,pc_id,hub_id) VALUES ('$new_remaining','slot4_generate_receipt_new_user','$new_slot4','$pc_id_added','$hub_id') ";
                                            $con->query($pre_ins);

                                            $suborderSlotStart = "UPDATE suborder_slot SET slot_available = 'Slot4' 
                                                                 WHERE id = '" . $suborderSlotID . "'";

                                            $sub_log = "INSERT INTO suborder_slot_log( hub_id, sid, stitch_type, pc_id, slot_available_date, slot_available, added_on, messege) VALUES 
                                            ('$hub_id','$sub_primary_id','$stitch_type_added','$pc_id_added','$pre_predict_date','Slot4','$added_on','generate_receipt_new_user')";
                                            $con->query($sub_log);
                                        }
                                    }     
                                }
                                
                                
                                
                                $con->query($updateCalendar);
                                $con->query($suborderSlotStart);
                                
                              
                            }
                        }
                    }
                   
                }
            }
}

if($new_order_id > 0)
{
	$data['msg'] = 'Order created successfully';
	$data['order_id'] = $new_order_id;
	$response = array(200,$data);
	echo json_encode($response);
	return;
}
