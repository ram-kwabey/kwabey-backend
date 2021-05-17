<template>
    <div>
        <div>
            <div class="card">
                <div class="card-body">
                   <div class="card-body">
                    	<h3>Manage Campaign Links
                    		<router-link class="btn btn-sm btn-success float-right ml-3" to="/users">
                    			<i class="fas fa-undo"></i> Back
                    		</router-link>

                    		<button class="btn btn-primary btn-sm float-right" @click="addCampaigns" v-show="canAddUserCampaign">
                        		<i class="fas fa-plus"></i> Add campaigns link
                        	</button>
                        </h3>
                        <data-table url="/admin/list-user-campaign" :columns="columns" ref="campaignTable" :filters="filters">
	                        <div slot="filters" slot-scope="{ tableFilters, perPage }">
	                            <div class="row mb-2">
	                                <div class="col-md-3">
	                                    <select class="form-control" v-model="tableFilters.length">
	                                        <option :key="page" v-for="page in perPage">{{ page }}</option>
	                                    </select>
	                                </div>
	                                <div class="col-md-6">
					                    <input type="hidden" v-model="tableFilters.filters.user_id">
					                </div>
	                                <div class="col-md-3">
	                                    <input
	                                        name="name"
	                                        class="form-control"
	                                        v-model="tableFilters.search"
	                                        placeholder="Search Campaign">
	                                </div>
	                            </div>
	                        </div>
	                    </data-table>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal fade" id="addCampaignModal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true"> 
            <div class="modal-dialog modal-md" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add campaign link</h5>
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
                                        <label>Campaign name</label>
                                        <input type="text" class="form-control" v-model="data.name" placeholder="Enter the campaign name/title here" />
                                    </div>
                                    <div class="form-group" >
                                        <label>Campaign url</label>
                                        <input type="text" class="form-control" v-model="data.url" placeholder="Enter the campaign url here" />
                                    </div>
                                    <div class="form-group" >
                                       <label>UTM term</label>
                                        <input type="text" class="form-control" v-model="data.term" placeholder="Enter the UTM term here" />
                                    </div>
                                    <div class="form-group" >
                                       <label>UTM medium</label>
                                        <input type="text" class="form-control" v-model="data.medium" placeholder="Enter the UTM medium here" />
                                    </div>
                                    <div class="form-group" >
                                       <label>UTM content</label>
                                        <input type="text" class="form-control" v-model="data.content" placeholder="Enter the UTM content here" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" @click.prevent="storeCampaign">Add campaign link</button> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editCampaignModal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true"> 
            <div class="modal-dialog modal-md" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add campaign link</h5>
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
                                        <label>Campaign name</label>
                                        <input type="text" class="form-control" v-model="edit.name" placeholder="Enter the campaign name/title here" />
                                    </div>
                                    <div class="form-group" >
                                        <label>Campaign url</label>
                                        <input type="text" class="form-control" v-model="edit.url" placeholder="Enter the campaign url here" />
                                    </div>
                                    <div class="form-group" >
                                       <label>UTM term</label>
                                        <input type="text" class="form-control" v-model="edit.term" placeholder="Enter the UTM term here" />
                                    </div>
                                    <div class="form-group" >
                                       <label>UTM medium</label>
                                        <input type="text" class="form-control" v-model="edit.medium" placeholder="Enter the UTM medium here" />
                                    </div>
                                    <div class="form-group" >
                                       <label>UTM content</label>
                                        <input type="text" class="form-control" v-model="edit.content" placeholder="Enter the UTM content here" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" @click.prevent="storeUpdatedCampaign">Update</button> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment";
    import UserCampaignActions from './UserCampaignActions.vue'
    import UserCampaignUrl from './UserCampaignUrl.vue'

    export default {
        data() {
            return {
                campaigns: {},
                errors: [],
                data:{
                    name: '',
                    url: '',
                    term: '',
                    content: '',
                    medium: '',
                },
                edit:{
                	id: '',
                    name: '',
                    url: '',
                    term: '',
                    content: '',
                    medium: '',
                },
                columns: [
                    {
                        label: 'Campaign name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'UTM Term',
                        name: 'term',
                        orderable: true,
                    },
                    {
                        label: 'UTM Medium',
                        name: 'medium',
                        orderable: true,
                    },
                    {
                        label: 'UTM Content',
                        name: 'content',
                        orderable: true,
                    },
                    {
                        label: 'Url',
                        name: 'url',
                        orderable: false,
                        component: UserCampaignUrl
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
                        component: UserCampaignActions, 
                    },
                ],
                filters: {
			        user_id: this.$route.params.user_id,
			    },
			    user_id: this.$route.params.user_id,
			    canAddUserCampaign: this.$gate.permissions.includes('create_user_campaigns'),
            };
        },
        created() {
            
        },
        watch: {
            '$route.params.user_id': function (user_id) {
                if (user_id != undefined) {
                	this.user_id = user_id;
                	this.filters.user_id = user_id;
                    this.getCampaigns()
                }
            }
        },
        methods: {
        	getCampaigns(){
        		this.$refs.campaignTable.getData()
        	},
           	addCampaigns(){
           		$("#addCampaignModal").modal('show');
           	},
           	storeCampaign(){
           		this.errors = [];
                axios.post('/admin/user-campaign/'+this.user_id,{
                    name: this.data.name,
                    url: this.data.url,
                    term: this.data.term,
                    content: this.data.content,
                    medium: this.data.medium
                })
                .then(response => {
                    $("#addCampaignModal").modal('hide');
                    this.data.name = '';
                    this.data.url = '';
                    this.data.term = '';
                    this.data.content = '';
                    this.data.medium = '';
                    this.getCampaigns();
                    Toast.fire({
                        icon: 'success',
                        title: 'Campaign link added successfully'
                    })
                })
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                    }
                })
           	},
           	dataTableEvent(payload,id,data){
                if(payload == 'edit'){
                	this.edit.id = id;
                    this.edit.name = data.name
                    this.edit.url = data.url
                    this.edit.term = data.term
                    this.edit.content = data.content
                    this.edit.medium = data.medium

                    $("#editCampaignModal").modal('show');
                }
                if(payload == 'deleteCampaign'){
                    this.deleteCampaign(id)
                }
            },
            storeUpdatedCampaign(){
           		this.errors = [];
                axios.post('/admin/updateCampaign/'+this.edit.id,{
                    name: this.edit.name,
                    url: this.edit.url,
                    term: this.edit.term,
                    content: this.edit.content,
                    medium: this.edit.medium
                })
                .then(response => {
                    $("#editCampaignModal").modal('hide');
                    this.edit.name = '';
                    this.edit.url = '';
                    this.edit.term = '';
                    this.edit.content = '';
                    this.edit.medium = '';
                    this.getCampaigns();
                    Toast.fire({
                        icon: 'success',
                        title: 'Campaign link updated successfully'
                    })
                })
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                    }
                })
           	},
            deleteCampaign(id){
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
                        axios.delete('/admin/deleteCampaign/'+id).then(({data})=>{
                            if(data.status == "success"){
                                this.getCampaigns();
                                Swal.fire('Deleted!', 'Campaign link has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },
            flashErrors(errors) {
                for (const [key, value] of Object.entries(errors)) {
                    for (let item in value) {
                        this.errors.push(value[item]);
                    }
                }
            },
        },
    };

</script>