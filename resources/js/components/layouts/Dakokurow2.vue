<template>
    <div>   
        <td style="width: 371.05px;padding:0px" :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?'paid-leave1':''" > 
            <template v-if="child_date!='' && child_date.am_leave==1">
                    〇
                <input type="hidden" name="am_leave[]" value="1" class="amleave">
            </template> 
            <template v-else-if="child_date!='' && child_date.am_leave==2">
                    -
                <input type="hidden" name="am_leave[]" value="2" class="amleave">
            </template>    
        </td> 
        <td style="width: 368.05px;padding:0px" :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?'paid-leave2':''" > 
            <template v-if="child_date!='' && child_date.pm_leave==2">
                    -
                <input type="hidden" name="pm_leave[]" value="2" class="pmleave">
            </template>  
            <template v-else-if="child_date!='' && child_date.pm_leave==1">
                    〇
                <input type="hidden" name="pm_leave[]" value="1" class="pmleave">
            </template>  
        </td> 
        <td style="width: 182px;padding:0px">  
            <template v-if="formchange=='edit'">       
              <input v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" :value="child_date.total_hours">
            </template>   
            <template v-else>
                 <input v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" @value="`${old(total_hours)}`">
            </template> 
            <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${(parseInt(dayindex)+1).toString().length==1?'0'+(parseInt(dayindex)+1):(parseInt(dayindex)+1)}`">
        </td> 
        <td style="width: 200px;padding:0px" >  
                    <!-- <template v-if="day[1].length!=0 && key==1"> -->
            <input name="id[]" class="form-control input-sm idx" style="text-align: center;" type="hidden" :value="`${child_date.id?child_date.id:''}`">   
                    <!-- </template>     -->
            <button :id="`autobut${dayindex}`" v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" onclick="this.blur();" type="button" class="btn btn-secondary" @click="childShowTimer()" >自動計算</button>  
        </td> 
        <!-- <div :bay="bay" :style="`width: 371.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`">{{bay}}</div> -->
 
    </div>  
</template>
<script>
export default {
    props:['year','month','dayindex','days','childdate','formchange'],
    data() {

        return {
            prop:['year','month','dayindex','days','childdate','formchange'],
            day:[this.days],
            child_date:this.childdate!=''?this.childdate:'',

        }
    },
    mounted(){
        this.checkBgColor();
    },
    methods: {  
            checkBgColor:function(year,month,dayindex,index){
               
                let custom_date=year+"/"+("0" + month).slice(-2)+("0" +parseInt(dayindex)).slice(-2);                
                let val=this.day[new Date(year+'/'+month+'/'+parseInt(dayindex)).getDay()];
                let cur_date=new Date().getFullYear()+"/"+("0" + parseInt(new Date().getMonth()+1)).slice(-2)+"/"+("0" +new Date().getDate()).slice(-2); 
               
                if(index==1){                
                    return (val!=="Sat" && val!=="Sun" && cur_date>custom_date)? '#FFDAB9' : '';
                }else if(index==true && cur_date>custom_date){
                    return '#FFDAB9';
                }

            },
            childShowTimer() {
            // @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')"
                this.$parent.showTimer(`mainIndex_${this.dayindex}`,`index_${this.dayindex}`,'') // How do I call a parent method?
            }
    } 
}
</script>