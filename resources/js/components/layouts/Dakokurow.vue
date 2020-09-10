<template>
  <div>                                       
        <td :style="`width: 371.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`"  > 
        </td>   
    
        <td :style="`width: 368.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`" > 
        </td>
        
        <td style="width: 182px;height:50px;">
        </td>  

        <td style="width: 200px;height:50px ;" >
        </td>                                                                      
      
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
                    return (val!=="Sat" && val!=="Sun" && cur_date>custom_date)? '#FBE5D6' : '';
                }else if(index==true && cur_date>custom_date){
                    return '#FBE5D6';
                }

            },
            childShowTimer() {
            // @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')"
                this.$parent.showTimer(`mainIndex_${this.dayindex}`,`index_${this.dayindex}`,'') // How do I call a parent method?
            }
    } 
}
</script>