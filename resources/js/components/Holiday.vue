
<template>

        <div class="col-md-10"> 
                <div class="container">
                    <div class="row">
                        <div class="col-md-4" id="app"> 
                             <datepicker  :minimumView="'year'" :maximumView="'year'" v-model="customDate" :format="customFormatter" id="dtPicker" v-on:selected="selectedDate()"></datepicker>
                        </div>  
                    </div> 
                </div>   
                
                <div class="container mt-5">
                    <!-- //form -->
                        <div class="row">
                            <div class="col-md-4"> {{customFormatter(customDate)}}</div>
                            <input type="hidden" name="dtInput" :value="customDate">
                        </div>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" ref="btnToggle" @click="addRow(holidays)" :disabled="!btnEnabled" >{{btnText.text}}</button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" v-if="isBtnHidden" @click="copyRow()">前年の物をコピー</button>
                            </div>
                        </div> 
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th scope="col">date</th>
                                <th scope="col">description</th>
                                </tr>
                            </thead>
                            
                            <tbody v-sortable.tr="holidays" id ="tblId" v-if="isRowOne">
                                <tr v-for="holiday in holidays">
                                    <td>
                                        <span v-if="holiday.dtError" class="text-danger">日付をフォーマット(YYYY-MM-DD)で入力してください</span>
                                        <span v-if="holiday.dtDuplicateError" class="text-danger">日付は重複しています</span>
                                        <span v-if="holiday.yearError" class="text-danger">
                                            {{customFormatter(customDate)}}の休日を入力してください
                                    </span>
                                        <input class="form-control" v-model="holiday.date"  type="text"/>
                                    </td>
                                    <td>
                                        <span v-if="holiday.desError" class="text-danger">名称を入力してください</span>
                                        <span v-if="holiday.desDuplicateError" class="text-danger">名称は重複しています</span>
                                        <input class="form-control" v-model="holiday.description" type="text"/>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody  id ="tblId" v-if="isRowTwo">
                                <tr>
                                    <td>
                                        <input class="form-control"  type="text" value=""/>
                                    </td>
                                    <td>
                                        <input class="form-control"  type="text" value=""/>
                                    </td>
                                </tr>

                            </tbody>

                        
                        </table>
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
                customDate: new Date(),holidays: [],seen: true, btnText: {text:'編集'},
                isBtnHidden:false, isRowOne:true, isRowTwo: false, btnEnabled:true,
                subholidays: []
               
            }
        },
        
        created() {
            this.axios
                .get('http://127.0.0.1:8000/api/holidays')
                .then(response => {
                    this.holidays = response.data;
                    
                });
        },
        methods :{
                customFormatter(date) {
                    return moment(date).format('YYYY');
            },
             addRow: function (holidays) {
                try {                                         
                    if( this.$refs.btnToggle.innerText !=='保存'){
                        this.holidays.splice(holidays.length + 1, 0, {});
                        this.seen =  !this.seen;
                        this.$refs.btnToggle.innerText = this.show?'編集': '保存';
                    }else{
                        for(let i=0; i <this.holidays.length; i++){
                             Vue.set(holidays[i],"dtDuplicateError",false);
                             Vue.set(holidays[i],"desDuplicateError",false);
                             Vue.set(holidays[i],"yearError",false);
                            if(this.holidays[i].date === "" || this.holidays[i].date == undefined){
                                Vue.set(holidays[i],"dtError",true);
                            }
                            if(this.holidays[i].description === "" || this.holidays[i].description == undefined){
                                Vue.set(holidays[i],"desError",true);
                            }
                        }
                        // 
                        for(let i=0; i <this.holidays.length; i++){
                          if((holidays[i].date == undefined && holiday[i].dtError == true)
                          || (holidays[i].description == undefined && holiday[i].desError == true)){
                              return this.holidays;
                          }
                        }

                        
                       
                        for (var i = 0; i < holidays.length; i++) {
                            for(var j= i+ 1; j < holidays.length; j++){
                                console.log(holidays[i]["date"]);
                                console.log("j " +holidays[j]["date"]);
                                if (holidays[i]["date"] === holidays[j]["date"]) {
                                    holidays[j]["dtDuplicateError"] = true;
                                }
                                if (holidays[i]["description"] === holidays[j]["description"]) {
                                    holidays[j]["desDuplicateError"] = true;
                                }
                            }
                             holidays[i]["dtError"] = false;
                            holidays[i]["desError"] = false;
                        }

                        for(let i=0; i <this.holidays.length; i++){
                          if(holidays[i].dtDuplicateError == true && holiday[i].desDuplicateError == true){
                              return this.holidays;
                          }
                        }

                        for(let i=0; i <this.holidays.length; i++){
                          if(moment(holidays[i].date).format('YYYY') !== moment(this.customDate).format('YYYY')){
                             holidays[i].yearError = true;
                          }
                           holidays[i]["dtDuplicateError"] = false;
                           holidays[i]["desDuplicateError"] = false;
                        }

                        for(let i=0; i <this.holidays.length; i++){
                          if( holidays[i].yearError == true){
                             return this.holidays;
                          }
                        }

                    console.log("Inholilday" +this.holidays);
                        this.axios
                        .post('http://127.0.0.1:8000/api/holidays', Object.values(this.holidays))
                        .then(response => (
                            this.$refs.btnToggle.innerText = '編集',
                             this.holidays =[],
                             //console.log("holildathis.holidays),
                              this.axios
                                .get('http://127.0.0.1:8000/api/holidays/findYear/'+ moment(this.customDate).format("yyyy"))
                                .then(response => (
                                    
                                    this.holidays = response.data
                                 
                                ))
                        ))
                        .catch(error=> {
                            //console.log(error.response.data)
                        });
                     
                    }
                    
                } catch(e){
                    //console.log(response.data),
                    //this.errors = error.response.data.errors;
                }
            },
            
            selectedDate: function(e){
                this.$nextTick(function(){
                    this.returnValue();
                });
            },
            returnValue: function(){
                console.log("find");
                    this.axios
                    .get('http://127.0.0.1:8000/api/holidays/findYear/'+ moment(this.customDate).format("yyyy"))
                    .then(response => {
                        this.holidays = response.data;
                        if(moment(this.customDate).format("yyyy") !== moment(new Date()).format("yyyy")){
                            this.$refs.btnToggle.innerText = '編集';
                            this.isBtnHidden = true;
                             if(this.holidays.length != 0){
                                this.isRowOne = true;
                                this.isRowTwo = false;
                                
                                this.btnEnabled = true;
                            }else{
                                this.btnEnabled = false;
                                this.isRowOne = false;
                                this.isRowTwo = true;
                                 
                            }

                        }else{
                            this.isRowTwo = true;
                             this.btnEnabled = true;
                             this.isBtnHidden = false;
                             if(this.holidays.length != 0){
                                this.isRowOne = true;
                                this.isRowTwo = false;
                                 
                            }else{
                                this.isRowOne = false;
                                this.isRowTwo = true;
                                 
                            }
                        }
                    });
                },
            copyRow: function () {
                    this.isRowOne = true;
                    this.isRowTwo = false;
                    
                    this.axios
                    .get('http://127.0.0.1:8000/api/holidays/copy')
                    .then(response => {
                        //console.log(response.data);
                        this.holidays = response.data,
                        this.btnEnabled = true;
                    });
                }
        },
        directives: {
            sortable: {
                twoWay: true,
                deep: true
            }
        }
        
  }

</script>
