<?php
/*
Plugin name: popupas
*/
function bfp_custom_style(){
   //styles registration
	wp_register_style('bfp_bootstarp', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css' );
   wp_enqueue_style('bfp_bootstarp');
}


add_action('wp_enqueue_scripts', 'bfp_custom_style');

function bfp_custom_scripts() {   
    //scripts registration
    wp_register_script( 'bfp_bootstarpJs', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'), '1.0' );
     wp_register_script( 'bfp_popJs', plugin_dir_url( __FILE__ ) . 'js/pop.js', array('jquery'), '1.0' );
    wp_enqueue_script('bfp_bootstarpJs');
    wp_enqueue_script('bfp_popJs');

}

/* call function in template then plugin is active */

add_action('wp_enqueue_scripts', 'bfp_custom_scripts');

/*  initialize variables outside all functions  */

$bfp_dateResults =''; 
$bfp_counter = '';
$bfp_currentDate ='';

/* count all records of current date */

function bfp_init_CountRecords(){

/* set variables to global 
 $bfp_currentDate - used for get curent date
 $bfp_dateResults = query all records of current date
 $bfp_counter - count all records of current date
*/
global $wpdb;
global $bfp_dateResults;
global $bfp_counter;
global $bfp_currentDate;
$bfp_currentDate = date("Y-m-d");
$bfp_dateResults = $wpdb->get_results( "SELECT text_for_date, published FROM wp_spidercalendar_event  WHERE date='$bfp_currentDate' AND published='1'");
$bfp_counter = count($bfp_dateResults);


}

/* call function in template then plugin is active */

add_action( 'wp_enqueue_scripts', 'bfp_init_CountRecords' );

/* set cookie */
function pop_setting_cookie() {
bfp_init_CountRecords(); // call function countRecords
global $bfp_counter; // set counter to gobal
global $bfp_published;
$cookie_name = "sausainis"; // cookie name
$cookie_value= $bfp_counter; //cookie value is recordsCount
$timestamp = time() + 120;  //example: 86400 = 1 day 
$deleteCookie = time () - 3600;  // delete cookie timer

/* cookie does not exist set cookie add shortcode and call function  */
if(!isset($_COOKIE[$cookie_name])) {
  setcookie($cookie_name, $cookie_value, $timestamp, "/"); 
  add_shortcode('popup','bfp_popup_modal_show');
}
else{
  add_shortcode('popup','bfp_popup_modal_hide');
}

/* if cookie name is greater than records counter, delele previuos cookie, set new cookie with modified value,add shortcode and call function  */
  if($_COOKIE[$cookie_name] < $bfp_counter){
  setcookie($cookie_name, '', $deleteCookie, "/");
  setcookie($cookie_name, $cookie_value, $timestamp, "/"); 
  add_shortcode('popup','bfp_popup_modal_show');


  }
  /* if cookie name is less than records counter, delele previuos cookie, set new cookie with modified value,add shortcode and call function  */
    if($_COOKIE[$cookie_name] > $bfp_counter){
  setcookie($cookie_name, '', $deleteCookie, "/");
  setcookie($cookie_name, $cookie_value, $timestamp, "/");
  }
  /* if counter equal to 0  add shortcode and call function */
  if($bfp_counter == 0){
  add_shortcode('popup','bfp_popup_modal_hide');
}
}

/* call function in template then plugin is active */
add_action( 'wp_enqueue_scripts', 'pop_setting_cookie' );

  // popup box function
 function bfp_popup_modal_show(){
     // get results from database
     global $wpdb;
     $pop_all =''; // blank variable
     $pop_currentDate = date("Y-m-d"); // get current date
     $pop_results = $wpdb->get_results( "SELECT  date, text_for_date, published  FROM wp_spidercalendar_event WHERE date='$pop_currentDate' AND published='1'");
     //fetch all data from text_for_date column in database
     foreach ($pop_results as $pop_res):
      
     $pop_response = $pop_res->text_for_date;
       
      //replace strings and cut in to pieces 

      //main text 
      $text=  preg_replace('//', '', $pop_response);
      $text2=  preg_replace("/<img [^>]+\>/i", '', $text);
      $text3 =  preg_replace("/<strong>[^>]+\>/i", '', $text2);
  
      // tiitle text
      $title=  preg_replace('//', '', $pop_response);
      $title2=  preg_replace("/<img [^>]+\>/i", '', $title);
      $title3 =  preg_replace("/<em>[^>]+\>/i", '', $title2);

      // image
     $img=  preg_replace('//', '', $pop_response);
     $img2 =  preg_replace("/<em>[^>]+\>/i", '', $img);
     $img3 =  preg_replace("/<strong>[^>]+\>/i", '', $img2);

    // combine string in to one
    $pop_all.=$title3."</br></br>".$img3."</br></br>".$text3."</br></br></br>";


    endforeach; 
             // create variable and set response from database comlun date
            $pop_dateofbirth = $pop_res->date;
            $pop_published =$pop_res->published;
            // if the current equals data of birth get response form $bfp_popup
           if($pop_currentDate == $pop_dateofbirth  && $pop_published){

           $bfp_popup = '
<!-- Modal -->
<div class="modal fade" id="mod" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-m" role="document">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">example</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times; </span>
        </button>
      </div> 
      <div class="modal-body">'.$pop_all.'</div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div>
  </div>
</div>
</div>';

  return $bfp_popup;
}
}
 // call popup hide function
 function bfp_popup_modal_hide(){
// return empty content
 $popup_hiden = '';
 return $popup_hiden;
 }


?>