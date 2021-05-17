<template>
    <div class="btn-group">
        <a href="#" class="link text-success mx-1" @click.prevent="assign(data)">
            <i class="fas fa-users"></i>
        </a>
        <a href="#" class="link text-primary mx-1" @click.prevent="edit(data.id,data)">
            <i class="fas fa-pencil-alt"></i>
        </a>
        <a href="#" class="link text-danger mx-1" v-if="canDeleteTeam" @click.prevent="deleteTeam(data.id)">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
</template>

<script>
export default {
    data(){
        return{
            canDeleteTeam: this.$gate.permissions.includes('delete_team'),
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
    	assign(data){
    		this.click('assign',data);
    	},
    	edit(id,data){
    		this.click('edit',data);
    	},
    	deleteTeam(data){
    		this.click('deleteTeam',data);
    	}
    }
}
</script>