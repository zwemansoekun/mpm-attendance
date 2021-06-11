
<template>
    <div class="col-md-12 py-4"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title"> Attendance Management Output </h4>
                    <hr class="underline_title">
                </div>
                <div class="col-md-4" id="app"> 
                    <datepicker class="datepicker1" placeholder="Select Year/Month" :minimumView="'month'" 
                        :maximumView="'month'" v-model="customDate" 
                        v-on:selected="selectedDate()" :format="customFormatter"></datepicker>
                      
                </div>
             
            </div>
             <div class="row">
                 <span v-if="dataError" class="err_msg text-danger">
                         There is a data error in data creation. <!--  CSV出力ため、データ作成にはデータエラーがあります。-->
                    </span>
            </div>
        </div>   
    </div>
</template>
<style>

    /* .datepicker1 input[disabled]{
        color:black;
    } */
    .datepicker1 input::placeholder {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size:15px;
        color: black;
    }
    .datepicker1 input {
          background-color: #fff;
          height: 50px;
          width: 100%;
          padding: 12px 20px;
          margin: 0px 0;
          display: inline-block;
          border: 1px solid gray;
          border-radius: 4px;
          box-sizing: border-box;
          color:black;
          font-size: 17px;
    }

    .datepicker1 input:hover {
        background-color: silver;
        color:white;
        font-size: 17px;
        cursor: pointer;
    }

    .err_msg {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 20px;
        padding-left: 20px;
        text-align: center;
    }
</style>
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
                axios.post(process.env.MIX_APP_URL+'/api/attendManage/csvOutput/'+ moment(this.customDate).format("yyyyMM"))
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

