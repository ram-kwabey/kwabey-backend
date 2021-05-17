<template>
    <div>
        <div v-if="data.deleted_at == null" class="btn-group">
            <a href="#" class="link text-primary mx-1" @click.stop="edit(data.id, data.name, data.assigned_to ,data.lead_id, data.due_date, data.status)">
                <i class="fas fa-pencil-alt"></i>
            </a>

            <a href="#" class="link text-success mx-1" title="Show task" @click.stop="show(data.id)">
                <i class="fas fa-eye"></i>
            </a>
            
            <a href="#" class="link text-danger mx-1" v-if="canDeleteTask" @click.stop="deleteTask(data.id)">
                <i class="fas fa-trash-alt"></i>
            </a>
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
            canDeleteTask: this.$gate.permissions.includes('delete_task'),
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
        restore(id){
            this.click('restore',id);
        },
        edit( id, name, assigned_to, lead, due_date,status ){
            this.click( 'edit', id, name, assigned_to, lead, due_date, status );
        },
        
        deleteTask( data ){
            this.click( 'deleteTask', data, '' );
        },
        show(id){
            this.$router.push('/tasks/' + id);
        }
    }
}
</script>