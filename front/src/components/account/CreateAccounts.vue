<template>
  <div class="container-fluid">
    <div class="block-header">
      <button type="button" class="btn btn-default waves-effect" @click="exit">
        <i class="material-icons">keyboard_backspace</i>
        <span>Back</span>
      </button>
    </div>
    <div class="row clearfix">
      <div class="col-lg-6 col-md-6">
        <div class="card">
          <form @submit.prevent="create">
            <div class="header">
              <h2>Create New User</h2>
            </div>
            <div class="body">
              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="name"
                        name="name"
                        class="form-control"
                        v-validate="'required'"
                        v-model.trim="user.name"
                        autocomplete="off"
                        autofocus="on"
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
                <div class="col-md-12">
                  <span>Department</span>
                  <div class="input-group">
                    <div class="form-line">
                      <model-list-select
                        class="search-list"
                        :list="departments"
                        option-value="id"
                        option-text="name"
                        name="department"
                        v-model="user.department_id"
                        v-validate="'required'"
                        placeholder="Please select department .."
                      >
                      </model-list-select>
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('department')"
                      >Department is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Email</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="email"
                        name="email"
                        class="form-control"
                        v-validate="{ required: true, email: true }"
                        v-model.trim="user.email"
                        autocomplete="off"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('email')"
                      >Email is required.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Password</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        name="password"
                        type="password"
                        ref="password"
                        class="form-control"
                        v-validate="{ required: true, min: 8 }"
                        v-model.trim="user.password"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('password')"
                      >{{ errors.first("password") }}</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <span>Confirm Password</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="password"
                        name="confirm"
                        class="form-control"
                        v-validate="'required|confirmed:password'"
                        v-model.trim="user.confirm_password"
                      />
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('confirm')"
                      >The password confirmation does not match.</small
                    >
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-12">
                  <input
                    type="submit"
                    value="Create"
                    class="btn btn-lg btn-info waves-effect waves-light pull-right"
                  />
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
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
      user: {
        name: null,
        email: null,
        password: null,
        confirm_password: null,
        department_id: null
      },
      departments: []
    };
  },

  created() {
    this.authenticatedUser = this.$global.getUser();
    this.departments = this.$global.getDepartment();
  },
  methods: {
    create() {
      this.$validator.validateAll().then(result => {
        this.user.authenticatedUser = this.authenticatedUser;
        if (result) {
          this.$http
            .post("api/users", this.user)
            .then(response => {
              console.log(response.body);
              this.$global.setUsers(response.body.original);

              swal(this.user.name, "was successfully created!", {
                icon: "success"
              });

              this.$router.push({
                path: "/accounts"
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

    exit() {
      swal("Are you sure you want to go back?", {
        icon: "warning",
        buttons: {
          exit: "Yes",
          cancel: true
        }
      }).then(value => {
        switch (value) {
          case "exit":
            this.$router.push({
              path: "/accounts"
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
.search-list {
  background: none;
  border: none !important;
  border-bottom: 1px solid black !important;
  border-radius: 0 0 0 0 !important;
  box-shadow: none !important;
  width: 70%;
}
</style>
