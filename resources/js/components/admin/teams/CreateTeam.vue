<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Create Team <button class="btn btn-success btn-sm float-right" @click="$emit('view-dashboard')">
                    <i class="fas fa-undo"></i> Back</button></h3>
                <hr/>
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">
                    <div class="row form-group">
                        <label for="" class="col-md-3">Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.name">
                        </div>
                    </div>
                    <div class="row form-group" >
                        <label class="col-md-3">Team Lead</label>
                        <div class="col-md-9">
                            <select  v-model="data.team_lead_id" name="team_lead_id" id="team_lead_id"  class="form-control" placeholder="Select Team Lead">
                                <option v-if="teamLeads.length > 0" value="" selected>Select Team Lead</option>
                                <option v-else value="" selected>No option found</option>
                                <option v-for="teamlead in teamLeads" :key="teamlead.id" :value="teamlead.id">{{teamlead.name | capitalize}}</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="storeTeam">Create Team</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        data(){
            return{
                data:{
                    name : '',
                    team_lead_id: '',
                },
                errors: [],
                teamLeads: {}
            }
        },
        created(){
            this.getTeamLeads();
        },
        methods:{
            getTeamLeads(){
                axios.get('admin/getTeamLeads')
                .then(response => {
                    this.teamLeads = response.data.results
                })
                .catch(e => console.log(e))
            },
            storeTeam(){
                this.errors = [];
                axios.post('admin/teams-list',{
                    name: this.data.name,
                    team_lead_id: this.data.team_lead_id,
                })
                .then(response => {
                        this.$emit('created-team','Team Created Successfully!');
                        if (typeof window.getNotificationCount === 'function') window.getNotificationCount();
                    }
                )
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                        if (typeof window.getNotificationCount === 'function') window.getNotificationCount(); 
                    }
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