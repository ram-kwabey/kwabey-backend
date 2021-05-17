<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div v-else>
            <div class="card" v-if="active.dashboard">
                <div class="card-body">
                    <h3>Manage Users  <button class="btn btn-primary btn-sm float-right" @click="setActive('createUser')" v-if="canCreateUser">
                        <i class="fas fa-plus"></i> User</button>
                        <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleUsersCsv($event)">Export <i class="fas fa-file-export"></i></button>
                        </h3>
                    <data-table url="admin/users-list" :columns="columns" ref="userTable" :filters="filters">
                        <div slot="filters" slot-scope="{ tableFilters, perPage }">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <select class="form-control" v-model="tableFilters.length">
                                        <option :key="page" v-for="page in perPage">{{ page }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select
                                        v-model="tableFilters.filters.role"
                                        class="form-control">
                                        <option value>All Roles</option>
                                        <option v-for="(role,index) in roles" :key="role.id" :value="role.id">{{role.name | capitalize}}</option>
                                    </select>
                                </div>
                                <div :class="canArchiveUser ? 'col-md-1' : 'col-md-3'"></div>
                                <div v-if="canArchiveUser" class="col-md-2">
                                    <select class="form-control" v-model="filters.type" required>
                                        <option v-for="option in data_type" :key="option.id" :value="option.type" >{{ option.type }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input
                                        name="name"
                                        class="form-control"
                                        v-model="tableFilters.search"
                                        placeholder="Search User">
                                </div>
                            </div>
                        </div>
                    </data-table>
                </div>
            </div>
            
            <createUser v-if="active.createUser" 
                        v-on:view-dashboard="setActive('dashboard')"
                        v-on:created-user="flashMessageandReload"
            ></createUser>
            <EditUser 
                v-if="active.EditUser"
                :user="user"
                v-on:view-dashboard="setActive('dashboard')"
                v-on:user-updated="flashMessageandReload">
            </EditUser>
        </div>
    </div>
</template>

<script>
    import createUser from './CreateUser.vue'
    import EditUser from './EditUser.vue'
    import UserListActions from './UserListActions.vue'

    export default{
        components:{
            createUser,
            EditUser,
            UserListActions
        },
        created(){
            this.loading = true
            this.getRoles()
        },
        data(){
            return{
                active:{
                    createUser: false,
                    dashboard: true,
                    EditUser: false
                },
                data_type:[
                    {
                    id:1,
                    type:'All'
                    },
                    {
                    id:2,
                    type: 'Archived'
                    }
                ],
                loading: false,
                results : {},
                success : null,
                alert_success: false,
                user: null,
                columns: [
                    {
                        label: 'Name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'Email',
                        name: 'email',
                        orderable: true,
                    },
                    {
                        label: 'Role',
                        name: 'roles',
                        orderable: false,
                        transform: ({data, roles}) => (data.roles.length > 0 ? `${data.roles[0].name}` : ''),
                    },
                    {
                        label: 'Actions',
                        name: 'Actions',
                        orderable: false,
                        classes: { 
                            'btn': true,
                            'btn-primary': true,
                            'btn-sm': true,
                        },
                        event: "click",
                        handler: this.dataTableEvent,
                        component: UserListActions, 
                    },
                ],
                filters: {
                    role: '',
                    type: 'All'
                },
                roles: {},
                canCreateUser: this.$gate.permissions.includes('create_user'),
                canArchiveUser: this.$gate.permissions.includes('archive_user')
            }
        },
        methods: {
            async handleUsersCsv(event){
                try {
                    event.target.disabled = true
                    this.$toaster.success('Downloading in progress...')
                    const response = await axios.post('admin/handleUsersCsv',{
                        'type': this.filters.type
                    })
                    if(response){
                    let blob = new Blob([response.data], { type: 'text/csv' })
                    let link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)
                    link.download = 'export_users.csv'
                    link.click()
                    }
                    event.target.disabled = false
                } catch (error) {
                    event.target.disabled = false
                    console.log(error)
                }
            },
            getUsers(){
                this.$refs.userTable.getData()
                this.getRoles()
            },
            editUser(user){
                this.user = user
                this.setActive('EditUser')
            },
            showUser(id){
                console.log(id);
            },
            setActive(component){
                //get all the keys of active object and make them false
                Object.keys(this.active).forEach(key => this.active[key] = false)
                this.active[component] = true
            },
            refreshPage(){
                this.setActive('dashboard')
            },
            flashMessageandReload(event){
                this.refreshPage()
                this.$toaster.success(event)
            },
            restore(data){
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to do this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, restore it!",
                }).then((result) => {
                    if (result.value) {
                    axios
                        .post("/admin/restore_user/" + data.id)
                        .then(({ data }) => {
                        if (data.status == "success") {
                            this.getUsers();
                            Swal.fire("Restored!", "User has been restored.", "success");
                        } else {
                            Swal.fire("Failed!", "Something went wrong.", "warning");
                        }
                        })
                        .catch(({ data }) => {
                        Swal.fire("Failed!", "Something went wrong.", "warning");
                        });
                    }
                });
            },
            deleteUser(user){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to do this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.post('admin/user/' + user.id,{_method:'DELETE'}).then(({data})=>{
                            if(data.status == "success"){
                                this.getUsers();
                                Swal.fire('Deleted!', 'User has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },
            dataTableEvent(payload,data){
                if(payload == 'edit'){
                    this.editUser(data);
                }
                if(payload == 'restore'){
                    this.restore(data);
                }
                if(payload == 'deleteUser'){
                    this.deleteUser(data);
                }
            },
            getRoles(){
                axios.get('admin/getRoles')
                .then(response => {
                    setTimeout(() => {
                        this.loading = false
                    },500)
                    this.roles = response.data.results
                })
                .catch(e => console.log(e))
            }
        }
    }
</script>