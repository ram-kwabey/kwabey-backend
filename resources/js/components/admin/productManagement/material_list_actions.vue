<template>

    <div>
        <div v-if="data.deleted_at == null" class="btn-group">
            <a href="#" class="link text-primary mx-1" @click="edit(data)">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="#" class="link text-danger mx-1" v-show="canDeleteMaterial" @click="deleteMaterial(data)">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
        <div v-else class="btn-group">
            <a class="btn btn-danger btn-sm mx-1"  href="javascript:void(0)" title="Restore Material" @click="restoreMaterial(data)">
                Restore
            </a>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            canDeleteMaterial: this.$gate.permissions.includes('delete_material'),
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
    	edit(data){
    		this.click('edit',data);
    	},
    	deleteMaterial(data){
    		this.click('deleteMaterial',data);
    	},
        restoreMaterial(data){
            this.click('restoreMaterial',data);
        },
    }
}
</script>