<template>
    <div class="btn btn-group">
       <a href="javascript:void(0)" class="link text-primary mx-1" @click="edit(data.id,data)">
            <i class="fas fa-pencil-alt"></i>
        </a>
        <a href="javascript:void(0)" class="link text-danger mx-1" @click="deleteCampaign(data.id,data)" v-show="canDeleteUserCampaign">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
</template>

<script>
export default {
    data(){
        return{
            canDeleteUserCampaign: this.$gate.permissions.includes('delete_user_campaigns'),
        }
    },
    props: {
        data: {},
        click: {
            type: Function,
            default: () => {}
        }
    },
    methods:{
    	edit(id,data){
    		this.click('edit',id,data);
    	},
    	deleteCampaign(id,data){
    		this.click('deleteCampaign',id,data);
    	}
    }
}
</script>