<?php
/* Template Name:view job detais */
global $wpdb;
if(!isset($_REQUEST['token'])){
    wp_safe_redirect(site_url().'/404.php');
    exit();
}
$str = base64_decode($_REQUEST['token']);
$job_id = end(explode('/', $str));
get_header();
?>
<div class="ctn">
<!--contain-->
    <div class="contain_main">
        <div class="ctn_in">
            <h1><?php echo strtoupper(get_the_title());?></h1>
                <div class="registration_main">
                    <div class="open_post_bg">
                        <div class="open_post_bg_in">
                              <?php
                              $job_from = $wpdb->get_row("select * from wp_awarded_job where id_job = $job_id");                                            
                              if(!empty($job_from)){
                                if($job_from->post_type=='spaces'){
                                    $space_from=$wpdb->get_row("select * from wp_spaces where id_space=$job_from->id_post");
                                    if(!empty($space_from)){
                            ?>
                                        <div class="open_post_ul_div">
                                               <h2>Space details</h2>
                                                <ul>                                                         
                                                    <li>
                                                        <div class="open_post_li_width">Origin State:</div>
                                                        <div class="open_post_li_width2"><?php echo $space_from->o_state; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">From Date:</div>
                                                        <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($space_from->o_date)); ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Destination State:</div>
                                                        <div class="open_post_li_width2"><?php echo $space_from->d_state; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">To Date::</div>
                                                        <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($space_from->d_date)); ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Space Available:</div>
                                                        <div class="open_post_li_width2"><?php echo $space_from->space_available; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Trailer Size:</div>
                                                        <div class="open_post_li_width2"><?php echo $space_from->trailer_size; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Comments:</div>
                                                        <div class="open_post_li_width2"><?php echo $space_from->comments; ?></div>
                                                    </li>
                                                </ul>
                                        </div>                          
                                <?php   
                                        } 
                                    }
                                    elseif ($job_from->post_type == 'loads') {
                                        $loads_from=$wpdb->get_row("select * from wp_loads where id = $job_from->id_post");
                                        if(!empty($loads_from)){
                                 ?>
                                            <div class="open_post_ul_div">
                                               <h2 class="loads_cls">Loads Details</h2>
                                                  <ul>
                                                      <li>
                                                          <div class="open_post_li_width">Zip Code:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_zip; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">City:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_city; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">State:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_state; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Loading From:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_loading; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Date Load Available for Pickup:</div>
                                                          <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($loads_from->o_date)); ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Stairs:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_stairs; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">53' Trailer Available:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_traileravlble; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Long Haul:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_haul; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Load Size:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->o_loadsize; ?> c.f. </div>
                                                      </li>
                                                  </ul>
                                                  <div class="open_post_ul_bottom"></div>
                                                  <ul>
                                                      <li>
                                                          <div class="open_post_li_width">Dest Zip Code:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->d_zip; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Dest City:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->d_city; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">State:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->d_state; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Delivery To:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->d_delivary; ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Date Load Available for Delivery:</div>
                                                          <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($loads_from->d_date)); ?></div>
                                                      </li>
                                                      <li>
                                                          <div class="open_post_li_width">Comments:</div>
                                                          <div class="open_post_li_width2"><?php echo $loads_from->d_comments; ?></div>
                                                      </li>
                                                  </ul>
                                        </div>                          
                                <?php
                                          }
                                      }
                                      $award_by_user_id= get_userdata($job_from->id_user);
                                ?>
                                        <div class="open_post_ul_div">
                                              <h2 class="loads_cls">Job Awarded by</h2>
                                                <ul>                                                         

                                                    <li>
                                                        <div class="open_post_li_width">Company Name:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_by_user_id->company_name;  ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">DOT#:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_by_user_id->dot; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">MC#:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_by_user_id->mc; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Company Phone:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_by_user_id->company_phone; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Address:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_by_user_id->address;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">City:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_by_user_id->city;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">State:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_by_user_id->state; ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Zip:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_by_user_id->zip;?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Name:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_by_user_id->first_name." ".$award_by_user_id->last_name;  ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Phone:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_by_user_id->phone?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Email:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_by_user_id->user_email?></div>
                                                    </li>
                                                </ul>
                                        </div>  

                                   <?php $award_to_carrier_id = get_userdata($job_from->id_carrier); ?>
                                            <div class="open_post_ul_div">
                                              <h2 class="loads_cls">Job Awarded To</h2>
                                                <ul>                                                         

                                                    <li>
                                                        <div class="open_post_li_width">Company Name:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_to_carrier_id->company_name;  ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">DOT#:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_to_carrier_id->dot; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">MC#:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_to_carrier_id->mc; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Company Phone:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_to_carrier_id->company_phone; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Address:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_to_carrier_id->address;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">City:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_to_carrier_id->city;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">State:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_to_carrier_id->state; ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Zip:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_to_carrier_id->zip;?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Name:</div>
                                                        <div class="open_post_li_width2"><?php  echo $award_to_carrier_id->first_name." ".$award_to_carrier_id->last_name;  ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Phone:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_to_carrier_id->phone?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Email:</div>
                                                        <div class="open_post_li_width2"><?php echo $award_to_carrier_id->user_email?></div>
                                                    </li>
                                                </ul>
                                        </div> 
                                        <!-------Job ------->
                                        <div class="open_post_ul_div">
                                              <h2 class="loads_cls">Job Details</h2>
                                                <ul>                                                         

                                                    <li>
                                                        <div class="open_post_li_width">Dispatcher Name:</div>
                                                        <div class="open_post_li_width2"><?php echo $job_from->dispatcher_name;  ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Customer Name:</div>
                                                        <div class="open_post_li_width2"><?php echo $job_from->cus_name; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Customer Job ID#:</div>
                                                        <div class="open_post_li_width2"><?php echo $job_from->cus_job_id; ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Job Loading Date:</div>
                                                        <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($job_from->job_loading_date)); ?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">CFT Loaded:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->cft_loaded; ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Balance To Carrier:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->balance_to_carrier;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Dispatcher Phone:</div>
                                                        <div class="open_post_li_width2"><?php echo $job_from->dispatcher_phone; ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Customer Phone:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->cus_phone;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Job Pickup Date:</div>
                                                        <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($job_from->job_pickup_date)); ?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Blankets:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->blankets;?></div>
                                                    </li>
                                                   <li>
                                                        <div class="open_post_li_width">CFT PU:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->cft_pu;?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Cost Per CFT:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->cost_per_cft;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Client Balance Due:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->client_balance_due;?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Balance To Company:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->balance_to_company;?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Comments:</div>
                                                        <div class="open_post_li_width2"><?php  echo $job_from->job_comments;?></div>
                                                    </li>

                                                </ul>
                                        </div>
                              <?php  } ?>
                              </div>
                        </div>
                    </div>
            </div>
        </div>
  </div>
<?php get_footer(); ?>