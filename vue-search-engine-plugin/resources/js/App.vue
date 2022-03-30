<template>
  <div id="imdeals-vue" v-if="sidebar">
    <div v-if="isLoading" class="lds-roller">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
    </div>
    <div class="container">
      <div class="search-panel">
        <div class="search-panel__header">
          <div class="finder">
            <h3>Join <span class="user_live_count">20,000</span> others getting deal alerts</h3>
          </div>
          <div class="ais-Autocomplete">
            <div>
              <SearchBox @search="addSearchText"></SearchBox>
            </div>
          </div>
          <div class="ais-SortBy">
            <div class="select-wrap fa">
              <select
                  :class="'ais-sort-by-select rounded-border'"
                  @change="getSearchResult(false, false, false)"
                  v-model="sortBy"
              >
                <option v-for="item in sortByItems" :value="item.value"
                >{{ item.label }}</option>
              </select>
            </div>
            <div class="fl_tr">Filter</div>

          </div>
        </div>
        <div class="main-search-area">
          <div class="search-panel__filters">
            <i class="fa fa-times txs"></i>
            <button class="apply_filters_now txs">Apply Filters</button>
            <div class="clr_filters_txt">Clear</div>
            <h4 class="apply_filters_txt">Filter By</h4>

            <div class="accordions">
              <div class="accordion-wrap featured-accordion">
                <div class="accordion-header">
                  <label :class="'featured-checkbox lbl'">
                    <input
                        :class="'ais-refinement-list-checkbox'"
                        type="checkbox"
                        :value="'Featured'"
                        @change="featuredChanged"
                        v-model="selected_featured"
                    >
                    <span class="checkmark"></span>
                  </label>
                  <h3 class="green-text">Featured &nbsp;
                    <span class="green-text">({{feature_count}})</span>

                    <img class="cat-feature-img" :src="root_url+'/wp-content/plugins/im-deals-vue-filters/public/images/featured-fire-icon.png'" alt="Featured">
                  </h3>
                </div>
              </div>
              <div class="accordion-wrap category-accordion">
                <div class="accordion-header" v-show="!categoriesAccordionOpen" @click="toggleCategoriesAccordion">
                  <i class="accordion-arrow green-text fa fa-angle-down"></i>
                  <h3 class="green-text">Categories&nbsp;&nbsp;&nbsp;</h3>
                </div>
                <div class="accordion-body" v-show="categoriesAccordionOpen && sidebar" >
                  <div class="ais-RefinementList">
                    <div class="filter-wrapper">
                      <div class="widget-heading-wrap" @click="toggleCategoriesAccordion">
                        <h4 class="widget-heading">Categories</h4>
                        <i class="accordion-arrow fa fa-angle-up"></i>
                      </div>
                      <ul :class="'ais-refinement-list-list'">
                        <template
                            v-for="(item, index) in sidebar.categories"
                        >
                          <li
                              :key="item.term_id"
                              :class="['ais-refinement-list-item']"
                              v-if="((categories.isShowingLess && (index+1) <= categories.limit) || !categories.isShowingLess)"
                          >
                            <label :class="'ais-refinement-list-label lbl'">
                              <input
                                  :class="'ais-refinement-list-checkbox'"
                                  type="checkbox"
                                  :value="item.name"
                                  @change="getSearchResult(false, false, false)"
                                  v-model="selected_categories"
                              >
                              <span class="checkmark"></span>
                              <div
                                  :class="'ais-refinement-list-labelText'"
                              >{{item.name}} </div>
                              <div class="green-text category-count">({{item.count}})</div>
                            </label>
                          </li>
                        </template>
                      </ul>
                      <!-- <button
                          :class="[
                                                            'ais-refinement-list-show-more'
                                                        ]"
                          v-if="categories.showMore && sidebar.categories.length > categories.limit"
                      >
                        <div :class="['show-more-text']">
                                                    <span v-if="!categories.isShowingLess"  @click="categoryView('less')">
                                                        View less categories&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-up"></i>
                                                    </span>
                          <span v-else @click="categoryView('all')">
                                                        View all categories&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                                    </span>
                        </div>
                      </button> -->
                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-wrap deal-type-accordion">
                <div class="accordion-header" v-show="!dealTypesAccordionOpen" @click="toggleDealTypesAccordion">
                  <i class="accordion-arrow green-text fa fa-angle-down"></i>
                  <h3 class="green-text">Deal Types&nbsp;&nbsp;&nbsp;</h3>
                </div>
                <div class="accordion-body" v-show="dealTypesAccordionOpen">
                  <div class="ais-RefinementList">
                    <div class="filter-wrapper">
                      <div class="widget-heading-wrap" @click="toggleDealTypesAccordion">
                        <h4 class="widget-heading">Deal Types</h4>
                        <i class="accordion-arrow fa fa-angle-up"></i>
                      </div>
                      <ul :class="'ais-refinement-list-list'">
                        <template v-for="(item, index) in sidebar.deal_types">
                          <li
                              v-show="item !== 'Featured'"
                              :class="['ais-refinement-list-item', item.isRefined ? 'ais-refinement-list-item-selected' : '']"
                              :key="item"
                          >
                            <label :class="'ais-refinement-list-label lbl'">
                              <input
                                  :class="'ais-refinement-list-checkbox'"
                                  type="checkbox"
                                  :value="item"
                                  @change="getSearchResult(false, false, false)"
                                  v-model="selected_deal_types"
                              >
                              <span class="checkmark"></span>
                              <span
                                  :class="'ais-refinement-list-labelText'"
                              >{{item}} <span class="green-text">({{sidebar.deal_types_count[item]}})</span></span>
                              <span class="cat-feature-img-wrap" v-show="item === 'Featured'">
                                                            <img class="cat-feature-img" :src="root_url+'/wp-content/plugins/im-deals-vue-filters/public/images/featured-fire-icon.png'" alt="Featured">
                                                        </span>
                            </label>
                          </li>
                        </template>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-wrap top-deals-accordion">
                <div class="accordion-header" v-show="!topDealsAccordionOpen" @click="toggleTopDealsAccordion">
                  <i class="accordion-arrow green-text fa fa-angle-down"></i>
                  <h3 class="green-text">Most Popular&nbsp;&nbsp;&nbsp;<i class="fa fa-bolt"></i></h3>
                </div>
                <div class="accordion-body" v-show="topDealsAccordionOpen">
                  <div class="ais-RefinementList">
                    <div class="filter-wrapper">
                      <div class="widget-heading-wrap" @click="toggleTopDealsAccordion">
                        <h4 class="widget-heading">Most Used</h4>
                        <i class="accordion-arrow fa fa-angle-up"></i>
                      </div>
                      <ul :class="'ais-refinement-list-list'">
                        <li
                            :class="['ais-refinement-list-item']"
                            v-for="(item, index) in sidebar.most_popular_deals"
                            :key="item"
                        >
                          <label :class="'ais-refinement-list-label lbl'">
                            <input
                                :class="'ais-refinement-list-checkbox'"
                                type="checkbox"
                                :value="item"
                                v-model="selected_popular_types"
                                @change="getSearchResult(false, false, true)"
                            >
                            <span class="checkmark"></span>
                            <span
                                :class="'ais-refinement-list-labelText'"
                            >{{item}} <span class="green-text">({{sidebar.most_popular_deals_count[item]}})</span></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="search-panel__results">
            <div class="middle-bar">
              <div class="selected-categories">
                <span v-if="appliedFiltersLength" class="dark-grey-text applied-label">Applied filters: </span>
                <template v-for="(category, index) in selected_categories">
                                    <span class="checked-category green-text">
                                        <a :href="'javascript:void(0)'">{{category}}</a>
                                        <i @click="removeFilter('category', index)" class="fa fa-times"></i>
                                    </span>
                </template>
                <template v-for="(deal_type, index) in selected_deal_types">
                                    <span class="checked-category green-text">
                                        <a :href="'javascript:void(0)'">{{deal_type}}</a>
                                        <i @click="removeFilter('deal_type', index)" class="fa fa-times"></i>
                                    </span>
                </template>
                <template v-for="(popular_type, index) in selected_popular_types">
                                    <span class="checked-category green-text">
                                        <a :href="'javascript:void(0)'">{{popular_type}}</a>
                                        <i @click="removeFilter('popular_type', index)" class="fa fa-times"></i>
                                    </span>
                </template>
                <template v-if="appliedFiltersLength">
                                    <span class="checked-category green-text" @click="removeAllFilters()">
                                        Clear all
                                    </span>
                </template>
              </div>
              <div class="total-results">
                <span class="dark-grey-text">Showing</span>
                <span class="green-text">
                                    {{ totalDealsFound }}
                                    results
                                </span>
              </div>
            </div>
            <ol class="grid im-deals" v-if="dealTiles.length > 0 || pinned_deals.length > 0">
              <li v-for="pinnedDealTile in pinned_deals" class="col-12 col-md-6 col-lg-4 col-sm-6 col-xs-12 col-grid" v-if="!appliedFiltersLength && !keyword">
                <Deal :deal="pinnedDealTile" :root_url="root_url" :redirect_url="redirect_url" :isli="isli" @model-affiliate-redirect="modelAffiliateRedirect" />
              </li>
              <li v-for="dealTile in dealTiles" class="col-12 col-md-6 col-lg-4 col-sm-6 col-xs-12 col-grid">
                <Deal :deal="dealTile" :root_url="root_url" :redirect_url="redirect_url" :isli="isli" @add-to-filters="addToFilters" :selected_popular_deal="selectedPopularDeal" @model-affiliate-redirect="modelAffiliateRedirect" />
              </li>
            </ol>
            <div v-if="canShowMore"
                 :class="['show-more-results-wrap']"
            >
              <button
                  :class="['show-more-results ais-infinite-hits-loadMore']"
                  @click="showMorePage"
              >
                Show more results
              </button>
              <scroll-loader :loader-method="showMorePage"></scroll-loader>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Deal from "./Deal";
