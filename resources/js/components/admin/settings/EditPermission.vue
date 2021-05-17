<template>
    <div>
        <div class="card">
            <div class="card-body col-6">
                <h3>Edit Permission <button class="btn btn-sm btn-success float-right" @click="$emit('view-userRole')">
                    <i class="fas fa-redo"></i> Back</button></h3>
                <hr/>
                <h4>{{prop_roles.name}}</h4>
                <select class="form-control" multiple v-model="checkPermissions">
                    <option v-for="permission in permissions" :key="permission"
                        :value="permission">{{permission | capitalize}}</option>
                </select>
                
            </div>
            <div class="card-footer">
                <button type="button" @click="handlePermissions(roles.name)" class="btn btn-primary btn-sm">Save permissions</button>
            </div>
        </div>
    </div>
</template>
<script>
  import Multiselect from 'vue-multiselect'
    export default{
        components:{
            Multiselect
        },
        props:['roles'],
        data(){
            return{
                prop_roles : this.roles,
                permissions: [],
                checkPermissions : []
            }
        },
        created(){
            this.getPermissions()
            
        },
        watch:{
             roles: function(){
                this.prop_roles = this.roles;
                }
        },
        methods:{
            
            onChange(event){
                console.log(event.target.value)
            },
            handlePermissions(role){
                axios.post('admin/updatePermissions',{
                    role: role,
                    checkedPermissions: this.checkPermissions
                })
                .then(response =>{
                    this.$toaster.success('Permission updated successfully!')
                })
                .catch(e => console.log(e))
            },
            getPermissions(){
                axios.get('admin/permissions')
                .then(response => {
                    this.permissions = response.data.map(permission => permission.name)
                    this.checkPermissions = this.roles.permissions.map(permission => permission.name)
                })
                .catch(e => console.log(e))
            },
        }
    }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css">