Component({
	options: {
		multipleSlots: false // 在组件定义时的选项中启用多slot支持
	},
	properties: { 
		cities:[],
		areas:[],
		city:{},
		area:{},
		value:[0,0],
	},
	methods: {
		_getCities:function(){
			let that = this
			request.post("part/district/all",function(data){
				that.setData({
					cities:data.cities
				})
			})
		},
		_change:function(e){
			let val = e.detail.value,
				cities = this.data.cities,
				areas = this.data.areas,
				city = cities[val[0]],
				area = areas[val[1]];
			if(city.cid != this.data.cid){
				this.setData({
					city:city,
					areas:city.areas
				})
			}else if(area.aid!=this.data.aid){
				this.setData({area:area})
			}
		},
		_cancel:function(){

		},
		_submit:function(){
			
		}
	}
})