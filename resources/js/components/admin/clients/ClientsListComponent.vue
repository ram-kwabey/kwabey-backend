<template>
  <div>
    <div class="text-center" v-if="loading" style="margin-top: 10%">
      <img src="/img/loader.svg" />
    </div>
    <div v-else>
      <div class="card" v-if="active.dashboard">
        <div class="card-body">
          <h3>
            Manage Leads
            <button
              class="btn btn-primary btn-sm float-right"
              @click="setActive('addClient')"
              v-if="canCreateClient"
            >
              <i class="fas fa-plus"></i> Lead
            </button>
            <button
              class="btn btn-warning mr-2 btn-sm float-right"
              @click="handleClientsCsv($event)"
            >
              Export <i class="fas fa-file-export"></i>
            </button>
            <!-- <button
              class="openbtn mr-2 btn btn-sm btn-secondary float-right"
              @click="openNav()"
            >
              <i class="fas fa-filter"></i>
            </button> -->
          </h3>
          <data-table
            url="admin/clients-list"
            :columns="columns"
            ref="clientTable"
            :filters="filters"
          >
            <div slot="filters" slot-scope="{ tableFilters, perPage }">
              <div class="row mb-2">
                <div class="col-md-3">
                  <select
                    class="form-control"
                    v-model="tableFilters.length"
                  >
                    <option :key="page" v-for="page in perPage">
                      {{ page }}
                    </option>
                  </select>
                </div>
                <div :class="canArchiveClient ? 'col-md-4' :'col-md-6'">
                </div>
                <div v-if="canArchiveClient" class="col-md-2">
                    <select class="form-control" v-model="filters.type" required>
                        <option v-for="option in data_type" :key="option.id" :value="option.type" >{{ option.type }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                  <input
                    name="name"
                    class="form-control"
                    v-model="tableFilters.search"
                    placeholder="Search Lead"
                  />
                </div>
              </div>
            </div>
          </data-table>

          <div id="mySidebar" class="sidebar bg-white shadow-lg px-0">
            <div class="modal-content1">
              <div class="modal-header bg-primary rounded-0">
                <h5 class="modal-title text-white">
                  <i class="fas fa-filter"></i> Filter Lead
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
                      <!---->
                      <div class="form-group">
                        <label>Assigned Users</label>
                        <input
                          type="text"
                          value=""
                          class="form-control form-control-sm"
                        />
                      </div>
                      <div class="form-group">
                        <label>Status</label>
                        <input
                          type="text"
                          value=""
                          class="form-control form-control-sm"
                        />
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Date Added</label>
                            <input
                              type="text"
                              value="Start"
                              class="form-control form-control-sm"
                            />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <input
                              type="text"
                              value="End"
                              class="form-control form-control-sm"
                            />
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Last Contact</label>
                            <input
                              type="text"
                              value=""
                              class="form-control form-control-sm"
                            />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <input
                              type="text"
                              value=""
                              class="form-control form-control-sm"
                            />
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Value</label>
                            <input
                              type="text"
                              value="Minimum"
                              class="form-control form-control-sm"
                            />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <input
                              type="text"
                              value="Maximum"
                              class="form-control form-control-sm"
                            />
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label>Tags</label>
                        <input
                          type="text"
                          value=""
                          class="form-control form-control-sm"
                        />
                      </div>
                      <div class="form-group">
                        <label>Show</label>
                        <select class="form-control form-control-sm">
                          <option>Default select</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-danger btn-sm">
                    Reset
                  </button>
                  <button type="submit" class="btn btn-primary btn-sm">
                    Apply Filter
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <addClient
        v-if="active.addClient"
        v-on:view-dashboard="setActive('dashboard')"
        v-on:added-client="flashMessageandReload"
      ></addClient>

      <div
        class="modal fade"
        id="clientModel"
        tabindex="-1"
        role="dialog"
        aria-labelledby="moduleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Lead</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="#">
              <div class="modal-body">
                <div class="row">
                  <div class="col-sm-12">
                    <div
                      v-if="errors.length > 0"
                      class="alert alert-danger"
                      role="alert"
                    >
                      <ul>
                        <li v-for="error in errors" :key="error">
                          {{ error }}
                        </li>
                      </ul>
                    </div>
                    <div class="form-group">
                      <label>Lead Name</label>
                      <input
                        type="text"
                        class="form-control form-control-sm"
                        v-model="data.selectedClientName"
                        value="data.selectedClientName"
                      />
                    </div>
                    <div class="form-group">
                      <label>Email</label>
                      <input
                        type="email"
                        class="form-control form-control-sm"
                        v-model="data.selectedClientEmail"
                        value="data.selectedClientEmail"
                      />
                    </div>
                    <div class="form-group">
                      <label>Phone</label>
                      <input
                        type="number"
                        class="form-control form-control-sm"
                        v-model="data.selectedClientPhone"
                        value="data.selectedClientPhone"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-danger"
                  data-dismiss="modal"
                >
                  Close
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  @click.prevent="storeUpdatedClient"
                >
                  Update Lead
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import addClient from "./AddClient.vue";
import ClientListActions from "./ClientListActions.vue";

export default {
  components: {
    addClient,
    ClientListActions,
  },
  created() {
    this.loading = true;
    this.loader();
  },
  data() {
    return {
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
      filters:{
        type: 'All'
      },
      active: {
        addClient: false,
        dashboard: true,
        clients: false,
      },
      loading: false,
      results: {},
      client: null,
      data: {
        selectedClientId: "",
        selectedClientName: "",
        selectedClientEmail: "",
        selectedClientPhone: "",
      },
      errors: [],
      columns: [
        {
          label: "Name",
          name: "name",
          orderable: true,
        },
        {
          label: "Email",
          name: "email",
          orderable: false,
        },
        {
          label: "Phone",
          name: "phone",
          orderable: true,
        },
        {
          label: "Actions",
          name: "Actions",
          orderable: false,
          classes: {
            btn: true,
            "btn-primary": true,
            "btn-sm": true,
          },
          event: "click",
          handler: this.dataTableEvent,
          component: ClientListActions,
        },
      ],
      canCreateClient: this.$gate.permissions.includes("create_lead"),
      canArchiveClient: this.$gate.permissions.includes("archive_lead"),
    };
  },
  methods: {
    async handleClientsCsv(event) {
      try {
        event.target.disabled = true;
        this.$toaster.success("Downloading in progress...");
        const response = await axios.post("admin/handleClientsCsv",{
          'type': this.filters.type
        });
        if (response) {
          let blob = new Blob([response.data], { type: "text/csv" });
          let link = document.createElement("a");
          link.href = window.URL.createObjectURL(blob);
          link.download = "export_clients.csv";
          link.click();
        }
        event.target.disabled = false;
      } catch (error) {
        event.target.disabled = false;
        console.log(error);
      }
    },
    getClients() {
      this.$refs.clientTable.getData();
    },
    loader() {
      setTimeout(() => {
        this.loading = false;
      }, 1000);
    },
    setActive(component) {
      //get all the keys of active object and make them false
      Object.keys(this.active).forEach((key) => (this.active[key] = false));
      this.active[component] = true;
    },
    deleteClient(id) {
      Swal.fire({
        title: "Are you sure?",
        text: `Do you really want to do this!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          axios
            .delete("admin/deleteClient/" + id)
            .then(({ data }) => {
              if (data.status == "success") {
                this.getClients();
                Swal.fire("Deleted!", "Lead has been deleted.", "success");
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
    refreshPage() {
      this.setActive("dashboard");
    },
    flashMessageandReload(event) {
      this.refreshPage();
      this.$toaster.success(event);
    },
    dataTableEvent(payload, data, dataName, dataEmail, dataPhone) {
      if (payload == "restore") {
        this.restore(data.id);
      }
      if (payload == "edit") {
        this.updateClient(data, dataName, dataEmail, dataPhone);
      } else if (payload == "deleteClient") {
        this.deleteClient(data);
      } else {
        console.warn("Invalid Payload. Input: " + payload);
      }
    },
    reloadWithoutFlash() {
      this.client = null;
      this.setActive("dashboard");
      this.getClients();
    },
    updateClient(id, name, email, phone) {
      this.errors = []; // reset the errors
      this.data.selectedClientId = id;
      this.data.selectedClientName = name;
      this.data.selectedClientEmail = email;
      this.data.selectedClientPhone = phone;
      $("#clientModel").modal("show");
    },
    storeUpdatedClient() {
      this.errors = [];
      axios
        .put("admin/updateClient/" + this.data.selectedClientId, {
          name: this.data.selectedClientName,
          email: this.data.selectedClientEmail,
          phone: this.data.selectedClientPhone,
        })
        .then((response) => {
          $("#clientModel").modal("hide");
          this.data.selectedClientId = "";
          this.data.selectedClientName = "";
          this.data.selectedClientEmail = "";
          this.data.selectedClientPhone = "";
          this.getClients();
          Toast.fire({
            icon: "success",
            title: "Lead updated successfully",
          });
        })
        .catch((errors) => {
          if (errors.response.status === 422) {
            this.flashErrors(errors.response.data.errors);
          }
        });
    },
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
            .post("/admin/restore_lead/" + id)
            .then(({ data }) => {
              if (data.status == "success") {
                this.getClients();
                Swal.fire("Restored!", "Lead has been restored.", "success");
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
    flashErrors(errors) {
      for (const [key, value] of Object.entries(errors)) {
        for (let item in value) {
          this.errors.push(value[item]);
        }
      }
    },
    openNav() {
      document.getElementById("mySidebar").style.width = "350px";
    },
    closeNav() {
      document.getElementById("mySidebar").style.width = "0";
    },
  },
};
</script>