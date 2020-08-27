
<template>
    <div class="col-md-10"> 
        <div class="container">
            <div class="row">
                <div class="col-md-4" id="app"> 
                    <datepicker  placeholder="Select Year/Month" :minimumView="'month'" :maximumView="'month'" v-model="customDate" v-on:selected="selectedDate()" :format="customFormatter"></datepicker>
                </div>  
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
                customDate: null
            }
        },
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
               var date = moment(this.customDate).format("yyyyMM");
                this.axios
                .get('http://127.0.0.1:8000/api/attendManage/csvOutput')
                .then(response => {
                    //this.holidays = response.data;
                    console.log("Success")
                   //console.log(response.data);
                });
            },


        }
    }

</script>

