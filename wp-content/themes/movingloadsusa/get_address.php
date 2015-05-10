<?php
    require $_SERVER['DOCUMENT_ROOT']."/MovingLoadsUSA/wp-blog-header.php";
    if(isset($_REQUEST['zipcode'])){
        $address = parse_address_google($_REQUEST['zipcode']);
        if($address['country'] === 'United States'){
            $display_state = $address['state'];
            if($address['neighborhood'] != ''){
                $display_city = ','.$address['neighborhood'].',';
            }
            if($address['sublocality'] != ''){
                $display_city .= ','.$address['sublocality'].',';
            }
            if($address['locality'] != ''){
                $display_city .= ','.$address['locality'].',';
            }
            if($address['sub_county'] != ''){
                $display_city .= ','.$address['sub_county'].',';
            }
            if($address['county'] != ''){
                $display_city .= ','.$address['county'].',';
            }
        }else{
            $display_city = 'Invalid US Zipcode.';
            $display_state = 'Invalid US Zipcode.';
        }
        
        echo json_encode(array('city' => str_replace(',,', ', ', trim($display_city,',')), 'state' => $display_state));
    }
?>
