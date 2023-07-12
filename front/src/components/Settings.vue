<template>
  <div class="container-fuild">
    <div class="row clearfix">
      <div class="col-lg-6 col-md-6">
        <div class="card">
          <form @submit.prevent="update">
            <div class="header">
              <h2>Profile Settings</h2>
            </div>
            <div class="body">
              <div class="row clearfix">
                <div class="col-sm-12">
                  <span>Name</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="text"
                        ref="name"
                        class="form-control"
                        v-validate="'required'"
                        v-model="user.name"
                        autocomplete="off"
                        autofocus="on"
                      >
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
                        type="email"
                        class="form-control"
                        v-validate="{ required: true, email: true }"
                        v-model="user.email"
                        autocomplete="off"
                      >
                    </div>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-12">
                  <span>Password</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        name="password"
                        type="password"
                        ref="password"
                        class="form-control"
                        v-model="user.password"
                        v-validate="{ required: false, min: 8 }"
                      >
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('password')"
                    >{{ errors.first('password') }}</small>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-sm-12">
                  <span>Confirm Password</span>
                  <div class="input-group">
                    <div class="form-line">
                      <input
                        type="password"
                        name="confirm"
                        class="form-control"
                        v-validate="'confirmed:password'"
                      >
                    </div>
                    <small
                      class="text-danger pull-left"
                      v-show="errors.has('confirm')"
                    >The password confirmation does not match.</small>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-6 col-md-offset-6">
                  <button class="btn btn-lg btn-info waves-effect pull-right">Save Changes</button>
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
export default {
  data() {
    return {
      user: []
    };
  },

  created() {
    this.user = this.$global.getUser();
  },

  methods: {
    update() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .put("api/users/" + this.user.id, this.user)
            .then(response => {
              this.$global.setUsers(response.body.original);
              this.users = this.$global.getUsers();

              $("#userModal").modal("hide");
              swal(this.user.name, "was successfully updated!", {
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
    }
  }
};
</script>

<style scoped>
textarea {
  resize: none;
}

i {
  font-size: 26px;
  vertical-align: middle;
  margin-bottom: 4px;
}
</style>
