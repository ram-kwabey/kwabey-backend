<template>
    <div>
        <div v-if="data.deleted_at == null" class="btn-group">
            
                <a class="link text-primary mx-1"  href="javascript:void(0)" title="Edit Appointment" @click.stop="edit(data.id,data)">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <a class="link text-danger mx-1"  href="javascript:void(0)" title="Delete Appointment" v-if="canDeleteLead" @click.stop="deleteLead(data.id)">
                    <i class="fas fa-trash-alt"></i>
                </a>

                <a class="link text-success mx-1"  href="javascript:void(0)" title="Show Appointment" @click.stop="showLead(data.id)">
                    <i class="fas fa-eye"></i>
                </a>
                
                <span v-if="condition">
                <a v-if="data.outcome !=9" class="link text-secondary mx-1"  href="javascript:void(0)" title="Change status" @click.stop="changeStatus(data.id,data)">
                    <i class="far fa-sticky-note"></i>
                </a>
                </span>
            
        </div>
        <div v-else>
            <a class="btn btn-danger btn-sm mx-1"  href="javascript:void(0)" title="Restore Appointment" @click.stop="restore(data.id)">
                Restore
            </a>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            // id: null
            canDeleteLead: this.$gate.permissions.includes('delete_appointment'),
        }
    },
    props: {
        data: {},
        click: {
            type: Function,
            default: () => {}
        }
    },
    computed:{
        condition(){
            return this.data.outcome !==2
        }
        
    },
    mounted(){
        
        if(Cookies.get('lead_id')){
            var new_data = JSON.parse(Cookies.get('lead_data'));
            this.edit(Cookies.get('lead_id'),new_data)
        }
    },
    
    methods:{
        restore(id){
            this.click('restore',id);
        },
        changeStatus(lead_id,data){
            this.click('changeStatus',lead_id,data)
        },
        
    	edit(id,data){
             Cookies.set('lead_id', id)
             Cookies.set('lead_data', JSON.stringify(data))
    		this.click('edit',id,data);
    	},
    	deleteLead(id){
    		this.click('deleteLead',id,'');
        },
        showLead(id){
            this.$router.push('showlead/'+id);
        }
    }
}
</script>