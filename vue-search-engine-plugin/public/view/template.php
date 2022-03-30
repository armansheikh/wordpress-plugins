<?php
$enable_banner = get_field('enable_top_bannr', 'option');
$deal_month_link = get_field('deal_of_the_month', 'option');
$deal_month_text = get_field('deal_of_the_month_text', 'option');
$deal_useful_link = get_field('useful_resource', 'option');
$deal_useful_text = get_field('useful_resource_text', 'option');
$deal_trending_text = get_field('trending_deal_text', 'option');
$icon = get_field('icon', $deal_month_link);
global $wpdb;
$trendingdeal_id = $wpdb->get_var("SELECT a.deal_id FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and MONTH(a.deal_claimed_date) = MONTH(CURDATE()) AND YEAR(a.deal_claimed_date) = YEAR(CURDATE()) group by a.deal_id order by sum(a.deal_count) DESC LIMIT 0,1");
$icon_trending = get_field('icon', $trendingdeal_id);
?>	

<?php
	$placeholder_arr = array();
	// check if the repeater field has rows of data
	if( have_rows('search_box_placeholders', 'option') ):

		// loop through the rows of data
		while ( have_rows('search_box_placeholders', 'option') ) : the_row();

			// display a sub field value
			$placeholder_arr[] = get_sub_field('placeholder_text');

		endwhile;

	endif;
?>
<script type='text/javascript' src='https://internetmarketingdeals.com/wp-content/plugins/im-deals-core-functionality/public/js/placeholderTypewriter.js'></script>
<script>
  var placeholderText = <?php echo json_encode($placeholder_arr); ?>;

  jQuery( document ).ready(function() {
		jQuery('.search-field').placeholderTypewriter({text: placeholderText});
	});
	
</script>

<?php if($enable_banner ) {?>
<div class="banner-bar-show">
<div class="grid">
<div class="col-md-12">
<div class="tp_heading_imd"><span>Save Money</span> with Exclusive Internet Marketing Deals and Coupon Codes</div>
</div>
</div>
</div>
<div class="hm_grid_new dsp_desktop">
<div class="grid">

<div class="col-md-4">
<div class="wrap_imd_box">
<div class="imd_deal_box">
<div class="imd_dv-content">
<h3><a href="<?php echo get_permalink($deal_month_link)?>"><img src="<?php echo get_stylesheet_directory_uri();?>/icons/money.png" alt="deal_icon"><?php echo $deal_month_text?></a></h3>
</div>

</div>
</div>
<div class="img-deal-brand">
<a href="<?php echo get_permalink($deal_month_link)?>"><img src="<?php echo $icon;?>" alt="deal_icon icons-white-stroke"></a>
</div>
</div>

<div class="col-md-4">
<div class="wrap_imd_box">

<div class="imd_deal_box">
<div class="imd_dv-content">
<h3><a href="<?php echo get_permalink($trendingdeal_id)?>"><img src="<?php echo get_stylesheet_directory_uri();?>/icons/stocks.png" alt="deal_icon"> <?php echo $deal_trending_text?></a></h3>
</div>

</div>
</div>
<div class="img-deal-brand">
<a href="<?php echo get_permalink($trendingdeal_id)?>"><img src="<?php echo $icon_trending;?>" alt="deal_icon icons-white-stroke"></a>
</div>
</div> 

<div class="col-md-4">
<div class="wrap_imd_box">

<div class="imd_deal_box">
<div class="imd_dv-content">
<h3><a href="<?php echo $deal_useful_link?>">🔑 <?php echo $deal_useful_text?></a></h3>
</div>

</div>
</div>
<div class="img-deal-brand userful-resource-img">
<a href="#"><img src="<?php echo get_field('useful_resource_image', 'option');?>" alt="deal_icon icons-white-stroke"></a>
</div>
</div>

</div> 
</div>



<div class="hm_grid_new dsp_mbl">
<div class="grid">
<div class="owl-carousel owl-theme owl_deals_top">
<div class="item">
<?php if ( is_user_logged_in() ) { ?>
<div class="btn-hold fb_hold_btn_tp"><a href="https://www.facebook.com/IMDealsAlerts" target="_blank">Join our free internet marketing community now</a></div>
<?php } else { ?>
<div class="btn-hold"><a href="https://internetmarketingdeals.com/account">Join <span class="user_live_count">2784</span> others getting deal alerts</a></div>
<?php } ?>
</div>


<div class="item">
<div class="wrap_imd_box">
<div class="imd_deal_box">
<div class="imd_dv-content">
<h3><a href="<?php echo get_permalink($deal_month_link)?>"><img src="<?php echo get_stylesheet_directory_uri();?>/icons/money.png" alt="deal_icon"> <?php echo $deal_month_text?></a></h3>
</div>
</div>
</div>
<div class="img-deal-brand">
<a href="<?php echo get_permalink($deal_month_link)?>"><img src="<?php echo $icon;?>" alt="deal_icon"></a>
</div>
</div>



<div class="item">
<div class="wrap_imd_box">

<div class="imd_deal_box">
<div class="imd_dv-content">
<h3><a href="<?php echo get_permalink($trendingdeal_id)?>"><img src="<?php echo get_stylesheet_directory_uri();?>/icons/stocks.png" alt="deal_icon"> <?php echo $deal_trending_text?></a></h3>
</div>

</div>
</div>
<div class="img-deal-brand">
<a href="<?php echo get_permalink($trendingdeal_id)?>"><img src="<?php echo $icon_trending;?>" alt="deal_icon"></a>
</div>
</div> 

<div class="item">
<div class="wrap_imd_box wrp_55">

<div class="imd_deal_box">
<div class="imd_dv-content">
<h3><a href="<?php echo $deal_useful_link?>"><img src="<?php echo get_stylesheet_directory_uri();?>/icons/books.png" alt="deal_icon"> <?php echo $deal_useful_text?></a></h3>
</div>
</div>
</div>

</div> 
</div>
</div>
</div>
<?php } ?>

