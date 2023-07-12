<template>
  <div class="container" style>
    <div class="mx-auto col-md-12">
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">INET INFORMATION SYSTEM (BETA)</p>
        </div>
        <div class="elClr panel-body">
          <div class="rowFields mx-auto row">
            <!-- <div class="col-lg-3">
              <p class="textLabel">Email:</p>
            </div>-->
            <div class="col-lg-9 mx-auto">
              <b-input-group class>
                <b-input-group-text slot="append">
                  <i class="fas fa-envelope-square" style="color:black"></i>
                </b-input-group-text>
                <b-form-input
                  type="text"
                  name="email"
                  ref="email"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Input Email address"
                  placeholder="Email address"
                  v-validate="'required'"
                  v-model.trim="email"
                  autocomplete="off"
                  v-on:keyup.enter="login"
                />
              </b-input-group>

              <small class="text-danger pull-left" v-show="errors.has('email')"
                >Email is required.</small
              >
            </div>
          </div>
          <br />
          <div class="rowFields mx-auto row">
            <!-- <div class="col-lg-3">
              <p class="textLabel">Password:</p>
            </div>-->
            <div class="col-lg-9 mx-auto">
              <b-input-group class>
                <b-input-group-text slot="append">
                  <i class="fas fa-key" style="color:black"></i>
                </b-input-group-text>
                <b-form-input
                  type="password"
                  name="pass"
                  ref="pass"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Input Password"
                  placeholder="Password"
                  v-validate="'required'"
                  v-model.trim="password"
                  autocomplete="off"
                  v-on:keyup.enter="login"
                />
              </b-input-group>
              <small class="text-danger pull-left" v-show="errors.has('pass')"
                >Password is required.</small
              >
            </div>
          </div>
        </div>

        <div class="elClr panel-footer">
          <p class="pull-left" style="color: black;">Version 1.0.1</p>
          <div class="heading-elements">
            <button
              type="button"
              class="btn btn-success btn-labeled pull-right"
              v-on:click="login"
            >
              <b> <i class="glyphicon glyphicon-plus"></i> </b>Log in
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
//import inputGroup from "../../plugins/assets/js/pages/form_input_groups.js";

export default {
  data() {
    return {
      email: "",
      password: "",
      password2: ""
    };
  },
  mounted() {
    this.email = "";
    this.password = "";
  },
  methods: {
    login() {
      var subEmail = this.email;

      if (!subEmail.includes("@dctechmicro.com"))
        subEmail += "@dctechmicro.com";

      var data = {
        client_id: 2,
        client_secret: "USPkppmoHvLG0lUdrJrB6clewV70yJTYJTDIcfZ7",
        grant_type: "password",
        username: subEmail,
        password: this.password
      };

      this.$http
        .post("oauth/token", data)
        .then(function(response) {
          console.log(response.body);
          // this.$auth.setToken(
          //   response.body.access_token
          //   //response.body.expires_in + Date.now()
          // );
          // this.$global.setEmail(subEmail);
          // window.location.href = "/";
        })
        .catch(response => {
          console.log(response.body);
          this.$refs.email.focus();
          swal("Error", "You have entered incorrect credentials.", "error");
        });
    },
    test() {
      this.$http.get("http://localhost:8000/api/test").then(function(response) {
        console.log(response.body);
      });
    },
    login1() {
      var user = {
        name: "sample",
        email: "sample",
        password: "sample",
        password2: "sample"
      };
      this.$http
        .post("api/user", user)
        .then(response => {
          swal(this.user.name, "Added successfully", "success");
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
  }
};
</script>
<style scoped>
.textLabel {
  margin-top: 10px;
  font-size: 15px;
  color: black;
}
.panel-title {
  font-size: 25px;
  text-align: center;
}
.container {
  color: white;
  width: 480px;
  /* width: 40%; */
}
.panel {
  margin-top: 100px;
  background-color: white;
}

.panel-heading {
  color: white;
  font-family: "Patua One", cursive;
  background: linear-gradient(#076c2d, #2fd16d, #076c2d);
}
</style>
