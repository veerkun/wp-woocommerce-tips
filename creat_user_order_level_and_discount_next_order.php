<?php 
/*
 * creat_user_order_level_and_discount_next_order.php
 * Creat user order level
 * Discount order totals for custom user order level in next order
 * add key to user meta total ordered complete
 * add key to user meta member level
 * Check member level and add fee to cart
 * add code to functions.php of current theme or include this file
*/
add_action( 'woocommerce_order_status_completed', 'vk_process_user_order' );
function vk_process_user_order($order_id) {
	global $woocommerce;
	$order = new WC_Order( $order_id );
	//Get user ID 
	$user_id = $order->get_user_id();
	if($user_id == 0 ) { return; }
	////$money_spent = get_user_meta($user_id, '_money_spent', true);
	$order_total_price = $order->get_total();
	$totals_user_ordered = get_user_meta($user_id, '_vk_totals_ordered', true);
	if(!$totals_user_ordered || $totals_user_ordered == "" ) {
		
		update_user_meta($user_id, '_vk_totals_ordered', $order_total_price);
	} else {
		$new_total_ordered = $order_total_price + $totals_user_ordered;
		update_user_meta($user_id, '_vk_totals_ordered', $new_total_ordered);
	}
	$totals_user_ordered_after = get_user_meta($user_id, '_vk_totals_ordered', true);
	if($totals_user_ordered_after > 5000 ) { //5000
		update_user_meta($user_id, '_vk_member_level', 'plantium');
	} else if($totals_user_ordered_after > 3000 ) {
		update_user_meta($user_id, '_vk_member_level', 'gold');
	} else if($totals_user_ordered_after > 1000 ) {
		update_user_meta($user_id, '_vk_member_level', 'silver');	
	} 

}

add_action( 'woocommerce_cart_calculate_fees','vk_discount_order' );
function vk_discount_order() {
	global $woocommerce;
	$user_ID = get_current_user_id();
	if($user_ID == 0) { return; }
	$cart_total =  $woocommerce->cart->cart_contents_total;
	$user_level = get_user_meta($user_ID, '_vk_member_level', true);
	$user_level_html = "(".$user_level.")";
	switch ($user_level) {
		case "plantium":
			$total_discount = $cart_total * 0.20; //20% = 0.2
			$woocommerce->cart->add_fee( 'Discount 20% ', -$total_discount, true, 'standard' );
			break;
		case "gold":
			$total_discount = $cart_total * 0.10; //10% = 0.1
			$woocommerce->cart->add_fee( 'Discount 10% ', -$total_discount, true, 'standard' );
			break;
		case "silver":
			$total_discount = $cart_total * 0.05; //5% = 0.05
			$woocommerce->cart->add_fee( 'Discount 5% ', -$total_discount, true, 'standard' );
			break;
		default:
			$total_discount = $cart_total * 0.00; //not discount
			$woocommerce->cart->add_fee( 'Discount 0% ', -$total_discount, true, 'standard' );
	}
   
   
}

?>
