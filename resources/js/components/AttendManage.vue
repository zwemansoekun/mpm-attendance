<template>
    <div class="col-md-10"> 
        <div class="container">
            <div class="row">
                <div class="col-md-4" id="app"> 
                    <datepicker class="datepicker1" placeholder="Select Year/Month" :minimumView="'month'" 
                        :maximumView="'month'" v-model="customDate" 
                        v-on:selected="selectedDate()" :format="customFormatter"></datepicker>
                      
                </div>
             
            </div>
             <div class="row">
                 <span v-if="dataError" class="text-danger">
                      There is a data error in data creation. <!--  CSV出力ため、データ作成にはデータエラーがあります。-->
                    </span>
            </div>
        </div>   
    </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker'
import { required, minLength, between } from 'vuelidate/lib/validators'
var moment = require('moment'); 
    export default {
        components: {
            Datepicker
        },
        data: function () {
            return {
                customDate: null, dataError: false
            }
        },
         template: `<div class="checkbox-wrapper" > </div>`,
        methods :{
            customFormatter(date) {
                return moment(date).format('YYYY/MM');
            },
            selectedDate: function(e){
                this.$nextTick(function(){
                    this.returnValue();
                });
            },
            returnValue: function(){
                axios.post(process.env.MIX_APP_URL+'/attendManage/csvOutput/'+ moment(this.customDate).format("yyyyMM"))
                    .then((response) => {
                        console.log(response.data);
                        if(response.data == "fail"){
                            this.dataError = true;
                        }else{
                             const link = document.createElement('a')
                             link.href =process.env.MIX_APP_URL+'/attendManage/download/'+ moment(this.customDate).format("yyyyMM")
                             document.body.appendChild(link)
                             link.click()
                            this.dataError = false
                        }
                });
            },
        }
    }
</script>