<?php
$source_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
<script type="text/javascript">
	function  getQueryParamValueOf(param) {
                let output = [];
                let queryParams  = new URLSearchParams(window.location.search);
                if(queryParams.has(param)) {
                    debugger;
                    output = queryParams.get(param) ? queryParams.get(param).split(',') : '';
                    if (output != '') {
                        for(var indexParam=0; indexParam < output.length; indexParam++) {
                            output[indexParam] = removeStringChar(output[indexParam]);
                        }
                        if (output.length > 0)
                            output = output[0] == '' ? [] : output;
                    }
                }

                return output;
    }

    function removeStringChar(str)
    {
        return str.replace(/\\/g, '').replace(/\[/g, '').replace(/\"/g, '').replace(/\]/g, '')
    }

	const isli = "<?=intval(get_current_user_id())?>";
	const redirect_url = "<?php echo home_url() . '/login/?redirect_url=' . $source_url?>";
	const home_url = "<?php echo get_home_url() ?>";
	const category_name = '<?=htmlspecialchars($_GET["category_id"])?>';
	const url_categories    = getQueryParamValueOf('categories');
	const url_deal_types    = getQueryParamValueOf('deal_types');
	const url_popular_deals    = getQueryParamValueOf('popular_deals');
	const url_most_popular  = '<?=htmlspecialchars($_GET["most_popular"])?>';
	const url_deal_type     = '<?=htmlspecialchars($_GET["deal_type"])?>';
</script>

<div class="container" id="vue-instant-search-app"></div>

<div class="scrolltop_deal home_sc_top">
<span>Scroll to top</span>
<i class="fas fa-chevron-up"></i>
</div>
<script>
jQuery(document).ready(function($) {
$('.owl_deals_top').owlCarousel({
    loop:true,
    margin:20,
    nav:false,
	dots: true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:3
        }
    }
});
});
</script>