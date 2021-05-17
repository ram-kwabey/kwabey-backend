<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div class="lead-container" v-else>
            <div class="page-title d-flex">
                <h3>
                    Show Task
                </h3>

                <div class="ml-auto">

                    <router-link to="/tasks" class="btn btn-sm btn-success"><i class="fas fa-undo"></i> Back
                    </router-link>

                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card bg-light">
                        <div class="card-header bg-primary">
                            <h3 class="card-title x-title ">Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="x-element x-action">


                                <div class=" row">
                                    <div class="col-md-6">
                                        <div class=" row">

                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="far fa-calendar-alt"></i> Task Name :</span>
                                                <span class="text-block ml-2 font-weight-bold">{{task.name}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-user-tie"></i>
                                                    Task Status :</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{task.getstatus.status}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-phone-alt"></i>
                                                    Assigned to :</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{task.assigned_to.name}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-phone-alt"></i>
                                                    Description :</span>
                                                <span class="text-block ml-2 font-weight-bold"
                                                    v-for="desc,key in task.description" :key="key">
                                                    {{key}}--{{desc | capitalize}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-envelope"></i>
                                                    Lead id :</span>
                                                <router-link
                                                    :to="{ name: 'showlead', params: { lead_id: task.lead_id }}">
                                                    <span class="text-block ml-2 font-weight-bold">{{ `#`}}
                                                        {{task.lead_id}}</span>
                                                </router-link>

                                            </div>
                                            <div class="col-md-12 d-flex" v-if="task.due_date != null">
                                                <span class="heading-text text-muted"> <i class="fab fa-sourcetree"></i>
                                                    Due date :</span>
                                                <span class="text-block ml-2 font-weight-bold"> {{task.due_date}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>



        </div>

    </div>
</template>

<script>
    import moment from "moment";

    export default {
        data() {
            return {
                moment: moment,
                task: {},
                loading: false,
            };
        },
        created() {
            this.loading = true;
            this.showtask();
        },
        watch: {
            '$route.params.task_id': function (task_id) {
                if (task_id) {
                    this.showtask()
                }
            }
        },
        methods: {
            async showtask() {
                try {
                    // Cookies.remove("show_id");
                    let task_id = this.$route.params.task_id;
                    const response = await axios.get("/admin/showtask/" + task_id);
                    this.loading = false;
                    this.task = response.data.task;
                } catch (error) {
                    console.log(error);
                }
            },
        },
    };

</script>
