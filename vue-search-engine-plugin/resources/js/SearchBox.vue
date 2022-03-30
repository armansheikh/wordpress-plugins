<template>
    <div id="searchbar">
        <i class="fa fa-search"></i>
        <input
                class="search-field grey-border rounded-border white-bg"
                type="search"
                ref="search"
                value=""
                v-model="searchBox"
                placeholder="OptinMonster"
                @input="emitSearch"
        >
    </div>
</template>

<script>
	import { EventBus } from './event-bus.js';
	export default {
		name: "SearchBox",
		data() {
			return {
				searchBox: ''
			}
		},
		mounted(){
			this.setSearchBoxValue();
			EventBus.$on('event-keyword', (keyword) => {
		    	this.searchBox = keyword;
            });
        },
		methods: {
			setSearchBoxValue() {
				const params = new URLSearchParams(window.location.search);
				if(params.has('keyword')) {
					this.searchBox = params.get('keyword');
				}
			},
			
			emitSearch() {
				this.$emit('search', this.searchBox);
			}
		},
	}
</script>
