<template>
    <div class="card-body">
        <img class="deal-pinned-icon" v-if="deal.pinned_deal == 1" :src="root_url+'/wp-content/plugins/im-deals-vue-filters/public/images/pinned.png'" alt="Pinned">
        <a :href="deal.permalink" class="link-promo-img">
            <div class="deal-limited-time-block" v-if="validLimitedTimeDeal(deal.limited_time_deal)"> 
                <span class="deal-limited-time-text" v-text="showLimitedTimeDeal(deal.limited_time_deal)"></span>
             </div>
            <img class="promo-image" :src="deal.promotion_image" :alt="deal.title">
        </a>
        

        
        <a :href="deal.permalink">
            <div class="deal-icon">
                <img alt="logo icon" class="shadow icons-white-stroke" :src="deal.icon">
            </div>
        </a>
   
        <div class="deal-categories">
            <a v-for="category in deal.categories" :href="'javascript:void(0)'" v-text="category.name" @click="addToFilters(category.name)"></a>
        </div>
        <div class="deal-details">
            <div class="offer-by">
                <h3>
                    <a :href="deal.permalink" v-text="deal.offer_by.name" :title="deal.offer_by.name"></a>
                </h3>
            </div>
            <h3 class="title">
                <a :href="deal.permalink" v-text="deal.title" :title="deal.title"></a>
            </h3>
            <p class="short-description" v-text="deal.short_description"></p>
            <p class="deal-count"><span class="green-text">{{deal.deal_count}}</span> used <span v-if="parseInt(deal.popular_deal_count) > 0 && selected_popular_deal !== '' && selected_popular_deal !== 'All'"> - <span class="green-text">{{deal.popular_deal_count}}</span> {{selected_popular_deal}}</span></p>
        </div>
        <div class="deal-button flex-footer">
            <div class="grid-container grid-parent">
                <div class="grid-20 tablet-grid-20 mobile-grid-20 grid-parent fav-icon">
                    <a :href="(parseInt(isli)===0)?redirect_url:'javascript:void(0)'" @click="heartAjax(deal)">
                        <i :class="['icon-heart', (parseInt(deal.deal_saved)>0)?'deal-saved-heart':'', 'circle-icon']"></i>
                    </a>
                </div>
                <div class="grid-80 tablet-grid-80 mobile-grid-80 grid-parent">
                    <button
                            :class="[(deal.is_coupon_deal)?'coupon-deal-button prev-deal' : 'prev-deal']"
                            :data-aff-domain-url="''"
                            :data-icon-img="deal.icon"
                            :data-offer-brand="deal.offer_by.name"
                            :data-promo-img="deal.promotion_image"
                            :data-afftext="''"
                            :data-code="(deal.coupon_code)?deal.coupon_code:'&nbsp;'"
                            :data-link="root_url+'/claim-deals/?dealsid='+deal.deal_id"
                            :data-title="deal.title"
                            data-dealstatus = "save"
                            :data-dealid = "deal.deal_id"
                            :data-desc= "deal.short_description"
                            @click="showCouponCode(deal)"
                    >
                        <span v-if="deal.is_coupon_deal" class="short-coupon-code" v-text="getShortCoupenCode(deal.coupon_code)">&nbsp;</span>
                        <span v-if="deal.is_coupon_deal" class="coupon-code-label">{{deal.button_text}} <i class="icon-right"></i></span>
                        <span v-if="!(deal.is_coupon_deal)">
                            <span v-text="deal.button_text"></span>
                            <i class="icon-right"></i>
                        </span>
                    </button>
                </div>
                <div class="grid-100 text-center save-deal-message">
                    <span :ref="'save-deal-message-deal-id-'+deal.deal_id" :class="['save-deal-message-deal-id-'+deal.deal_id]"></span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
	import { EventBus } from './event-bus.js';
    import moment from 'moment';
	export default {
		name: "Deal",
		props: ['deal', 'root_url', 'redirect_url', 'isli', 'add_to_filters', 'selected_popular_deal'],
		data() {
			return {}
		},
		methods: {
			addToFilters(category_name){
				this.$emit('add-to-filters', {value: category_name})
			},
			heartAjax(deal){
				if(parseInt(this.isli) > 1){
					let el = this.$refs['save-deal-message-deal-id-'+deal.deal_id];
					el.innerHTML = 'Processing...';
					let formData = new FormData();
					formData.append('action','heart_ajax_callback')
					formData.append('task','save_deal')
					formData.append('deal_id',deal.deal_id)
					formData.append('deal_status',(parseInt(deal.deal_saved)>0?'unsave':'save'))
					this.$http.post(ajax_url, formData,
						{
							headers: {
								'Content-Type': 'multipart/form-data'
							}
						})
						.then(response => response.json())
						.then((response) => {
								let _response = JSON.parse(response)
								jQuery('.save-deal-message').show()
								el.innerHTML = _response.deal_save_status;
								location.reload();
							},
							(error) => {
								console.error(error);
							});
				}else
					return false;
			},
			getShortCoupenCode(coupon_code){
				let _coupon_code = '';
				if(coupon_code.length >= 9)
					_coupon_code = coupon_code.substr(coupon_code.length - 8).toUpperCase();
				else
					_coupon_code = coupon_code.toUpperCase();
				return _coupon_code;
			},
			showCouponCode(deal){
				this.$emit('model-affiliate-redirect', deal);
			},
            showLimitedTimeDeal(endTime) {
                const now = moment(new Date()); //todays date
                const end = moment(endTime); // another date
                const duration = moment.duration(end.diff(now));
                const days = duration.asDays().toFixed(0);
                if (days > 1) {
                    return 'Offer ends in '+days+' days';
                }else{
                   const hours = duration.asHours().toFixed(0); 
                   return 'Offer ends in '+hours+' hours';
                }
            },
            validLimitedTimeDeal(endTime) {
                if (!endTime || !moment(new Date()).isBefore(endTime)) return false;

                return true;
            }
		},
	}
</script>
