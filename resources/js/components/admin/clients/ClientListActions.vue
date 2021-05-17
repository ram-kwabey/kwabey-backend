<template>
    <div>
    <div v-if="data.deleted_at == null" class="btn-group">
        <a href="#" class="link text-primary mx-1" @click="edit(data.id,data.name,data.email,data.phone)">
            <i class="fas fa-pencil-alt"></i>
        </a>
        <a href="#" class="link text-danger mx-1" v-if="canDeleteClient" @click="deleteClient(data.id)">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
    <div v-else>
            <a class="btn btn-danger btn-sm mx-1"  href="javascript:void(0)" title="Restore lead" @click.stop="restore(data)">
                Restore
            </a>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            canDeleteClient: this.$gate.permissions.includes('delete_lead'),
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
        edit( id, name, email, phone ){
            this.click( 'edit', id, name, email, phone );
        },
        deleteClient( data ){
            this.click( 'deleteClient', data, '' );
        }
    }
}
</script>