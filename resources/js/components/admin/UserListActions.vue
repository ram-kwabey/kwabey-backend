<template>

    <div>
        <div v-if="data.deleted_at == null" class="btn-group">
            <a href="#" class="link text-primary mx-1" @click="edit(data)">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="#" v-if="data.id != 1" class="link text-danger mx-1" v-show="canDeleteUser" @click="deleteUser(data)">
                <i class="fas fa-trash-alt"></i>
            </a>
            <a class="link text-success mx-1"  href="javascript:void(0)" title="Show Campaigns" v-show="data.id != 1 && canListCampaigns" @click.stop="showCampaign(data.id)">
                <i class="fas fa-link"></i>
            </a>
        </div>
        <div v-else class="btn-group">
            <a class="btn btn-danger btn-sm mx-1"  href="javascript:void(0)" title="Restore User" @click.stop="restore(data)">
                Restore
            </a>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            canDeleteUser: this.$gate.permissions.includes('delete_user'),
            canListCampaigns: this.$gate.permissions.includes('list_user_campaigns'),
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
        restore(data){
            this.click('restore',data);
        },
    	edit(data){
    		this.click('edit',data);
    	},
    	deleteUser(data){
    		this.click('deleteUser',data);
    	},
        showCampaign(id){
            this.$router.push('user-campaign/'+id);
        }
    }
}
</script>