import SearchBox from "./SearchBox";
import { EventBus } from './event-bus.js';


export default {
  components: {SearchBox, Deal},
  data() {
    return {
      feature_count : 0,
      category_name: category_name,
      root_url: home_url,
      isli: isli,
      redirect_url: redirect_url,
      categoriesAccordionOpen: true,
      topDealsAccordionOpen: true,
      isLoading: true,
      dealTypesAccordionOpen: true,
      queryString: '?sort_by=%SORT%&keyword=%KEYWORD%&page=%PAGE%&page_limit=%PAGE_LIMIT%&categories=%CATEGORIES%&deal_types=%DEAL_TYPES%&popular_deals=%POPULAR_DEALS%',
      sidebarQueryString: '?categories=%CATEGORIES%&deal_types=%DEAL_TYPES%&popular_deals=%POPULAR_DEALS%',
      dealTiles: [],
      sidebar: {
        'categories': []
      },
      sortByItems: [
        { value: 'post_date', label: 'Sort By: Latest' },
        { value: 'claim_count', label: 'Sort By: Most Popular' },
      ],
      sortBy: 'post_date',
      categories: {
        limit: 8,
        classNames: '',
        showMore: true,
        showMoreLimit: 10,
        isShowingLess: false,
        searchable: false,
        attribute: "categories.name",
        searchablePlaceholder: 'Search categories...',
        searchForFacetValues: '',
      },
      deal_type: {
        limit: 8,
        classNames: '',
        showMore: false,
        showMoreLimit: 10,
        searchable: false,
        attribute: "deal_type",
        searchablePlaceholder: 'Search deal type...',
        searchForFacetValues: '',
      },
      selected_featured: false,
      selected_categories: [],
      selected_deal_types: [],
      selected_popular_types: [],
      keyword: '',
      totalDealsFound: 0,
      perPage: 12,
      currentPage: 1,
      totalPages: 1,
      url_categories:     url_categories,
      url_popular_deals:  url_popular_deals,
      url_deal_types:     url_deal_types,
      url_most_popular:   url_most_popular,
      url_deal_type:      url_deal_type,
      pinned_deals: []
    };
  },
  computed: {
    urlCategories(){
      return this.url_categories.length > 0;
    },
    urlPopularDeals(){
      return this.url_popular_deals.length > 0;
    },
    urlDealTypes(){
      return this.url_deal_types.length > 0;
    },
    canShowMore(){
      return this.totalPages > this.currentPage;
    },
    appliedFiltersLength(){
      return (this.selected_categories.length > 0 || this.selected_deal_types.length > 0 || this.selected_popular_types.length > 0);
    },
    selectedPopularDeal(){
      return ((this.selected_popular_types.length>0)?this.selected_popular_types[0]:'');
    },
    isCategoryURL(){
      return this.category_name !== '' && this.category_name !== undefined && this.category_name !== null;
    },
    mostPopular(){
      return this.url_most_popular !== '' && this.url_most_popular !== undefined && this.url_most_popular !== null;
    },
    dealType(){
      return this.url_deal_type !== '' && this.url_deal_type !== undefined && this.url_deal_type !== null;
    }
  },
  beforeMount(){
    this.getCategories();
    this.getPinnedDeals();
    this.getData();
  },
  mounted(){
    const params = new URLSearchParams(window.location.search);
    if(params.has('keyword')) {
      this.keyword = params.get('keyword');
    }

    this.selected_featured = (this.url_deal_types.includes('Featured')) ? true : false;
  },
  watch: {
    selected_popular_types: function (newValue, oldValue) {
      if(this.selected_popular_types.length > 1){
        this.selected_popular_types = [this.selected_popular_types[this.selected_popular_types.length - 1]];
      }
    }
  },
  methods: {
    getQueryParamValueOf(param) {
      let output = [];
      let queryParams  = new URLSearchParams(window.location.search);
      if(queryParams.has(param)) {
        output = queryParams.get(param).split(',')
      }

      return output;
    },
    featuredChanged(){
      if(this.selected_featured){
        if (!this.selected_deal_types.includes('Featured')){
          this.selected_deal_types.push('Featured');
        }
      }else{
        let featuredIndex = this.selected_deal_types.indexOf('Featured');
        if(featuredIndex !== -1){
          this.selected_deal_types.splice(featuredIndex, 1);
        }
      }
      this.getSearchResult(false, false, false);
    },
    modelAffiliateRedirect(deal){
      //redirect to detail page if it is expired
      if (deal.is_expired == 1)
      {
        let deal_detail_url = deal.permalink;
        window.open(deal_detail_url, '_self');
        return;
      }

      let deal_target_url = this.root_url+'/claim-deals/?dealsid='+deal.deal_id;
      let deal_deal_id = deal.deal_id;
      let deal_coupon_title = deal.title;
      let deal_coupon_code = deal.coupon_code;
      let deal_promotion_image = deal.promotion_image;
      let deal_icon_image = deal.icon;
      let deal_offer_brand = deal.offer_by.name;
      // data related cookies
      Cookies.set('deal_current_page', parseInt(this.currentPage));
      Cookies.set('deal_per_page', parseInt(this.perPage));
      Cookies.set('deal_keyword', this.keyword);
      Cookies.set('deal_sort_by', this.sortBy);
      Cookies.set('deal_deal_id', parseInt(deal_deal_id));
      Cookies.set('deal_target_url', deal_target_url);
      Cookies.set('deal_coupon_title', deal_coupon_title);
      Cookies.set('deal_coupon_code', deal_coupon_code);
      Cookies.set('deal_promotion_image', deal_promotion_image);
      Cookies.set('deal_icon_image', deal_icon_image);
      Cookies.set('deal_offer_brand', deal_offer_brand);
      Cookies.set('deal_show_modal_coupon', 'true');
      let deal_current_url = location.protocol + '//' + location.host + location.pathname;
      let queryParams = this.sidebarQueryString;
      sidebarQueryString: '?categories=%CATEGORIES%&deal_types=%DEAL_TYPES%&popular_deals=%POPULAR_DEALS%',
          queryParams = queryParams.replace('%CATEGORIES%', JSON.stringify(this.selected_categories));
      queryParams = queryParams.replace('%DEAL_TYPES%', JSON.stringify(this.selected_deal_types));
      queryParams = queryParams.replace('%POPULAR_DEALS%', this.selectedPopularDeal);
      deal_current_url = deal_current_url+queryParams;
      window.open(deal_current_url, '_blank');
      window.open(deal_target_url, '_self');
    },
    getCookieData: function(){
      if(Cookies.get('deal_show_modal_coupon') == 'true') {
        jQuery('.coupon-title').html(Cookies.get('deal_coupon_title'));
        jQuery('.coupon-code-box-content').html(this.getCouponBoxContent(Cookies.get('deal_coupon_code')));
        jQuery(".promotion-image").attr("src", Cookies.get('deal_promotion_image'));
        jQuery(".coupon-target-url a").attr("href", this.root_url+'/claim-deals/?dealsid=' + Cookies.get('deal_deal_id'));
        jQuery(".icon-image").attr("src", Cookies.get('deal_icon_image'));
        jQuery('.offer-brand').html(Cookies.get('deal_offer_brand'));
        jQuery('.coupon.ui.modal').modal({ centered: false }).modal('show');
        Cookies.set('deal_show_modal_coupon', 'false');
        if(Cookies.get('deal_current_page') !== '' && Cookies.get('deal_current_page') !== null && Cookies.get('deal_current_page') !== undefined )
          this.currentPage = parseInt(Cookies.get('deal_current_page'));
        if(Cookies.get('deal_per_page') !== '' && Cookies.get('deal_per_page') !== null && Cookies.get('deal_per_page') !== undefined )
          this.perPage = parseInt(Cookies.get('deal_per_page'));
        if(Cookies.get('deal_keyword') !== '' && Cookies.get('deal_keyword') !== null && Cookies.get('deal_keyword') !== undefined ){
          this.keyword = Cookies.get('deal_keyword');
          this.$nextTick(() => {
            EventBus.$emit('event-keyword', this.keyword);
          })
        }
        if(Cookies.get('deal_sort_by') !== '' && Cookies.get('deal_sort_by') !== null && Cookies.get('deal_sort_by') !== undefined )
          this.sortBy = Cookies.get('deal_sort_by');
        this.setFilters();
        this.getSearchResult(false, true, false);
      }else{
        this.setFilters();
        this.getSearchResult(false, false, false);
      }
    },
    setFilters(){
      if(this.urlCategories && this.urlCategories != "")
        this.selected_categories = this.url_categories;
      if(this.urlPopularDeals)
        this.selected_popular_types = this.url_popular_deals;
      if(this.urlDealTypes)
      {
        this.selected_deal_types = this.url_deal_types;
      }
      if(this.selected_categories.length > 0)
        this.categoriesAccordionOpen = true;
      if(this.selected_deal_types.length > 0)
        this.dealTypesAccordionOpen = true;
      if(this.selected_popular_types.length > 0)
        this.topDealsAccordionOpen = true;
    },
    getCouponBoxContent(couponcode) {
      var returnstring = '';
      if(couponcode == '')
        returnstring = '<div class="coupon-code-wrapper-nocode"><div class="c1-content-left"><img src="https://internetmarketingdeals.com/wp-content/uploads/2020/07/deal-activated.jpg" alt="tick" /></div><div class="c2-content-right">Deal activated <br>No coupon code required!</div></div>';
      else
        returnstring = '<div class="coupon-code-wrapper"><span id="coupon-code-text">'+ couponcode + '</span><a href="#" class="button clipboard-coupon-copy" onclick="copyCodeToClipboard()">Copy</a></div>';
      return returnstring;
    },
    getData(){
      this.setURLData();
    },
    setURLData: function(){
      if(this.isCategoryURL){
        this.selected_categories = [this.category_name];
        this.categoriesAccordionOpen = true;
      }
      if(this.mostPopular){
        this.selected_popular_types = this.url_most_popular;
        this.topDealsAccordionOpen = true;
      }
      if(this.dealType){
        this.selected_deal_types = [this.url_deal_type];
        this.dealTypesAccordionOpen = true;
      }
      if(this.urlCategories){
        this.selected_categories = this.url_categories;
        this.categoriesAccordionOpen = true;
      }
      if(this.urlDealTypes){
        this.selected_deal_types = this.url_deal_types;
        this.dealTypesAccordionOpen = true;
      }
      if(this.urlPopularDeals){
        this.selected_popular_types = this.url_popular_deals;
        this.topDealsAccordionOpen = true;
      }
      this.getCookieData();
    },
    categoryView: function(value){
      if(value === 'all')
        this.categories.isShowingLess = false;
      else if(value === 'less')
        this.categories.isShowingLess = true;
    },
    showMorePage: function(){
      if(this.canShowMore){
        ++this.currentPage;
        this.getSearchResult(true, false, false);
      }
    },
    addToFilters: function(category_name){
      if (!this.selected_categories.includes(category_name.value)){
        this.selected_categories.push(category_name.value);
        this.getSearchResult(false, false, false);
      }
    },
    removeAllFilters: function() {
      this.selected_categories = [];
      this.selected_deal_types = [];
      this.selected_popular_types = [];
      this.selected_featured = false;
      this.sortBy = 'post_date';
      this.getSearchResult(false, false, false);
    },
    removeFilter: function(widget, index) {
      if(widget === 'category')
        this.selected_categories.splice(index, 1);
      else if(widget === 'deal_type'){
        if(this.selected_deal_types[index] === 'Featured')
          this.selected_featured = false;
        this.selected_deal_types.splice(index, 1);
      }
      else if(widget === 'popular_type')
        this.selected_popular_types.splice(index, 1);
      this.getSearchResult(false, false, false);
    },
    addSearchText: function(data) {
      this.keyword = data;
      this.getSearchResult(false, false, false)
    },
    getResults(params = null, fromShowMoreBtn){
      let self = this;
      let url = '';
      this.isLoading = true;
      if (params)
        url= this.root_url + '/wp-json/rest/search/deals'+params;
      else
        url= this.root_url + '/wp-json/rest/search/deals';
      this.$http.get(url).then((response) => {
            this.totalDealsFound    = parseInt(response.body.total_records);
            this.currentPage        = parseInt(response.body.page);
            this.totalPages         = parseInt(response.body.total_pages);
            if(fromShowMoreBtn)
              this.assignDealTiles(response.body.data, self);
            else
              this.dealTiles = response.body.data;
            this.isLoading          = false;

            if(url.includes('?')) {
              params = url.split('?');
              params = decodeURIComponent(params[1]).split('&').map( (param, index) => {
                param = param.split('=');
                for(let i = 0; i < param.length; i++) {
                  if( param[0] == 'page' ||
                      param[0] == 'page_limit' ||
                      param[0] == 'sort_by' ||
                      param[1] == '' ||
                      param[1]== '[]' ||
                      param[1] == '\"\"'
                  ) {
                    continue;
                  }

                  // URL query params formated code
                  if(typeof param[1] != 'undefined' && param[1][0] == "[") {
                    param[1] = param[1].replace(/[\[\]']+/g,'');
                    param[1] = param[1].replace(/['"]+/g, '')
                  }

                  return param.join('=');
                }
              }).filter((param) => {
                return param !== undefined;
              });

              let nextURL = this.root_url;
              let paramLen = params.join('&').length;
              if( paramLen > 0) {
                nextURL = this.root_url + '?' + params.join('&');
              }

              let nextTitle = 'filtered-deals';
              let nextState = { additionalInformation: 'Updated the URL with JS' };
              window.history.pushState(nextState, nextTitle, nextURL);
              window.history.replaceState(nextState, nextTitle, nextURL);
            }

          },
          (error) => {
            console.error(error);
            this.isLoading = false;
          },
      );
    },
    assignDealTiles: function(deals, self) {
      if(self.dealTiles.length === 0){
        self.dealTiles = deals;
      }else{
        if(deals.length > 0){
          let repeatingDeals = [];
          let repeatingDealIds = [];
          deals.forEach(function (deal) {
            if(!(self.dealTiles.filter(function(e) { return parseInt(e.deal_id) === parseInt(deal.deal_id); }).length === 0)){
              repeatingDeals.push(deal);
              repeatingDealIds.push(deal.deal_id);
            }
            // if (self.dealTiles.filter(function(e) { return parseInt(e.deal_id) === parseInt(deal.deal_id); }).length === 0) {
            self.dealTiles.push(deal);
            // }
          })
          if(repeatingDeals.length > 0){
            console.log('Repeating Deals');
            console.log(repeatingDeals);
          }
          if(repeatingDealIds.length > 0){
            console.log('Repeating Deal Ids');
            console.log(repeatingDealIds);
          }
        }
      }
    },
    getCategories(){
      let self = this;
      let url = window.location.origin + "/wp-json/rest/deals/filters";
      this.$http.get(url).then((response) => {
            this.sidebar = response.body.data;
            if(typeof this.sidebar.deal_types_count['Featured'] != undefined) {
              this.feature_count = this.sidebar.deal_types_count['Featured'];
            }
          },
          (error) => {
            console.error(error);
          });
    },
    getSearchResult(fromShowMoreBtn, fromCookie, checkMostPopularType){
      this.$nextTick(() => {
        if(checkMostPopularType)
          this.sortBy = 'claim_count';
        let queryParams = this.queryString;

        let pageLimit = ((fromCookie)?(this.perPage * this.currentPage):this.perPage);
      
        queryParams = queryParams.replace('%SORT%', this.sortBy);
        queryParams = queryParams.replace('%PAGE%', ((fromCookie)?1:((fromShowMoreBtn)?this.currentPage:1)));
        queryParams = queryParams.replace('%PAGE_LIMIT%', pageLimit);
        queryParams = queryParams.replace('%CATEGORIES%', JSON.stringify(this.selected_categories));
        queryParams = queryParams.replace('%DEAL_TYPES%', JSON.stringify(this.selected_deal_types));
        queryParams = queryParams.replace('%POPULAR_DEALS%', this.selectedPopularDeal);
        queryParams = queryParams.replace('%KEYWORD%', this.keyword);

        this.getResults(queryParams, fromShowMoreBtn);
      });
    },
    toggleCategoriesAccordion(){
      this.categoriesAccordionOpen = !this.categoriesAccordionOpen;
      this.categories.isShowingLess = false;
    },
    toggleTopDealsAccordion(){
      this.topDealsAccordionOpen = !this.topDealsAccordionOpen;
    },
    toggleDealTypesAccordion(){
      this.dealTypesAccordionOpen = !this.dealTypesAccordionOpen;
    },
    getPinnedDeals(){
      let self = this;
      let url = window.location.origin + "/wp-json/rest/deals/pinned";
      this.$http.get(url).then((response) => {
            self.pinned_deals = response.body.data;
          },
          (error) => {
            console.error(error);
          });
    }
  }
};
</script>
