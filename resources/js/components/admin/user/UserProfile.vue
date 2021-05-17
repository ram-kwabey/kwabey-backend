<template>
    <div class="content-s">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Profile</h1>
                    </div>
                    <div class="col-sm-6"></div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-3">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" :src="form.imageUrl !== '' ? form.imageUrl : '/admin/img/dummy_profile.png'" alt="User profile picture">
                    </div>

                    <h3 class="profile-username text-center">{{ form.name }}</h3>

                    <p class="text-muted text-center" title="User Roles" >{{ roles }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                      <!-- <li class="list-group-item">
                        <strong>Appointments</strong> <router-link to="/appointments" class="float-right">{{ form.leadsCount }}</router-link>
                      </li>
                      <li class="list-group-item">
                        <strong>Tasks</strong> <router-link to="/tasks" class="float-right">{{ form.tasksCount }}</router-link>
                      </li> -->
                      <!-- <li class="list-group-item">
                        <b>Friends</b> <a class="float-right">13,287</a>
                      </li> -->
                    </ul>

                    <!-- <a href="#" class="btn btn-primary btn-block"><strong>Follow</strong></a> -->
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->

                
              </div>
              <!-- /.col -->
              <div class="col-md-9">
                <div class="card">
                  <div class="card-header p-2">
                    <ul class="nav nav-pills">
                      <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Profile</a></li>
                      <li class="nav-item"><a class="nav-link" href="#changePassword" data-toggle="tab">Change Password</a></li>
                      <li class="nav-item" v-show="canListCampaigns && isNotAdmin"><a class="nav-link" href="#userCampaigns" data-toggle="tab">User Campaigns</a></li>
                    </ul>
                  </div><!-- /.card-header -->
                  <div class="card-body">
                    <div class="tab-content">
                      <div class="active tab-pane" id="profile">
                        <form @submit.prevent="updateUser()" @keydown="form.onKeydown($event)" class="form-horizontal" enctype="multipart/form-data">
                         
                          <alert-error :form="form"></alert-error>
                          <div class="form-group">
                             <input v-model="form.first_name" type="text" name="first_name" id="first_name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('first_name') }" placeholder="First Name">
                             <has-error :form="form" field="first_name"></has-error>
                          </div>
                          <div class="form-group">
                             <input v-model="form.last_name" type="text" name="last_name" id="last_name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }" placeholder="Last Name">
                             <has-error :form="form" field="last_name"></has-error>
                          </div>
                          <div class="form-group">
                             <input v-model="form.username" type="text" name="username" id="username"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('username') }" placeholder="Last Name">
                             <has-error :form="form" field="username"></has-error>
                          </div>
                          <div class="form-group">
                             <input v-model="form.email" type="email" name="email" id="email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email" autocomplete="off">
                             <has-error :form="form" field="email"></has-error>
                          </div>
                          <!-- <div class="form-group">
                              <select v-model="form.role" name="role" id="role"
                              class="form-control" :class="{ 'is-invalid': form.errors.has('role') }" placeholder="Select Role">
                                  <option value="" disabled="" selected="">-Select Role-</option>
                                  <option v-for="option in options" :key="option.id" :value="option.name"> {{ option.name }}</option>
                              </select>
                              <has-error :form="form" field="role"></has-error>
                          </div> -->

                          <div class="form-group">
                                <strong>Profile Image:</strong>
                                <input  type="file" class="form-control" :class="{ 'is-invalid': error.image }" id="image" name="image" v-on:change="onImageChange">
                                <div class="help-block invalid-feedback" v-html="imageError"></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <router-link to="/home" class="btn btn-danger">Cancel</router-link>
                            </div>
                          </div>
                        </form>
                      </div>
                      <div class="tab-pane" id="userCampaigns">
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
                      <!-- /.tab-pane -->
                      <div class="tab-pane" id="changePassword">
                        <form @submit.prevent="changePassword()" @keydown="form.onKeydown($event)" class="form-horizontal" id="userChangePasswordForm" >
                          <alert-error :form="changePasswordForm"></alert-error>
                          <div class="form-group">
                             <input v-model="changePasswordForm.password" type="password" name="password" id="password"
                                class="form-control" :class="{ 'is-invalid': changePasswordForm.errors.has('password') }" placeholder="Password">
                             <has-error :form="changePasswordForm" field="password"></has-error>
                          </div>
                          <div class="form-group">
                             <input v-model="changePasswordForm.confirmPassword" type="password" name="confirmPassword" id="confirmPassword"
                                class="form-control" :class="{ 'is-invalid': changePasswordForm.errors.has('confirmPassword') }" placeholder="Confirm Password" autocomplete="off">
                             <has-error :form="changePasswordForm" field="confirmPassword"></has-error>
                          </div>
                          <div class="form-group row">
                             <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Update</button>
                             </div>
                          </div>
                        </form> 
                      </div>
                      <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                  </div><!-- /.card-body -->
                </div>
                <!-- /.nav-tabs-custom -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
    </div>
</template>

