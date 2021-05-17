<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>
                    Notification Preferences
                </h3>
                <hr />
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">                    
                    <div class="row form-group" v-for="(preference, ind) in preferences" :key="ind" >
                        <label :for="preference.type" class="col-md-3">{{ preference.type | capitalize }}</label>
                        <div class="col-md-9" >
                            <select class="form-control" :id="preference.type" v-model="data.preferences[ preference.type ]" >
                                <option 
                                    v-for="(option, key, index) in preference_options" 
                                    :key="index" 
                                    :value="key" 
                                >{{ option }}</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="updateNotificationPreferences">Save</button>
                    <router-link to="/home" class="btn btn-danger" >Cancel</router-link>
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
                    preferences: {},
                },
                preferences: {},
                errors: [],
                preference_options: {
                    'notification_email' : 'Notification & Email',
                    // 'notification' : 'Notification',
                    'nothing' : 'Nothing',
                },
            }
        },
        created(){
            this.getNotificationPreferences();
        },
        methods:{
            getNotificationPreferences(){
                axios.get( 'admin/user-notification-preferences' )
                .then(response => {
                    this.preferences = response.data;
                    response.data.map( ( data, key )=>{
                        console.log( data )
                        this.data.preferences[ data.type ] = data.preference
                    } )
                })
                .catch(e => console.log(e))
            },
            updateNotificationPreferences(){
                this.errors = [];
                axios.put('admin/user-notification-preferences',{
                    data: this.data.preferences
                })
                .then(response => {
                    this.getNotificationPreferences();
                    this.$toaster.success('Notification preference saved!')
                } )
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                        this.$toaster.error('Error in storing Preference!')
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