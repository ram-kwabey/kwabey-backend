<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div v-else>
            <div class="card" v-if="active.dashboard">
                <div class="card-body">
                    <h3>Manage Tasks <button class="btn btn-primary btn-sm float-right" @click="setActive('addTask')" v-if="canCreateTask">
                        <i class="fas fa-plus"></i> New Task</button>
                        <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleTasksCsv($event)">Export <i class="fas fa-file-export"></i></button>
                        <button
                            class="openbtn mr-2 btn btn-sm btn-secondary float-right"
                            @click="openNav()"
                        >
                            <i class="fas fa-filter"></i>
                        </button>
                        </h3>
                    <data-table url="admin/tasks-list" :columns="columns" order-dir="desc" ref="tasksTable" :filters="filters">
                        <div slot="filters" slot-scope="{ tableFilters, perPage }">
                            <div class="row mb-2">
                                <div :class="filtered_task ? 'col-md-2' : 'col-md-3'">
                                    <select class="form-control" v-model="tableFilters.length">
                                        <option :key="page" v-for="page in perPage">{{ page }}</option>
                                    </select>
                                </div>
                            
                                <div :class="filtered_task ? 'col-md-2' : 'col-md-4'">
                                    <input type="hidden" v-model="tableFilters.filters.status">
                                    <input type="hidden" v-model="tableFilters.filters.assigned_to">
                                    <input type="hidden" v-model="tableFilters.filters.name">
                                    <input type="hidden" v-model="tableFilters.filters.lead_id">
                                    <input type="hidden" v-model="tableFilters.filters.startDate">
                                    <input type="hidden" v-model="tableFilters.filters.endDate">
                                </div>
                                <div :class="canArchiveTask ? 'col-md-0' : 'col-md-2'"></div>
                                <div class="col-md-3" v-if="filtered_task">
                                    <button v-if="showTasks" class="btn btn-secondary btn-md float-right" title="MyTasks" @click="filterTasks('myTasks')"><i class="fas fa-user"></i></button>
                                    <button v-else class="btn btn-success btn-md float-right" title="MyTeamTasks" @click="filterTasks('teamTasks')"><i class="fas fa-users"></i></button>
                                </div>
                                <div v-if="canArchiveTask" class="col-md-2">
                                    <select class="form-control" v-model="filters.type" required>
                                        <option v-for="option in data_type" :key="option.id" :value="option.type" >{{ option.type }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input
                                        name="name"
                                        class="form-control"
                                        v-model="tableFilters.search"
                                        placeholder="Search Task">
                                </div>
                            </div>
                        </div>
                        <tbody slot="body" slot-scope="{ data }" class="laravel-vue-datatable-tbody">
                            <tr
                              @click="clicked(item)"
                              class="laravel-vue-datatable-tbody-tr"
                              :key="item.id"
                              role="button"
                              v-for="item in data">
                                <td 
                                  class="laravel-vue-datatable-td"
                                  :key="column.name"
                                  v-for="column in columns"
                                  
                                  >
                                    <data-table-cell
                                        :value="item"
                                        :name="column.name"
                                        :meta="column.meta"
                                        :comp="column.component"
                                        :classes="column.classes"
                                        :transform="column.transform"
                                        :orderable="column.orderable"
                                        :component="column.component"
                                        :handler="column.handler"
                                        :event="column.event"
                                        >
                                    </data-table-cell>
                                </td>
                            </tr>
                          </tbody>
                    </data-table>

                    <div id="mySidebar" class="sidebar bg-white shadow-lg px-0">
                        <div class="modal-content1">

                            <div class="modal-header bg-primary rounded-0">
                                <h5 class="modal-title text-white">
                                    <i class="fas fa-filter"></i> Filter Tasks
                                </h5>
                                <a
                                    href="javascript:void(0)"
                                    class="closebtn"
                                    @click="closeNav()"
                                >×</a
                                >
                            </div>
                            <form action="#">
                                <div class="modal-body">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Task name</label>
                                                <input
                                                  type="text"
                                                  v-model="filters.name"
                                                  class="form-control form-control-sm"
                                                />
                                            </div>
                                            <div class="form-group">
                                                <label>Assigned Users</label>
                                                <select class="form-control form-control-sm" v-model="filters.assigned_to">
                                                    <option value="" selected>Select User</option>
                                                    <option v-for="user in users" :key="user.id" :value="user.id" >{{ user.name }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Appointment</label>
                                                <select class="form-control form-control-sm" v-model="filters.lead_id" >
                                                    <option value="" selected>Select Appointment</option>
                                                    <option v-for="(lead,index) in leads" :key="index" :value="lead.id" v-if="lead.client">{{ lead.client.name | capitalize }} (#{{ lead.id}})</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select class="form-control form-control-sm" v-model="filters.status" >
                                                    <option value="" selected>Select Status</option>
                                                    <option v-for="status in statuses" :key="status.status" :value="status.status">{{status.label | capitalize}}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Due date</label>
                                                <div class="sidebar-filter">
                                                    <DateRangePicker
                                                        ref="picker"
                                                        :opens="opens"
                                                        :showDropdowns="showDropdowns"
                                                        :singleDatePicker="singleDatePicker"
                                                        v-model="dateRange"
                                                        @update="updateDateRange"
                                                    >
                                                    </DateRangePicker>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn btn-danger btn-sm" @click="clearFilter()">
                                        Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <addTask v-if="active.addTask" v-on:view-dashboard="setActive('dashboard')" v-on:added-task="flashMessageandReload"></addTask>

            <div class="modal fade" id="taskModel" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true"> 
            <div class="modal-dialog modal-sm" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                                        <ul>
                                            <li v-for="error in errors" :key="error">{{error}}</li>
                                        </ul>
                                    </div>                                    
                                <!---->
                               
                                <div class="form-group">
                                    <label for="task_name">Name</label>
                                    <input type="text" id="task_name" class="form-control" v-model="data.selectedTaskName" placeholder="Enter the task name/title here" />
                                </div>
                                <div class="form-group">
                                    <label for="assigned_to" >Assigned to</label>
                                    <select class="form-control" id="assigned_to" v-model="data.selectedTaskUser" >
                                        <option value="" selected>Select User</option>
                                        <option v-for="user in users" :key="user.id" :value="user.id" >{{ user.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="lead" >Appointment</label>
                                    <select class="form-control" id="lead" v-model="data.selectedTaskLead" >
                                        <option value="" selected>Select Appointment</option>
                                        <option v-for="(lead,index) in leads" :key="index" :value="lead.id" v-if="lead.client">{{ lead.client.name | capitalize }} (#{{ lead.id}})</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="task_status" >Status</label>
                                    <select class="form-control" id="task_status" v-model="data.selectedTaskStatus" >
                                        <option value="" selected>Select Status</option>
                                        <option v-for="status in statuses" :key="status.status" :value="status.status">{{status.label | capitalize}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="task_status" >Due date</label>
                                    <datetime 
                                            type="datetime" 
                                            class="form-control" 
                                            v-model="data.due_date" 
                                            format="yyyy-MM-dd HH:mm:ss"
                                            :min-datetime="disabledDates"
                                        >
                                    </datetime>
                                </div>
                                
                                <!---->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" @click.prevent="storeUpdatedTask($event)">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
    import addTask from './AddTask.vue'
    import TaskListActions from './TaskListActions.vue'
    import TaskLeadNavigate from './TaskLeadNavigate.vue'
    import TaskDescription from './TaskDescription.vue'
    import moment from "moment";

    export default{
        components:{
            addTask,
            TaskListActions,
            TaskLeadNavigate,
            TaskDescription
        },
        created(){
            this.loading = true
            this.getUsers();
            this.getLeads();
            this.getTaskStatuses();
            this.filters.status = this.$route.query.status ? this.$route.query.status : '';
        },
        watch: {
            '$route.query.status': function (status) {
                this.filters.status = status
                this.getTasks()
            },
            '$route.query.assigned_to': function (assigned_to) {
                this.filters.assigned_to = assigned_to
                this.getTasks()
            },
            $route(to,from){
                if(from.name != 'tasks'){
                    this.getTasks()
                } 
            }
        },
        data(){
            return{
                filtered_task: this.$gate.permissions.includes('filter_task'),
                canArchiveTask: this.$gate.permissions.includes('archive_task'),
                showTasks: true,
                active:{
                    addTask: false,
                    dashboard: true,
                    tasks : false
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
                disabledDates: new Date().toISOString(),
                results : {},
                leads: [],
                users: [],
                statuses: [],
                task: null,
                data:{
                    selectedTaskId: '',
                    selectedTaskName: '',
                    selectedTaskUser: '',
                    selectedTaskLead: '',
                    selectedTaskStatus: '',
                    due_date: ''
                },
                errors: [],
                columns: [
                    {
                        label: 'Name',
                        name: 'name',
                        orderable: true,
                    },
                    
                    {
                        label: 'Assigned to',
                        name: 'assigned_to',
                        orderable: false,
                        transform: ({ data, name }) => (data[name] ? data[name].name : ""),
                    },
                    {
                        label: 'Appointment',
                        name: 'lead_id',
                        orderable: false,
                        component: TaskLeadNavigate
                    },
                    {
                        label: 'Status',
                        name: 'getstatus.status',
                        orderable: true,
                    },
                    {
                        label: 'Due date',
                        name: 'due_date',
                        orderable: true,
                        transform: ({ data, name }) =>
                        data[name]
                        ? moment.utc(data[name]).local().format("h:mm,MMMM Do,YYYY")
                        : "",
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
                        component: TaskListActions, 
                    },
                ],
                canCreateTask: this.$gate.permissions.includes('create_task'),
                filters: {
                    name : '',
                    lead_id : '',
                    status: '',
                    startDate: '',
                    endDate: '',
                    assigned_to: '',
                    type: 'All'
                },
                dateRange: {
                    startDate: null,
                    endDate: null
                },
                opens: 'right',
                showDropdowns: true,
                singleDatePicker: "range"
            }
        },
        methods: {
            restore(id){
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
                        .post("/admin/restore_task/" + id)
                        .then(({ data }) => {
                        if (data.status == "success") {
                            this.getTasks();
                            Swal.fire("Restored!", "Task has been restored.", "success");
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
            filterTasks(task){
                this.showTasks = !this.showTasks
                if(task && task == 'myTasks'){
                    this.filters.assigned_to = this.$gate.user.id;
                }else{
                    this.filters.assigned_to = '';
                }
            },
            async handleTasksCsv(event){
                try {
                    event.target.disabled = true
                    
                    const response = await axios.post('admin/handleTasksCsv',{
                        'type' : this.filters.type
                    })
                    this.$toaster.success('Downloading in progress...')
                    if(response){
                    let blob = new Blob([response.data], { type: 'text/csv' })
                    let link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)
                    link.download = 'export_tasks.csv'
                    link.click()
                    }
                    event.target.disabled = false
                } catch (error) {
                    event.target.disabled = false
                    this.$toaster.error('Error in downloading!')
                }
            },
            getTasks(){
                this.$refs.tasksTable.getData();
            },
            getUsers(){
                axios.get( 'admin/users-list?search=&dir=asc&column=id&length=1000&setter=1' )
                .then(response => {
                    this.users = response.data.data
                })
                .catch(e => console.log(e))
            },
            getLeads(){
                axios.get('admin/leads-list?search=&dir=asc&column=id&length=1000&draw=0&page=1')
                .then(response => {
                   this.leads = response.data.data
                })
                .catch(e => console.log(e))
            },
            getTaskStatuses(){
                axios.get( 'admin/tasks-status-list') 
                .then( response => {
                    setTimeout(()=>{
                        this.loading = false
                    },500)
                    this.statuses = response.data
                }) 
                .catch( e => console.log( e)) 
            },
            setActive(component){
                //get all the keys of active object and make them false
                Object.keys(this.active).forEach(key => this.active[key] = false)
                this.active[component] = true
            },
           
            deleteTask( id ){
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you really want to do this!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(( result ) => {
                    if ( result.value ) {
                        axios.delete( 'admin/deleteTask/'+id ).then(({ data })=>{
                            if( data.status == "success" ){
                                this.getTasks();
                                Swal.fire( 'Deleted!', 'Task has been deleted.', 'success' )
                            } else {
                                Swal.fire( 'Failed!', 'Something went wrong.', 'warning' )
                            }
                        }).catch(( { data } )=>{
                            Swal.fire( 'Failed!', 'Something went wrong.', 'warning' )
                        });
                    }
                })
            },
            refreshPage(){
                this.setActive('dashboard')
            },
            flashMessageandReload(event){
                this.refreshPage()
                this.$toaster.success(event)
            },
            dataTableEvent( payload, id, name, user_id, lead_id,due_date,status ){
                if( payload == 'edit' ){
                    
                    this.updateTask( id, name, user_id.id, lead_id ? lead_id.id : '',due_date,status );
                    
                }
                if(payload == 'restore'){
                    this.restore(id);
                }
                else if( payload == 'deleteTask' ){
                    this.deleteTask( id );
                }
                else{
                    console.warn( 'Invalid Payload. Input: ' + payload );
                }
            },
            reloadWithoutFlash(){
                this.task = null
                this.setActive('dashboard')
                this.getTasks();
            },
            updateTask(id,name,user,lead,due_date,status){
                this.errors = []; // reset the errors
                this.data.selectedTaskId = id;
                this.data.selectedTaskName = name;
                this.data.selectedTaskUser = user;
                this.data.selectedTaskLead = lead;
                this.data.selectedTaskStatus = status;
                this.data.due_date = due_date
                ? new Date(due_date).toISOString()
                : "";
                $( "#taskModel" ).modal( 'show' );
            },
            storeUpdatedTask(event){
                this.errors = [];
                event.target.disabled = true
                axios.put( 'admin/updateTask/' + this.data.selectedTaskId, {
                    name: this.data.selectedTaskName,
                    assigned_to: this.data.selectedTaskUser,
                    lead_id: this.data.selectedTaskLead,
                    status: this.data.selectedTaskStatus,
                    due_date: this.data.due_date
                })
                .then(response => {
                    $("#taskModel").modal('hide');
                        this.data.selectedTaskId = '';
                        this.data.selectedTaskName = '';
                        this.data.selectedTaskUser = '';
                        this.data.selectedTaskLead = '';
                        this.data.selectedTaskStatus = '';
                        this.data.due_date = '';
                    this.getTasks();
                    this.$toaster.success('Task updated successfully!')
                    event.target.disabled = false
                })
                .catch( errors => {
                    if( errors.response.status ===  422 ){
                        this.flashErrors( errors.response.data.errors )
                    }
                    event.target.disabled = false
                    this.getTasks();
                })
            },
            flashErrors( errors ){
                for( const[key,value] of Object.entries( errors )){
                    for( let item in value ){
                        this.errors.push( value[item] )
                    }
                }
            },
            clicked(data){
                if(data.lead_id){
                    this.$router.push('/showlead/' + data.lead_id.id);
                }else{
                    // console.log(data)
                    this.updateTask(data.id, data.name, data.assigned_to.id ,data.lead_id, data.due_date, data.status );
                }
            },
            clearFilter(){
                this.filters.name  = '';
                this.filters.status  = '';
                this.filters.assigned_to  = '';
                this.filters.lead_id  = '';
                this.filters.startDate  = '';
                this.filters.endDate  = '';
                this.dateRange.endDate  = null;
                this.dateRange.endDate  = null;
            },
            updateDateRange(){
                this.filters.startDate = moment(this.dateRange.startDate).format()
                this.filters.endDate = moment(this.dateRange.endDate).format()
            },
            openNav() {
                document.getElementById("mySidebar").classList.add("open");
                document.getElementById("mySidebar").style.width = "400px";
            },
            closeNav() {
                document.getElementById("mySidebar").classList.remove("open");
                document.getElementById("mySidebar").style.width = "0";
            },
        }
    }
</script>
