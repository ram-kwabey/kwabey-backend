<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3><button class="btn btn-sm btn-success float-right ml-3" @click="$emit('back-dashboard')">
                    <i class="fas fa-undo"></i> Back</button><button class="btn btn-primary btn-sm float-right" @click="addTeamMembers">
                        <i class="fas fa-plus"></i> Add team members</button>
                        <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleTeamMemebersCsv($event)">Export <i class="fas fa-file-export"></i></button>
                        Team Members ( {{team.name}} )</h3>
                <hr/>
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Closer</th>
                            <th>Setter</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody v-if="teamMembers !== null">
                        <tr v-for="teamMember in teamMembers.data" :key="teamMember.id">
                            <td>{{teamMember.team_lead.name}}</td>
                            <td>{{teamMember.setter.name}}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="#" class="link text-primary mx-1" @click="updateTeamMember(teamMember.id,teamMember.team_lead_id,teamMember.setter_id)">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="#" class="link text-danger mx-1" v-if="canDeleteMember" @click="deleteTeamMember(teamMember.id)">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination :data="teamMembers" :limit="2" @pagination-change-page="getTeamMembers"></pagination>
        </div>
        <div class="modal fade" id="teamMembersModal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true"> 
            <div class="modal-dialog modal-sm" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="editMode" class="modal-title">Edit team members</h5>
                        <h5 v-else class="modal-title">Add team members</h5>
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
                                        <label>Closer</label>
                                        <select  v-model="data.team_lead_id" name="team_lead_id" id="team_lead_id"
                                        class="form-control" placeholder="Select Team Lead" :disabled="editMode">
                                            <option v-if="teamLeads.length > 0" value="" selected>Select Closer</option>
                                            <option v-else value="" selected>No option found</option>
                                            <option v-for="teamlead in teamLeads" :key="teamlead.id" :value="teamlead.id">{{teamlead.name | capitalize}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group" >
                                        <label>Setter</label>
                                        <select  v-model="data.setter_id" name="setter_id" id="setter_id"
                                        class="form-control" placeholder="Select Setter">
                                            <option v-if="setters.length > 0" value="" selected>Select Setter</option>
                                            <option v-else value="" selected>No option found</option>
                                            <option v-for="setter in setters" :key="setter.id" :value="setter.id">{{setter.name | capitalize}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-if="editMode" type="submit" class="btn btn-primary" @click.prevent="storeUpadatedTeamMember">Update Team Members</button>
                            <button v-else type="submit" class="btn btn-primary" @click.prevent="storeTeamMembers">Add Team Members</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props: ['team'],
        data(){
            return{
                data:{
                    team_member_id: '',
                    team_lead_id: '',
                    setter_id: '',
                },
                errors: [],
                teamMembers: {},
                teamLeads: {},
                setters: {},
                editMode: false,
                canDeleteMember: this.$gate.permissions.includes('delete_team_member'),
            }
        },
        created(){
            this.getTeamMembers();
        },
        methods:{
            async handleTeamMemebersCsv(event){
                try {
                    event.target.disabled = true
                    this.$toaster.success('Downloading in progress...')
                    const response = await axios.post('admin/handleTeamMemebersCsv/'+ this.team.id)
                    if(response){
                    let blob = new Blob([response.data], { type: 'text/csv' })
                    let link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)
                    link.download = 'export_team_members.csv'
                    link.click()
                    }
                    event.target.disabled = false
                } catch (error) {
                    event.target.disabled = false
                    console.log(error)
                }
            },
            getTeamLeads(){
                axios.get('admin/getClosers')
                .then(response => {
                    this.teamLeads = response.data.results
                })
                .catch(e => console.log(e))
            },
            getSetters(){
                axios.get('admin/getSetters')
                .then(response => {
                    this.setters = response.data.results
                })
                .catch(e => console.log(e))
            },
            getTeamMembers(){
                axios.get('admin/getTeamMembers/'+this.team.id)
                .then(response => {
                    this.teamMembers = response.data.results
                })
                .catch(e => console.log(e))
            },
            storeTeamMembers(){
                this.errors = [];
                axios.post('admin/teamMembers/'+this.team.id,{
                    team_lead_id: this.data.team_lead_id,
                    setter_id: this.data.setter_id
                })
                .then(response => {
                    $("#teamMembersModal").modal('hide');
                    this.data.team_lead_id = '';
                    this.data.setter_id = '';
                    this.getTeamMembers();
                    Toast.fire({
                        icon: 'success',
                        title: 'Team member added successfully'
                    })
                })
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                    }
                })
            },
            deleteTeamMember(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete('admin/deleteTeamMember/'+id).then(({data})=>{
                            if(data.status == "success"){
                                this.getTeamMembers();
                                Swal.fire('Deleted!', 'Team Member has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },
            addTeamMembers(){
                this.editMode = false;
                this.getTeamLeads();
                this.getSetters();
                $("#teamMembersModal").modal('show');
            },
            updateTeamMember(id,team_lead_id,setter_id){
                this.editMode = true;
                this.getTeamLeads();
                this.getSetters();
                this.data.team_lead_id = team_lead_id;
                this.data.setter_id = setter_id;
                this.data.team_member_id = id;
                $("#teamMembersModal").modal('show');
            },
            storeUpadatedTeamMember(){
                this.errors = [];
                axios.post('admin/updateteamMembers/'+this.data.team_member_id,{
                    team_id: this.team.id,
                    team_lead_id: this.data.team_lead_id,
                    setter_id: this.data.setter_id
                })
                .then(response => {
                    $("#teamMembersModal").modal('hide');
                    this.data.team_lead_id = '';
                    this.data.setter_id = '';
                    this.data.team_member_id = '';
                    this.editMode = false;
                    this.getTeamMembers();
                    Toast.fire({
                        icon: 'success',
                        title: 'Team member updated successfully'
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