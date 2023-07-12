<template>
  <div class="container-fluid">
    <div class="block-header">
      <button
        type="button"
        class="btn btn-default waves-effect"
        @click="reset"
        data-toggle="modal"
        data-target="#departmentModal"
        v-show="roles.admin"
      >
        <i class="material-icons">note_add</i>
        <span>Create New Department</span>
      </button>
    </div>
    <div class="card">
      <div class="header">
        <h2>Manage Departments</h2>
      </div>
      <div class="body">
        <form>
          <div class="row clearfix">
            <div class="col-md-3 col-md-offset-7">
              <div class="form-group">
                <span>Search</span>
                <div class="form-line">
                  <input type="text" class="form-control" autocomplete="off" />
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
              <button class="btn btn-success waves-effect">
                Reset
              </button>
            </div>
          </div>
        </form>

        <div class="row clearfix">
          <div class="col-md-12">
            <div class="table-wrap">
              <div class="row clearfix" v-if="showLoading" style="width:100%">
                <td colspan="14" class="text-center">
                  <img src="../../img/bars.gif" height="50" />
                  <br />
                  Fetching list...
                </td>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-condensed table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Department</th>
                      <th>Approver</th>
                      <th>Created at</th>
                      <th>Updated at</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="department in departments"
                      :key="department.id"
                      style="cursor: pointer;"
                      data-toggle="modal"
                      data-target="#updateDepartmentModal"
                      @click="getDepartment(department.id)"
                      title="click more details"
                    >
                      <td>{{ department.id }}</td>
                      <td>{{ department.name }}</td>
                      <td>{{ department.user.name }}</td>
                      <td>
                        <small>{{ department.created_at }}</small>
                      </td>
                      <td>
                        <small>{{ department.updated_at }}</small>
                      </td>
                    </tr>
                    <tr v-show="departments.length == 0">
                      <td colspan="5" class="text-center">
                        <small class="col-red">
                          <i>No department found.</i>
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <hr />
            <p>{{ departments.length }} department displayed.</p>
          </div>
        </div>
      </div>

      <!--  STORE MODAL -->
      <div
        id="departmentModal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        data-keyboard="false"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="header">
              <div class="row clearfix">
                <div class="col-md-6">
                  <h2>
                    Department Details
                  </h2>
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
                <div class="col-md-12">
                  <span>Department Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="name"
                        name="name"
                        class="form-control"
                        v-validate="'required'"
                        v-model="departmentDetails.name"
                        autocomplete="off"
                        autofocus="on"
                        :disabled="!roles.admin"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('name')"
                      >Name is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12 col-sm-12">
                  <div class="input-group">
                    <span>Approver</span>
                    <div class="form-line">
                      <model-list-select
                        class="search-list"
                        :list="users"
                        option-value="id"
                        option-text="name"
                        name="approver"
                        v-model="departmentDetails.approver_id"
                        v-validate="'required'"
                        placeholder="Please select approver .."
                      >
                      </model-list-select>

                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('approver')"
                        >Approver is required.</small
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                class="btn btn-lg btn-info waves-effect waves-light"
                v-show="roles.admin"
                @click="create"
              >
                Create
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- MODAL -->
      <!--  UPDATE MODAL -->
      <div
        id="updateDepartmentModal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        data-keyboard="false"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="header">
              <div class="row clearfix">
                <div class="col-md-6">
                  <h2>
                    Department Details
                  </h2>
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
                <div class="col-md-12">
                  <span>Department Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="name"
                        name="name"
                        class="form-control"
                        v-validate="'required'"
                        v-model="departmentDetails.name"
                        autocomplete="off"
                        autofocus="on"
                        :disabled="!roles.admin"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('name')"
                      >Name is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12 col-sm-12">
                  <div class="input-group">
                    <span>Approver</span>
                    <div class="form-line">
                      <model-list-select
                        class="search-list"
                        :list="users"
                        option-value="id"
                        option-text="name"
                        name="approver"
                        v-model="departmentDetails.approver_id"
                        v-validate="'required'"
                        placeholder="Please select approver .."
                      >
                      </model-list-select>

                      <small
                        class="text-danger pull-left"
                        v-show="errors.has('approver')"
                        >Approver is required.</small
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                class="btn btn-lg btn-info waves-effect waves-light"
                v-show="roles.admin"
                @click="update"
              >
                Update
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- MODAL -->
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";
import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      authenticatedUser: [],
      roles: [],
      departments: [],
      users: [],
      departmentDetails: {
        name: "",
        approver_id: ""
      },
      approver: "",
      showLoading: false
    };
  },

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.users = this.$global.getUsers();
    this.departments = this.$global.getDepartment();
  },

  methods: {
    reset() {
      this.departmentDetails.name = "";
      this.departmentDetails.approver_id = "";
    },
    create() {
      console.log(this.departmentDetails);
      this.showLoading = true;
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/department", this.departmentDetails)
            .then(response => {
              console.log(response.body);
              swal(this.departmentDetails.name, "was successfully created!", {
                icon: "success"
              });

              this.$http.get("api/department").then(response => {
                this.$global.setDepartment(response.body);
                this.departments = response.body;
                this.showLoading = false;
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
                  this.$refs.name.focus();
                }
              });
            });
        }
      });
    },
    getDepartment(id) {
      this.departmentDetails = [];

      this.$http.get("api/department/" + id).then(response => {
        this.departmentDetails = response.body;
        console.log(response.body);
      });
    },
    update() {
      console.log(this.departmentDetails.id);
      this.showLoading = true;
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put(
              "api/department/" + this.departmentDetails.id,
              this.departmentDetails
            )
            .then(response => {
              console.log(response.body);
              this.departments = response.body;
              // this.$global.setDepartment(response.body);
              swal(this.departmentDetails.name, "was successfully updated!", {
                icon: "success"
              });
              this.showLoading = false;
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
    }
  }
};
</script>

<style scoped>
.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}
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
</style>
