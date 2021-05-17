<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div v-else>
            <div class="card" v-if="active.dashboard">
                <div class="card-body">
                    <h3>Manage Teams <button class="btn btn-primary btn-sm float-right" @click="setActive('createTeam')" v-if="canCreateTeam">
                        <i class="fas fa-plus"></i> Team</button>
                        <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleTeamsCsv($event)">Export <i class="fas fa-file-export"></i></button>
                        </h3>
                    <data-table url="admin/teams-list" :columns="columns" ref="teamTable">
                        <div slot="filters" slot-scope="{ tableFilters, perPage }">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <select class="form-control" v-model="tableFilters.length">
                                        <option :key="page" v-for="page in perPage">{{ page }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-3">
                                    <input
                                        name="name"
                                        class="form-control"
                                        v-model="tableFilters.search"
                                        placeholder="Search Team">
                                </div>
                            </div>
                        </div>
                    </data-table>
                </div>
            </div>
            <createTeam v-if="active.createTeam" v-on:view-dashboard="setActive('dashboard')" v-on:created-team="flashMessageandReload"></createTeam>
            <teamMembers v-if="team !== null" :team="team" v-on:back-dashboard="reloadWithoutFlash">
            </teamMembers>

            <div class="modal fade" id="teamModal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true"> 
                <div class="modal-dialog modal-sm" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit team</h5>
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
                                        <div class="form-group" >
                                            <label>Team Name</label>
                                            <input type="text" class="form-control" v-model="data.name" value="data.name">
                                        </div>
                                        <div class="form-group" >
                                            <label>Team Lead</label>
                                            <select  v-model="data.team_lead_id" name="team_lead_id" id="team_lead_id"
                                            class="form-control" placeholder="Select Team Lead">
                                                <option v-if="teamLeads.length > 0" value="" selected>Select Team Lead</option>
                                                <option v-else value="" selected>No option found</option>
                                                <option v-for="teamlead in teamLeads" :key="teamlead.id" :value="teamlead.id">{{teamlead.name | capitalize}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-outline-primary" @click.prevent="storeUpadatedTeam">Update Team</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import createTeam from './CreateTeam.vue'
    import teamMembers from './TeamMembers.vue'
    import TeamListActions from './TeamListActions.vue'

    export default{
        components:{
            createTeam,
            teamMembers,
            TeamListActions
        },
        created(){
            this.loading = true
            this.getTeamLeads();
        },
        data(){
            return{
                active:{
                    createTeam: false,
                    dashboard: true,
                    teamMembers : false
                },
                loading: false,
                results : {},
                team: null,
                data:{
                    id: '',
                    name: '',
                    team_lead_id: '',
                },
                errors: [],
                columns: [
                    {
                        label: 'Team name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'Team Lead',
                        name: 'team_lead.name',
                        orderable: false,
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
                        component: TeamListActions, 
                    },
                ],
                canCreateTeam: this.$gate.permissions.includes('create_team'),
                teamLeads: {}
            }
        },
        methods: {
            async handleTeamsCsv(event){
                try {
                    event.target.disabled = true
                    this.$toaster.success('Downloading in progress...')
                    const response = await axios.post('admin/handleTeamsCsv')
                    if(response){
                    let blob = new Blob([response.data], { type: 'text/csv' })
                    let link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)
                    link.download = 'export_teams.csv'
                    link.click()
                    }
                    event.target.disabled = false
                } catch (error) {
                    event.target.disabled = false
                    console.log(error)
                }
            },
            getTeamLeads(){
                axios.get('admin/getTeamLeads')
                .then(response => {
                    setTimeout(() => {
                        this.loading = false
                    }, 500);
                    this.teamLeads = response.data.results
                })
                .catch(e => console.log(e))
            },
            getTeams(){
                this.$refs.teamTable.getData()
            },
            setActive(component){
                //get all the keys of active object and make them false
                Object.keys(this.active).forEach(key => this.active[key] = false)
                this.active[component] = true
            },
            deleteTeam(id){
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
                        axios.delete('admin/deleteTeam/'+id).then(({data})=>{
                            if(data.status == "success"){
                                this.getTeams();
                                Swal.fire('Deleted!', 'Team has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
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
            dataTableEvent(payload,data){
                if(payload == 'assign'){
                    this.teamMembersAssign(data);
                }
                if(payload == 'edit'){
                    this.updateTeam(data);
                }
                if(payload == 'deleteTeam'){
                    this.deleteTeam(data);
                }
            },
            teamMembersAssign(team){
                this.team = team
                this.setActive('teamMembers')
            },
            reloadWithoutFlash(){
                this.team = null
                this.setActive('dashboard')
                this.getTeams()
            },
            updateTeam(data){
                this.data.id = data.id;
                this.data.name = data.name;
                this.data.team_lead_id = data.team_lead_id;
                $("#teamModal").modal('show');
            },
            storeUpadatedTeam(){
                this.errors = [];
                axios.post('admin/updateteam/'+this.data.id,{
                    name: this.data.name,
                    team_lead_id: this.data.team_lead_id,
                })
                .then(response => {
                    $("#teamModal").modal('hide');
                    this.data.id = '';
                    this.data.name = '';
                    this.data.team_lead_id = '';
                    this.getTeams();
                    Toast.fire({
                        icon: 'success',
                        title: 'Team updated successfully'
                    })
                })
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
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