<script>
  import UserCampaignUrl from './UserCampaignUrl.vue'
    export default {
        created() {
      
          this.getRoleData();
          this.getEditProfileUser();
          
        },
        watch:{
          $route(to,from){

          this.getRoleData();
          this.getEditProfileUser();
          }
        },
        data() {
            return {
                options:{},
                baseUrl: 'http://127.0.0.1:8001/',
                imageError:null,
                error: { image : false },
                users:{},
                editMode:false,
                roles: "",
                form: new Form({
                    id: '',
                    first_name: '',
                    last_name: '',
                    username: '',
                    email: '',
                    avatar: '',
                    image:'',
                    imageUrl:'',
                    role:'',
                    //type: '',
                    //password: '',
                    //remember: false,
                }),
                changePasswordForm: new Form({
                    id: '',
                    password: '',
                    confirmPassword: '',
                    //type: '',
                    //password: '',
                    //remember: false,
                }),
                canListCampaigns: this.$gate.permissions.includes('list_user_campaigns'),
                isNotAdmin : !this.$gate.roles.includes('Admin'),
                filters: {
                  user_id: this.$gate.user.id,
                },
                columns: [
                    {
                        label: 'Campaign name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'Url',
                        name: 'url',
                        orderable: false,
                        component: UserCampaignUrl
                    }
                ],
            }
        },

        methods: {
             onImageChange(e){
                const fileTypes = ['jpg', 'jpeg', 'png'];
                const fileReader = new FileReader();

                const file = e.target.files[0]; // Get your file here
                const fileExt = file.type.split('/')[1]; // Get the file extension

                if (fileTypes.indexOf(fileExt) !== -1) {
                    
                    if (e.target.files[0].size > 2097152) { 
                        
                        this.error.image = true;
                        this.imageError = 'Try to upload file less than 2MB!.';

                        //console.log('Try to upload file less than 2MB!');

                    } else { 
                          
                          fileReader.readAsDataURL(e.target.files[0]);
                            fileReader.onload = (e) => {
                              this.form.image = e.target.result;
                              
                              this.error.image = false;
                              this.imageError = null;
                              
                              this.getImageUrl(this.form.image,'base64');
                          };
                    }
                  
                } else {
                    this.error.image = true;
                    this.imageError = 'Allow only jpg,jpeg and png files.';
                    //console.log('File not supported');
                }
            },
            updateUser (event) {
                // this.$Progress.start();
                this.form.updateFor = 'profile';

                this.form.put('admin/user/update/' + this.form.id)
                .then(({ data }) => { 
                    Toast.fire({ icon: 'success', title: 'User updated successfully' });
                    window.location.reload(); // To refresh user details on app 
                    // this.$Progress.finish() // GTODO
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    // this.$Progress.finish()  // GTODO
                });
            },
            changePassword (event) {
                // this.$Progress.start();  // GTODO
                this.changePasswordForm.updateFor = 'password';
              
                this.changePasswordForm.put('admin/user/update/' + this.changePasswordForm.id)
                .then(({ data }) => { 
                    //this.getUser();
                    Toast.fire({ icon: 'success', title: 'Password has been changed successfully' });
                    $('#userChangePasswordForm').trigger("reset"); // reset password fields
                    // this.$Progress.finish() // GTODO
                    this.changePasswordForm.password = null; // reseting the password values in v-model
                    this.changePasswordForm.confirmPassword = null;
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    // this.$Progress.finish() // GTODO
                });
            },
            /*getUser (id) {
                axios.get('/api/users/'+id).then(({ data }) => { 
                        //console.log(data);
                        //return false;
                        //this.users = data; 
                        this.form.reset();
                        this.form.clear();
                        this.form.fill(data);
                        this.changePasswordForm.fill(data);
                });
            },*/
            getEditProfileUser () {

                axios.get('/admin/edit-profile').then(({ data }) => { 
                    
                        let val = '';
                        let selRole = '';
                        for (val in data.roles) {
                            selRole = data.roles[val].name;
                        }

                        this.form.reset();
                        this.form.clear();
                        this.form.fill(data);
                        this.form.role = selRole;
                        let roles_str = "";
                        data.roles.map(({ name }, index) => {
                            // Get user roles
                            roles_str += name.charAt(0).toUpperCase() + name.slice(1); // First letter capital 
                            if( index < data.roles.length - 1 ){ roles_str += ", "};
                            return name;
                        })
                        this.roles = roles_str; // set User roles string on profile page
                        this.form.leadsCount = data.leads.length;
                        this.form.tasksCount = data.tasks.length;
                        
                        this.getImageUrl(this.form.avatar,'image');

                        this.changePasswordForm.fill(data);
                });
            },
            getImageUrl (image,type=null) {

                if( (image != '') && (type === 'base64') ){

                    this.form.imageUrl = image;

                }else if( (image != '' && image != null && image != undefined ) && (type === 'image') ){

                  this.form.imageUrl = '/storage/profile_pic/' + image;

                }else{

                  this.form.imageUrl = '/admin/img/dummy_profile.png';

                }
            },
            getRoleData () {
                axios.get('admin/getRoles').then(({ data }) => { this.options = data; });
            },
        }
    }
</script>
