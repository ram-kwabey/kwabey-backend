<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Add Task  <button class="btn btn-sm btn-success float-right" @click="$emit('view-dashboard')">
                    <i class="fas fa-undo"></i> Back</button></h3>
                <hr />
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">
                    <div class="row form-group">
                        <label for="task_name" class="col-md-3">Name</label>
                        <div class="col-md-9">
                            <input type="text" id="task_name" class="form-control" v-model="data.name" placeholder="Enter the task name/title here" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="task_name" class="col-md-3">Description</label>
                        <div class="col-md-9">
                            <textarea  id="task_description" v-model="data.description" class="form-control" placeholder="Enter the task description here"></textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="assigned_to" class="col-md-3">Assigned to</label>
                        <div class="col-md-9">
                            <select class="form-control" id="assigned_to" v-model="data.user" >
                                <option value="" selected>Select User</option>
                                <option v-for="user in users" :key="user.id" :value="user.id" >{{ user.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="lead" class="col-md-3">Lead</label>
                        <div class="col-md-9">
                            <select class="form-control" id="lead" v-model="data.lead" >
                                <option value="" selected>Select Lead</option>
                                <option v-for="lead in leads" :key="lead.id" :value="lead.id" v-if="lead.client">{{ lead.client.name | capitalize }} (#{{ lead.id}})</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="task_status" class="col-md-3" >Due date</label>
                        <div class="col-md-9">
                            <datetime 
                                    type="datetime" 
                                    class="form-control" 
                                    v-model="data.due_date" 
                                    format="yyyy-MM-dd HH:mm:ss"
                                    :min-datetime="disabledDates"
                                >
                            </datetime>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="addTask($event)">Add Task</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { DateTime } from 'luxon';
    export default{
        data(){
            return{
                data:{
                    name : '',
                    user : '',
                    lead : '',
                    description : '',
                    due_date: ''
                },
                disabledDates: new Date().toISOString(),
                
                errors: [],
                leads: [],
                users: [],
            }
        },
        created(){
            this.getUsers();
            this.getLeads();
        },
        methods:{
            getUsers(){
                axios.get( 'admin/users-list?search=&dir=asc&column=id&length=1000&draw=0&page=1&setter=1' )
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
            addTask(event){
                this.errors = [];
                event.target.disabled = true
                axios.post('admin/tasks-list',{
                    name: this.data.name,
                    lead_id: this.data.lead,
                    assigned_to: this.data.user,
                    description: this.data.description,
                    due_date : this.data.due_date
                })
                .then(response => {
                        this.$emit('added-task','Task added successfully!')
                        event.target.disabled = false
                        if (typeof window.getNotificationCount === 'function') window.getNotificationCount();
                    }
                )
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                        if (typeof window.getNotificationCount === 'function') window.getNotificationCount();
                    }
                    event.target.disabled = false
                })
            },
            flashErrors(errors){
                for(const[key,value] of Object.entries(errors)){
                    for(let item in value){
                        this.errors.push(value[item])
                    }
                }
            }
            
        }
    }
</script>