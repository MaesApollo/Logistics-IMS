<template>
  <div class="container-fluid">
    <div class="block-header">
      <button
        type="button"
        class="btn btn-default waves-effect"
        @click="createNewClient"
        :disabled="!roles.create_client"
      >
        <i class="material-icons">note_add</i>
        <span>Create New Client</span>
      </button>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Clients</h2>
        <div style="background:green;float:right;margin-top:-18px;">
          <button
            type="button"
            class="btn bg-black waves-effect waves-light"
            v-show="roles.update_client"
            @click="updateClients"
          >
            <i class="material-icons">cached</i>
            <span>Update</span>
          </button>
        </div>
      </div>

      <div class="body">
        <form @submit.prevent="searchClient">
          <div class="row clearfix">
            <div class="col-md-3 col-md-offset-7">
              <div class="form-group">
                <span>Search</span>
                <div class="form-line">
                  <input
                    type="text"
                    class="form-control"
                    v-model="search.client"
                    autocomplete="off"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <br />
              <button
                type="submit"
                class="btn bg-black waves-effect waves-light"
              >
                Search
              </button>
              <button class="btn btn-success waves-effect" @click="reset">
                Reset
              </button>
            </div>
          </div>
        </form>

        <div class="row clearfix">
          <div class="col-sm-12">
            <div class="table-wrap">
              <div class="row clearfix" v-if="showLoading" style="width:100%">
                <td colspan="14" class="text-center">
                  <img src="../../img/bars.gif" height="50" />
                  <br />
                  Fetching list...
                </td>
              </div>
              <div class="table-responsive" v-else>
                <table class="table table-striped table-condensed table-hover">
                  <thead>
                    <tr>
                      <th>Account No.</th>
                      <th>Name</th>
                      <th>Contact</th>
                      <th>Address</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="client in clients"
                      :key="client.id"
                      style="cursor: pointer;"
                      @click="getClient(client)"
                      data-toggle="modal"
                      data-target="#clientModal"
                      title="click more details"
                    >
                      <td>{{ client.account_no }}</td>
                      <td>{{ client.name }}</td>
                      <td>{{ client.contact }}</td>
                      <td>{{ client.location }}</td>
                    </tr>

                    <tr v-show="clients.length == 0">
                      <td colspan="6" class="text-center">
                        <small class="col-red">
                          <i>No client found.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <p>{{ clients.length }} clients displayed.</p>
          </div>
        </div>
      </div>

      <!-- SALES ORDER MODAL -->
      <div
        id="clientModal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        data-backdrop="static"
        data-keyboard="false"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="header">
              <div class="row clearfix">
                <div class="col-md-6">
                  <h2>Client Details</h2>
                </div>
                <div class="col-md-6">
                  <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              </div>
            </div>
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-sm-12">
                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <label>Account No.</label>
                      <div class="input-group">
                        <div class="form-line">
                          <input
                            type="text"
                            class="form-control"
                            v-model="client.account_no"
                            autocomplete="off"
                            disabled
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <label>Name</label>
                      <div class="input-group">
                        <div class="form-line">
                          <input
                            type="text"
                            class="form-control"
                            v-model="client.name"
                            autocomplete="off"
                          />
                        </div>

                        <small
                          class="text-danger pull-left"
                          v-show="errors.has('name')"
                          >{{ errors.first("name") }}</small
                        >
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <span>Contact Person</span>
                      <div class="input-group">
                        <div class="form-line">
                          <input
                            type="text"
                            name="contact"
                            class="form-control"
                            v-model.trim="client.contact_person"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <span>Contact Number</span>
                      <div class="input-group">
                        <div class="form-line">
                          <input
                            type="text"
                            name="contact"
                            class="form-control"
                            v-model.trim="client.contact"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <span>Email</span>
                      <div class="input-group">
                        <div class="form-line">
                          <input
                            type="text"
                            name="contact"
                            class="form-control"
                            v-model.trim="client.email_add"
                            autocomplete="off"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <span>Location</span>
                      <div class="input-group">
                        <div class="form-line">
                          <textarea
                            type="text"
                            name="location"
                            class="form-control"
                            v-model.trim="client.location"
                            rows="5"
                          ></textarea>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row clearfix">
                    <div class="col-sm-12">
                      <span>Class</span>
                      <div class="input-group">
                        <div class="form-line">
                          <model-list-select
                            class="search-list"
                            :list="client_class"
                            option-value="name"
                            option-text="name"
                            v-model="client.class"
                            v-validate="'required'"
                            placeholder="Please select class .."
                          >
                          </model-list-select>
                        </div>
                        <small
                          class="text-danger pull-left"
                          v-show="errors.has('class')"
                          >Class is required.</small
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <router-link :to="'/account/' + client.id + '/ordered'">
                <button
                  class="btn btn-lg btn-success waves-effect waves-light"
                  data-dismiss="modal"
                >
                  Order History
                </button>
              </router-link>
              <button
                class="btn btn-lg btn-info waves-effect waves-light"
                @click="update"
                :disabled="!roles.update_client"
              >
                Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- END SALES ORDER MODAL -->
    </div>
  </div>
</template>

<script>
import { ModelListSelect } from "vue-search-select";
export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      authenticatedUser: [],
      roles: [],
      clients: [],
      client: [],
      search: {
        client: null
      },
      showLoading: false,
      client_class: [
        {
          name: "INET CLIENTS"
        },
        {
          name: "SOLUTIONS CLIENTS"
        },
        {
          name: "DCTECH"
        }
      ]
    };
  },
  created() {
    this.authenticatedUser = this.$global.getUser();
    this.getClients();
    this.roles = this.$global.getRoles();
  },

  methods: {
    getClients() {
      this.showLoading = true;
      this.$http.post("api/client/limited").then(response => {
        this.clients = response.body;
        this.showLoading = false;
      });
    },
    getClient(client) {
      this.client = client;
    },
    updateClients() {
      this.showLoading = true;
      this.$http.post("api/updateClients").then(response => {
        swal("Clients List", "Updated", "success");
        this.getClients();
        this.showLoading = false;
      });
    },

    update() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/client/" + this.client.id, this.client)
            .then(response => {
              // console.log(response.body);
              this.clients = response.body;
              this.$global.setClients(response.body);
              // $("#clientModal").modal("hide");
              swal(this.client.name, "was successfully updated!", {
                icon: "success"
              });
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                }
              });
            });
        }
      });
    },

    searchClient() {
      this.$http.post("api/client/search", this.search).then(response => {
        this.clients = response.body;
      });
    },

    reset() {
      this.search.client = "";
      this.searchClient();
    },

    createNewClient() {
      swal("Create a new client?", {
        buttons: {
          Yes: true,
          cancel: "Cancel"
        }
      }).then(value => {
        switch (value) {
          case "Yes":
            this.$router.push({
              path: "/create/client"
            });
            break;

          default:
            break;
        }
      });
    }
  }
};
</script>

<style scoped>
textarea {
  resize: none;
}

.table-wrap {
  height: 500px;
  overflow-y: auto;
  overflow-x: hidden;
  border: 1px solid #eee;
}

/* width */
::-webkit-scrollbar {
  width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}
</style>
